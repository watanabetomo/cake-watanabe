<?php
class CartModel extends Model
{
    /**
     * カートに商品を追加
     *
     * @param int $userId
     * @param int $detailId
     * @return void
     */
    public function addToCart($userId, $detailId)
    {
        $cart = $this->fetchAll();
        foreach ($cart as $prodOfTheCart) {
            if ($prodOfTheCart['product_detail_id'] == $detailId) {
                $this->addNum($prodOfTheCart['product_detail_id']);
                $idExist = true;
            }
        }
        if (!isset($idExist)) {
            $stmt = $this->dbh->prepare('INSERT INTO cart(user_id, product_detail_id, num) VALUES(?, ?, 1)');
            $stmt->execute([$userId, $detailId]);
        }
    }

    /**
     * カートの中身を全件取得
     *
     * @return array cartの全件
     */
    public function fetchAll()
    {
        $stmt = $this->dbh->query('SELECT * FROM cart');
        return $stmt->fetchAll();
    }

    /**
     * カートから特定の商品を削除
     *
     * @param int $id
     * @return void
     */
    public function delete($id)
    {
        $stmt = $this->dbh->prepare('DELETE FROM cart WHERE id = ?');
        $stmt->execute([$id]);
    }

    /**
     * 商品の数を変更
     *
     * @param int $num
     * @param int $id
     * @return void
     */
    public function changeNum($num, $id)
    {
        $stmt = $this->dbh->prepare('UPDATE cart SET num = ? WHERE id = ?');
        $stmt->execute([$num, $id]);
    }

    /**
     * numに1を加算する
     *
     * @param int $id
     * @return void
     */
    public function addNum($id)
    {
        $stmt = $this->dbh->prepare('SELECT num FROM cart WHERE product_detail_id = ?');
        $stmt->execute([$id]);
        $num = $stmt->fetch();
        $stmt = $this->dbh->prepare('UPDATE cart SET num = ? WHERE product_detail_id = ?');
        $stmt->execute([$num['num'] + 1, $id]);
    }

    /**
     * カートの中を全削除
     *
     * @return void
     */
    public function deleteFromCart()
    {
        $this->dbh->query('DELETE FROM cart');
    }

