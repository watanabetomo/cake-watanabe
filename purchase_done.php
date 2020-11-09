<?php
require_once('autoload.php');

if (!isset($_SESSION['authenticated'])) {
    header('Location: login.php');
    exit;
}

if ((isset($_SESSION['token']) ? $_SESSION['token'] : '') != getToken()) {
    header('Location: purchase_edit.php');
    exit;
}

try {
    $cartModel = new CartModel();
    $cartModel->purchaseComplete($prefectures);
//     $cart = $cartModel->fetchAll();
//     $orderModel = new OrderModel();
//     $productDetailModel = new ProductDetailModel();
//     $productModel = new ProductModel();
//     $orderModel->commitOrder($_SESSION['userId'], $_SESSION['name'], $_SESSION['name_kana'], $_SESSION['mail'], $_SESSION['tel1'], $_SESSION['tel2'], $_SESSION['tel3'], $_SESSION['postal_code1'], $_SESSION['postal_code2'], array_search($_SESSION['pref'], $prefectures), $_SESSION['city'], $_SESSION['address'], $_SESSION['other'], $_SESSION['payment'], $_SESSION['sub_price'], $_SESSION['shipping'], ($_SESSION['tax'] * 100), $_SESSION['total_price']);
//     $oederDetailModel = new OrderDetailModel();
//     $userModel = new UserModel();
//     $user = $userModel->fetchById($_SESSION['userId']);
//     foreach ($cart as $onCart) {
//         $productDetail = $productDetailModel->fetchById($onCart['product_detail_id']);
//         $product = $productModel->fetchById($productDetail['product_id']);
//         $oederDetailModel->registOrderDetail($orderModel->getMaxId()[0], $onCart['product_detail_id'], $product[0]['name'], $productDetail['size'], $productDetail['price'], $onCart['num']);
//     }
//     $cartModel->truncateCart();
//     $name = $_SESSION['userName'];
//     mb_language("Japanese");
//     mb_internal_encoding("UTF-8");
// $mailBody = <<<EOT
// $name 様
// お世話になっております。
// 洋菓子店カサミンゴーカスタマーサポートです。

// $name 様が購入手続きをされました商品について
// お間違えのないようメールをお送りいたしました。
// 今一度ご購入商品等にお間違えなどないよう、ご確認いただけましたら幸いでございます。
// --------------------------------------
// 【購入情報】
// EOT;
//     foreach($cart as $onCart){
//         $productDetail = $productDetailModel->fetchById($onCart['product_detail_id']);
//         $product = $productModel->fetchById($productDetail['product_id']);
//         $productName = $product[0]['name'];
//         $size = $productDetail['size'];
//         $price = $productDetail['price'];
//         $count = $onCart['num'];
// $mailBody .= <<<EOT
// $productName
// $size cm
// $price 円
// $count 枚
// -----------------------
// EOT;
//     }
//     $postal_code1 = $_SESSION['postal_code1'];
//     $postal_code2 = $_SESSION['postal_code2'];
//     $pref = $_SESSION['pref'];
//     $city = $_SESSION['city'];
//     $address = $_SESSION['address'];
//     $other = $_SESSION['other'];
//     $tel1 = $_SESSION['tel1'];
//     $tel2 = $_SESSION['tel2'];
//     $tel3 = $_SESSION['tel3'];
//     $name_kana = $_SESSION['name_kana'];
//     $name = $_SESSION['name'];
//     $sub_price = $_SESSION['sub_price'];
//     $shipping = $_SESSION['shipping'];
//     $total_price = $_SESSION['total_price'];
//     $tax_price = $sub_price * (1 + TAX);
// $mailBody .= <<<EOT
// 小計： $sub_price
// 消費税： $tax_price
// 送料： $shipping
// 合計： $total_price
// --------------------------------------
// 【送付先情報】
// お名前： $name
// フリガナ： $name_kana
// 電話番号： $tel1 - $tel2 - $tel3
// 郵便番号： $postal_code1 - $postal_code2
// 都道府県： $pref
// 市区町村： $city
// 番地： $address
// マンション名等： $other
// --------------------------------------
// EOT;
//     $postal_code1 = $user['postal_code1'];
//     $postal_code2 = $user['postal_code2'];
//     $pref = $prefectures[$user['pref']];
//     $city = $user['city'];
//     $address = $user['address'];
//     $other = $user['other'];
//     $tel1 = $user['tel1'];
//     $tel2 = $user['tel2'];
//     $tel3 = $user['tel3'];
//     $name_kana = $user['name_kana'];
//     $name = $user['name'];
// $mailBody .= <<<EOT
// 【請求先情報】
// お名前： $name
// フリガナ： $name_kana
// 電話番号： $tel1 - $tel2 - $tel3
// 郵便番号： $postal_code1 - $postal_code2
// 都道府県： $pref
// 市区町村： $city
// 番地： $address
// マンション名等： $other
// --------------------------------------
// 商品ご到着まで。今しばらくお待ちください。
// EOT;
//     mb_send_mail('t.watanabe@ebacorp.jp', '【洋菓子店カサミンゴー】ご購入商品確認メール', $mailBody, 'From: 洋菓子店カサミンゴー');
    unset($_SESSION['purchase_info']);
} catch (PDOException $e) {
    $error = 'データベースに接続できませんでした';
} catch (Exception $e) {
    $error = "メールの送信に失敗しました";
}


?>
<?php require_once('header.html') ?>
<main>
    <p class="error"><?=isset($error) ? $error : ''?></p>
    <p class="done-message">購入が完了しました。ご利用ありがとうございました。</p>
</main>
<?php require_once('footer.html') ?>