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
        $this->connect();
        $cart = $this->fetchAll();
        foreach($cart as $onCart) {
            if($onCart['product_detail_id'] == $detailId) {
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
    public function fetchAll() {
        $this->connect();
        $stmt = $this->dbh->query('SELECT * FROM cart');
        return $stmt->fetchAll();
    }

    /**
     * カートから特定の商品を削除
     *
     * @param int $id
     * @return void
     */
    public function delete($id) {
        $this->connect();
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
    public function changeNum($num, $id) {
        $this->connect();
        $stmt = $this->dbh->prepare('UPDATE cart SET num = ? WHERE id = ?');
        $stmt->execute([$num, $id]);
    }

    /**
     * カートの中を全削除
     *
     * @return void
     */
    public function truncateCart() {
        $this->connect();
        $this->dbh->query('TRUNCATE TABLE cart');
    }

    public function purchaseComplete($prefectures) {
        try {
            $orderModel = new OrderModel();
            $productDetailModel = new ProductDetailModel();
            $productModel = new ProductModel();
            $oederDetailModel = new OrderDetailModel();
            $userModel = new UserModel();
            $cart = $this->fetchAll();
            $this->dbh->exec('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED');
            $this->dbh->beginTrunsaction();
            $orderModel->commitOrder($_SESSION['userId'], $_SESSION['purchase_info']['name'], $_SESSION['purchase_info']['name_kana'], $_SESSION['purchase_info']['mail'], $_SESSION['purchase_info']['tel1'], $_SESSION['purchase_info']['tel2'], $_SESSION['purchase_info']['tel3'], $_SESSION['purchase_info']['postal_code1'], $_SESSION['purchase_info']['postal_code2'], array_search($_SESSION['purchase_info']['pref'], $prefectures), $_SESSION['purchase_info']['city'], $_SESSION['purchase_info']['address'], $_SESSION['purchase_info']['other'], $_SESSION['purchase_info']['payment'], $_SESSION['purchase_info']['sub_price'], $_SESSION['purchase_info']['shipping'], ($_SESSION['purchase_info']['tax'] * 100), $_SESSION['purchase_info']['total_price']);
            $user = $userModel->fetchById($_SESSION['userId']);
            foreach ($cart as $onCart) {
                $productDetail = $productDetailModel->fetchById($onCart['product_detail_id']);
                $product = $productModel->fetchById($productDetail['product_id']);
                $oederDetailModel->registOrderDetail($orderModel->getMaxId()[0], $onCart['product_detail_id'], $product[0]['name'], $productDetail['size'], $productDetail['price'], $onCart['num']);
            }
            $this->truncateCart();
            $name = $_SESSION['userName'];
            mb_language("Japanese");
            mb_internal_encoding("UTF-8");
$mailBody = <<<EOT
<p>$name 様</p>
<p>お世話になっております。<br>洋菓子店カサミンゴーカスタマーサポートです。</p>


<p>$name 様が購入手続きをされました商品について<br>お間違えのないようメールをお送りいたしました。<br>今一度ご購入商品等にお間違えなどないよう、ご確認いただけましたら幸いでございます。</p>
<p>--------------------------------------</p>
<h2>【購入情報】</h2>
EOT;
            foreach($cart as $onCart) {
                $productDetail = $productDetailModel->fetchById($onCart['product_detail_id']);
                $product = $productModel->fetchById($productDetail['product_id']);
                $productName = $product[0]['name'];
                $size = $productDetail['size'];
                $price = $productDetail['price'];
                $count = $onCart['num'];
$mailBody .= <<<EOT
<p>$productName $size cm<br>
$price 円<br>
$count 枚</p>
<p>-----------------------</p>
EOT;
            }
            $postal_code1 = $_SESSION['purchase_info']['postal_code1'];
            $postal_code2 = $_SESSION['purchase_info']['postal_code2'];
            $pref = $_SESSION['purchase_info']['pref'];
            $city = $_SESSION['purchase_info']['city'];
            $address = $_SESSION['purchase_info']['address'];
            $other = $_SESSION['purchase_info']['other'];
            $tel1 = $_SESSION['purchase_info']['tel1'];
            $tel2 = $_SESSION['purchase_info']['tel2'];
            $tel3 = $_SESSION['purchase_info']['tel3'];
            $name_kana = $_SESSION['purchase_info']['name_kana'];
            $name = $_SESSION['purchase_info']['name'];
            $sub_price = $_SESSION['purchase_info']['sub_price'];
            $shipping = $_SESSION['purchase_info']['shipping'];
            $total_price = $_SESSION['purchase_info']['total_price'];
            $tax_price = $sub_price * (1 + TAX);
$mailBody .= <<<EOT
<p>小計： $sub_price<br>
消費税： $tax_price<br>
送料： $shipping<br>
合計： $total_price</p>
<p>--------------------------------------</p>
<h2>【送付先情報】</h2>
<p>お名前： $name<br>
フリガナ： $name_kana<br>
電話番号： $tel1 - $tel2 - $tel3<br>
郵便番号： $postal_code1 - $postal_code2<br>
都道府県： $pref<br>
市区町村： $city<br>
番地： $address<br>
マンション名等： $other</p>
--------------------------------------
EOT;
            $postal_code1 = $user['postal_code1'];
            $postal_code2 = $user['postal_code2'];
            $pref = $prefectures[$user['pref']];
            $city = $user['city'];
            $address = $user['address'];
            $other = $user['other'];
            $tel1 = $user['tel1'];
            $tel2 = $user['tel2'];
            $tel3 = $user['tel3'];
            $name_kana = $user['name_kana'];
            $name = $user['name'];
$mailBody .= <<<EOT
<h2>【請求先情報】</h2>
<>お名前： $name<br>
フリガナ： $name_kana<br>
電話番号： $tel1 - $tel2 - $tel3<br>
郵便番号： $postal_code1 - $postal_code2<br>
都道府県： $pref<br>
市区町村： $city<br>
番地： $address<br>
マンション名等： $other</p>
<p>--------------------------------------</p>
<p>商品ご到着まで。今しばらくお待ちください。</p>

<p>※このメールは自動送信メールです。<br>※返信をされてもご回答しかねますのでご了承ください。</p>
EOT;
            mb_send_mail(MAIL_TO, '【洋菓子店カサミンゴー】ご購入商品確認メール', $mailBody, "From: 洋菓子店カサミンゴー\r\nContent-type: text/html; charset=UTF-8");
            $this->dbh->commit();
        } catch (PDOException $e) {
            throw new PDOException($e);
            $this->dbh->rollback();
        } catch (Exception $e) {
            throw new Exception($e);
            $this->dbh->rollback();
        }
    }
}