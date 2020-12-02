<?php
require_once('autoload.php');

if (!isset($_SESSION['user']['authenticated'])) {
    header('Location: login.php');
    exit;
}

if ((
    isset($_SESSION['purchase_info']['token']) ? $_SESSION['purchase_info']['token'] : '') != getToken()
    and !isset($_POST['send'])
) {
    header('Location: cart.php');
    exit;
}

try {
    $cartModel = new CartModel();
    $cartModel->completePurchase($_POST);
} catch (Exception $e) {
    $error = '商品の購入に失敗しました。<br>カスタマーサポートにお問い合わせください。';
}

?>
<?php require_once('header.html')?>
<main>
    <?php if (isset($error)) :?>
        <p class="error done-message"><?=$error?></p>
    <?php else:?>
        <p class="done-message">購入が完了しました。ご利用ありがとうございました。</p>
    <?php endif;?>
</main>
<?php require_once('footer.html')?>