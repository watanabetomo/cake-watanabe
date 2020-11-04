<?php
require_once('admin/autoload.php');

if ((isset($_SESSION['token']) ? $_SESSION['token'] : '') != getToken()) {
    header('Location: purchase_edit.php');
    exit;
}

try {
    $cartModel = new CartModel();
    $cart = $cartModel->fetchAll();
    $orderModel = new OrderModel();
    $productDetailModel = new ProductDetailModel();
    $productModel = new ProductModel();
    $orderModel->commitOrder($_SESSION['userId'], $_SESSION['name'], $_SESSION['name_kana'], $_SESSION['mail'], $_SESSION['tel1'], $_SESSION['tel2'], $_SESSION['tel3'], $_SESSION['postal_code1'], $_SESSION['postal_code2'], array_search($_SESSION['pref'], $prefectures), $_SESSION['city'], $_SESSION['address'], $_SESSION['other'], $_SESSION['payment'], $_SESSION['sub_price'], $_SESSION['shipping'], ($_SESSION['tax'] * 100), $_SESSION['total_price']);
    $oederDetailModel = new OrderDetailModel();
    foreach ($cart as $onCart) {
        $productDetail = $productDetailModel->fetchById($onCart['product_detail_id']);
        $product = $productModel->fetchById($productDetail['product_id']);
        $oederDetailModel->registOrderDetail($orderModel->getMaxId()[0], $onCart['product_detail_id'], $product[0]['name'], $productDetail['size'], $productDetail['price'], $onCart['num']);
    }
    $cartModel->truncateCart();
    try {
        $name = $_SESSION['userName'];
        mb_language("Japanese");
        mb_internal_encoding("UTF-8");
$mailBody = <<<EOT
$name 様
EOT;
        $mail = mb_send_mail('t.watanabe@ebacorp.jp', '【洋菓子店カサミンゴー】ご購入商品確認メール', $mailBody, 'From: chelseano55@gmail.com');
        unset($_SESSION['postal_code1']);
        unset($_SESSION['postal_code2']);
        unset($_SESSION['pref']);
        unset($_SESSION['city']);
        unset($_SESSION['address']);
        unset($_SESSION['other']);
        unset($_SESSION['tel1']);
        unset($_SESSION['tel2']);
        unset($_SESSION['tel3']);
        unset($_SESSION['name_kana']);
        unset($_SESSION['name']);
        unset($_SESSION['payment']);
        unset($_SESSION['token']);
        unset($_SESSION['sub_price']);
        unset($_SESSION['shipping']);
        unset($_SESSION['total_price']);
        unset($_SESSION['tax']);
    } catch (Exception $e) {
        $error = "メールの送信に失敗しました";
    }
} catch (PDOException $e) {
    $error = 'データベースに接続できませんでした';
}


?>
<?php require_once('header.html') ?>
<main>
    <?php if (isset($error)): ?>
        <p class="error"><?=$error?></p>
    <?php endif; ?>
    <p class="done-message">購入が完了しました。ご利用ありがとうございました。</p>
</main>
<?php require_once('footer.html') ?>