    /**
     * cartを空にし、order, order_detailに注文情報を登録する。メールの送信をする。
     *
     * @param array $prefectures
     * @return void
     */
    public function purchaseComplete($prefectures)
    {
        try {
            $orderModel = new OrderModel();
            $productDetailModel = new ProductDetailModel();
            $productModel = new ProductModel();
            $oederDetailModel = new OrderDetailModel();
            $userModel = new UserModel();
            $cart = $this->fetchAll();
            $this->dbh->exec('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED');
            $this->dbh->beginTransaction();
            $orderModel->commitOrder(
                $_SESSION['userId'],
                $_SESSION['purchase_info']['name'],
                $_SESSION['purchase_info']['name_kana'],
                $_SESSION['purchase_info']['mail'],
                $_SESSION['purchase_info']['tel1'],
                $_SESSION['purchase_info']['tel2'],
                $_SESSION['purchase_info']['tel3'],
                $_SESSION['purchase_info']['postal_code1'],
                $_SESSION['purchase_info']['postal_code2'],
                array_search($_SESSION['purchase_info']['pref'], $prefectures),
                $_SESSION['purchase_info']['city'],
                $_SESSION['purchase_info']['address'],
                $_SESSION['purchase_info']['other'],
                $_SESSION['purchase_info']['payment'],
                $_SESSION['purchase_info']['sub_price'],
                $_SESSION['purchase_info']['shipping'],
                TAX * 100,
                $_SESSION['purchase_info']['total_price'],
                $this->dbh
            );
            $user = $userModel->fetchById($_SESSION['userId']);
            foreach ($cart as $prodOfTheCart) {
                $productDetail = $productDetailModel->fetchById($prodOfTheCart['product_detail_id']);
                $product = $productModel->fetchById($productDetail['product_id']);
                $oederDetailModel->registOrderDetail(
                    $orderModel->getMaxId()[0],
                    $prodOfTheCart['product_detail_id'],
                    $product[0]['name'],
                    $productDetail['size'],
                    $productDetail['price'],
                    $prodOfTheCart['num'],
                    $this->dbh
                );
            }
            $this->deleteFromCart();
            $mailBody =
                '<p>' . $_SESSION['userName'] . '様</p>
                <p>お世話になっております。<br>洋菓子店カサミンゴーカスタマーサポートです。</p>
                <p>
                    ' . $_SESSION['userName'] . ' 様が購入手続きをされました商品について<br>お間違えのないようメールをお送りいたしました。<br>
                    今一度ご購入商品等にお間違えなどないよう、ご確認いただけましたら幸いでございます。
                </p>
                <p>--------------------------------------</p>
                <p>【購入情報】</p>';
            foreach ($cart as $prodOfTheCart) {
                $productDetail = $productDetailModel->fetchById($prodOfTheCart['product_detail_id']);
                $product = $productModel->fetchById($productDetail['product_id']);
                $mailBody .=
                    '<p>' . $product[0]['name'] . '<br>' . $productDetail['size'] . 'cm<br>
                    ' . $productDetail['price'] . '円<br>
                    ' . $prodOfTheCart['num'] . '枚</p>
                    <p>-----------------------</p>';
            }
            $mailBody .=
                '<p>小計： ' . $_SESSION['purchase_info']['sub_price'] . '<br>
                消費税： ' . floor($_SESSION['purchase_info']['sub_price'] * TAX) . '<br>
                送料： ' . $_SESSION['purchase_info']['shipping'] . '<br>
                合計： ' . $_SESSION['purchase_info']['total_price'] . '</p>
                <p>--------------------------------------</p>
                <p>【送付先情報】</p>
                <p>お名前： ' . $_SESSION['purchase_info']['name'] . '<br>
                フリガナ： ' . $_SESSION['purchase_info']['name_kana'] . '<br>
                電話番号： ' . $_SESSION['purchase_info']['tel1'] . ' - ' . $_SESSION['purchase_info']['tel2'] . ' - ' . $_SESSION['purchase_info']['tel3'] . '<br>
                郵便番号： ' . $_SESSION['purchase_info']['postal_code1'] . ' - ' . $_SESSION['purchase_info']['postal_code2'] . '<br>
                都道府県： ' . $_SESSION['purchase_info']['pref'] . '<br>
                市区町村： ' . $_SESSION['purchase_info']['city'] . '<br>
                番地： ' . $_SESSION['purchase_info']['address'] . '<br>
                マンション名等： ' . $_SESSION['purchase_info']['other'] . '</p>
                <p>--------------------------------------</p>
                <p>【請求先情報】</p>
                <p>お名前： ' . $user['name'] . '<br>
                フリガナ： ' . $user['name_kana'] . '<br>
                電話番号： ' . $user['tel1'] . ' - ' . $user['tel2'] . ' - ' . $user['tel3'] . '<br>
                郵便番号： ' . $user['postal_code1'] . ' - ' . $user['postal_code2'] . '<br>
                都道府県： ' . $prefectures[$user['pref']] . '<br>
                市区町村： ' . $user['city'] . '<br>
                番地： ' . $user['address'] . '<br>
                マンション名等： ' . $user['other'] . '</p>
                <p>--------------------------------------</p>
                <p>商品ご到着まで。今しばらくお待ちください。</p>
                <p>※このメールは自動送信メールです。<br>※返信をされてもご回答しかねますのでご了承ください。</p>';
            $from = 'From:' . mb_encode_mimeheader('洋菓子店カサミンゴー');
            $from .= "\r\n";
            $from .= 'Content-type: text/html; charset=UTF-8';
            mb_language('uni');
            mb_internal_encoding('UTF-8');
            if (!mb_send_mail(
                $_SESSION['purchase_info']['mail'],
                '【洋菓子店カサミンゴー】ご購入商品確認メール',
                $mailBody,
                $from
            )) {
                throw new Exception;
            }
            $this->dbh->commit();
        } catch (Exception $e) {
            $this->dbh->rollBack();
            throw new Exception($e);
        }
    }
}