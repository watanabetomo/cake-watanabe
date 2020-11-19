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
            $sql =
                'INSERT '
                . 'INTO '
                    . 'cart '
                . '('
                    . 'user_id'
                . ',    product_detail_id'
                . ',    num'
                . ') VALUES ('
                    . '?'
                . ',    ?'
                . ',    1'
                . ')';
            $stmt = $this->dbh->prepare($sql);
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
        $sql =
            'SELECT '
                . '* '
            . 'FROM '
                . 'cart';
        $stmt = $this->dbh->query($sql);
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
        $sql =
            'DELETE FROM '
                . 'cart '
            . 'WHERE '
                . 'id = ?';
        $stmt = $this->dbh->prepare($sql);
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
        $sql =
            'UPDATE '
                . 'cart '
            . 'SET '
                . 'num = ? '
            . 'WHERE '
                . 'id = ?';
        $stmt = $this->dbh->prepare($sql);
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
        $sql =
            'SELECT '
                . 'num '
            . 'FROM '
                . 'cart '
            . 'WHERE '
                . 'product_detail_id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$id]);
        $num = $stmt->fetch();
        $sql =
            'UPDATE '
                . 'cart '
            . 'SET '
                . 'num = ? '
            . 'WHERE '
                . 'product_detail_id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$num['num'] + 1, $id]);
    }

    /**
     * カートの中を全削除
     *
     * @return void
     */
    public function deleteFromCart()
    {
        $sql =
            'DELETE FROM '
                . 'cart';
        $this->dbh->query($sql);
    }

    /**
     * cartを空にし、order, order_detailに注文情報を登録する。メールの送信をする。
     *
     * @return void
     */
    public function purchaseComplete()
    {
        global $prefectures;
        try {
            $cart = $this->fetchAll();
            $this->dbh->exec('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED');
            $this->dbh->beginTransaction();
            $orderModel = new OrderModel();
            $orderModel->commitOrder(
                $_SESSION['user']['userId'],
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
                $_SESSION['purchase_info']['shipping'], TAX * 100,
                $_SESSION['purchase_info']['total_price'], $this->dbh
            );
            $userModel = new UserModel();
            $user = $userModel->fetchById($_SESSION['user']['userId']);
            $id = $orderModel->getMaxId();
            $productDetailModel = new ProductDetailModel();
            $productModel = new ProductModel();
            $oederDetailModel = new OrderDetailModel();
            foreach ($cart as $prodOfTheCart) {
                $productDetail = $productDetailModel->fetchById($prodOfTheCart['product_detail_id']);
                $product = $productModel->fetchSingleProduct($productDetail['product_id']);
                $oederDetailModel->registOrderDetail(
                    $id,
                    $prodOfTheCart['product_detail_id'],
                    $product['name'],
                    $productDetail['size'],
                    $productDetail['price'],
                    $prodOfTheCart['num'],
                    $this->dbh
                );
            }
            $this->deleteFromCart();
            $mailBody =
                $_SESSION['user']['userName'] . "様\n\n"
                . "お世話になっております。\n"
                . "洋菓子店カサミンゴーカスタマーサポートです。\n\n"
                . $_SESSION['user']['userName'] . "様が購入手続きをされました商品について\n"
                . "お間違えのないようメールをお送りいたしました。\n"
                . "今一度ご購入商品等にお間違えなどないよう、ご確認いただけましたら幸いでございます。\n\n"
                . "--------------------------------------\n\n"
                . "【購入情報】\n\n";
            foreach ($cart as $prodOfTheCart) {
                $productDetail = $productDetailModel->fetchById($prodOfTheCart['product_detail_id']);
                $product = $productModel->fetchSingleProduct($productDetail['product_id']);
                $mailBody .=
                    $product['name'] . "\n"
                    . $productDetail['size'] . "cm\n"
                    . $productDetail['price'] . "円\n"
                    . $prodOfTheCart['num'] . "枚\n\n"
                    . "-----------------------\n\n";
            }
            $mailBody .=
                '小計： ' . $_SESSION['purchase_info']['sub_price'] . "円\n"
                . '消費税： ' . floor($_SESSION['purchase_info']['sub_price'] * TAX) . "円\n"
                . '送料： ' . $_SESSION['purchase_info']['shipping'] . "円\n"
                . '合計： ' . $_SESSION['purchase_info']['total_price'] . "円\n\n"
                . "--------------------------------------\n\n"
                . "【送付先情報】\n\n"
                . 'お名前： ' . $_SESSION['purchase_info']['name'] . "\n"
                . 'フリガナ： ' . $_SESSION['purchase_info']['name_kana'] . "\n"
                . '電話番号： ' . $_SESSION['purchase_info']['tel1'] . ' - ' . $_SESSION['purchase_info']['tel2'] . ' - ' . $_SESSION['purchase_info']['tel3'] . "\n"
                . '郵便番号： ' . $_SESSION['purchase_info']['postal_code1'] . ' - ' . $_SESSION['purchase_info']['postal_code2'] . "\n"
                . '都道府県： ' . $_SESSION['purchase_info']['pref'] . "\n"
                . '市区町村： ' . $_SESSION['purchase_info']['city'] . "\n"
                . '番地： ' . $_SESSION['purchase_info']['address'] . "\n"
                . 'マンション名等： ' . $_SESSION['purchase_info']['other'] . "\n\n"
                . "--------------------------------------\n\n"
                . "【請求先情報】\n\n"
                . 'お名前： ' . $user['name'] . "\n"
                . 'フリガナ： ' . $user['name_kana'] . "\n"
                . '電話番号： ' . $user['tel1'] . ' - ' . $user['tel2'] . ' - ' . $user['tel3'] . "\n"
                . '郵便番号： ' . $user['postal_code1'] . ' - ' . $user['postal_code2'] . "\n"
                . '都道府県： ' . $prefectures[$user['pref']] . "\n"
                . '市区町村： ' . $user['city'] . "\n"
                . '番地： ' . $user['address'] . "\n"
                . 'マンション名等： ' . $user['other'] . "\n\n"
                . "--------------------------------------\n\n"
                . "商品ご到着まで。今しばらくお待ちください。\n\n"
                . "※このメールは自動送信メールです。\n"
                . "※返信をされてもご回答しかねますのでご了承ください。\n\n"
                . "〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜\n"
                . "洋菓子店カサミンゴー\n"
                . "TEL：000-0000-0000\n"
                . "住所：福島県郡山市中ノ目3-149-12\n"
                . "mail：t.watanabe@ebacorp.jp\n"
                . '〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜';
            mb_language('uni');
            mb_internal_encoding('UTF-8');
            if (!mb_send_mail(
                $_SESSION['purchase_info']['mail'],
                '【洋菓子店カサミンゴー】ご購入商品確認メール',
                $mailBody,
                'From:' . mb_encode_mimeheader('洋菓子店カサミンゴー')
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