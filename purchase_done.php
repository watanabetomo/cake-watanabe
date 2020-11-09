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