<?php
class CartModel extends Model{
    /**
     * カートに商品を追加
     *
     * @param int $userId
     * @param int $detailId
     * @return void
     */
    public function addToCart($userId, $detailId){
        $this->connect();
        $stmt = $this->dbh->prepare('INSERT INTO cart(user_id, product_detail_id, num) VALUES(?, ?, 1)');
        $stmt->execute([$userId, $detailId]);
    }

    /**
     * カートの中身を全件取得
     *
     * @return array cartの全件
     */
    public function fetchAll()
    {
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
    public function delete($id)
    {
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
    public function changeNum($num, $id)
    {
        $this->connect();
        $stmt = $this->dbh->prepare('UPDATE cart SET num = ? WHERE id = ?');
        $stmt->execute([$num, $id]);
    }

    /**
     * カートの中を全削除
     *
     * @return void
     */
    public function truncateCart()
    {
        $this->connect();
        $this->dbh->query('TRUNCATE TABLE cart');
    }

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
            $this->dbh->beginTrunsaction();
            $orderModel->commitOrder($_SESSION['userId'], $_SESSION['name'], $_SESSION['name_kana'], $_SESSION['mail'], $_SESSION['tel1'], $_SESSION['tel2'], $_SESSION['tel3'], $_SESSION['postal_code1'], $_SESSION['postal_code2'], array_search($_SESSION['pref'], $prefectures), $_SESSION['city'], $_SESSION['address'], $_SESSION['other'], $_SESSION['payment'], $_SESSION['sub_price'], $_SESSION['shipping'], ($_SESSION['tax'] * 100), $_SESSION['total_price']);
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
$name 様
お世話になっております。
洋菓子店カサミンゴーカスタマーサポートです。

$name 様が購入手続きをされました商品について
お間違えのないようメールをお送りいたしました。
今一度ご購入商品等にお間違えなどないよう、ご確認いただけましたら幸いでございます。
--------------------------------------
【購入情報】
EOT;
            foreach($cart as $onCart){
                $productDetail = $productDetailModel->fetchById($onCart['product_detail_id']);
                $product = $productModel->fetchById($productDetail['product_id']);
                $productName = $product[0]['name'];
                $size = $productDetail['size'];
                $price = $productDetail['price'];
                $count = $onCart['num'];
$mailBody .= <<<EOT
$productName
$size cm
$price 円
$count 枚
-----------------------
EOT;
            }
            $postal_code1 = $_SESSION['postal_code1'];
            $postal_code2 = $_SESSION['postal_code2'];
            $pref = $_SESSION['pref'];
            $city = $_SESSION['city'];
            $address = $_SESSION['address'];
            $other = $_SESSION['other'];
            $tel1 = $_SESSION['tel1'];
            $tel2 = $_SESSION['tel2'];
            $tel3 = $_SESSION['tel3'];
            $name_kana = $_SESSION['name_kana'];
            $name = $_SESSION['name'];
            $sub_price = $_SESSION['sub_price'];
            $shipping = $_SESSION['shipping'];
            $total_price = $_SESSION['total_price'];
            $tax_price = $sub_price * (1 + TAX);
$mailBody .= <<<EOT
小計： $sub_price
消費税： $tax_price
送料： $shipping
合計： $total_price
--------------------------------------
【送付先情報】
お名前： $name
フリガナ： $name_kana
電話番号： $tel1 - $tel2 - $tel3
郵便番号： $postal_code1 - $postal_code2
都道府県： $pref
市区町村： $city
番地： $address
マンション名等： $other
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
【請求先情報】
お名前： $name
フリガナ： $name_kana
電話番号： $tel1 - $tel2 - $tel3
郵便番号： $postal_code1 - $postal_code2
都道府県： $pref
市区町村： $city
番地： $address
マンション名等： $other
--------------------------------------
商品ご到着まで。今しばらくお待ちください。
EOT;
            mb_send_mail('t.watanabe@ebacorp.jp', '【洋菓子店カサミンゴー】ご購入商品確認メール', $mailBody, 'From: 洋菓子店カサミンゴー');
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