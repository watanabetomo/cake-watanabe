<?php
require_once('autoload.php');

if (!isset($_SESSION['authenticated'])) {
    header('Location: login.php');
    exit;
}

if ((isset($_SESSION['purchase_info']['token']) ? $_SESSION['purchase_info']['token'] : '') != getToken()) {
    header('Location: purchase_edit.php');
    exit;
}

try {
    $cartModel = new CartModel();
    $cartModel->purchaseComplete($prefectures);
    unset($_SESSION['purchase_info']);
} catch (Exception $e) {
    $error = '商品情報の取得及び登録に失敗しました。<br>カスタマーサポートにお問い合わせください。';
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