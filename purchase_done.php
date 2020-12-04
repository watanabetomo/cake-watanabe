<?php
require_once('autoload.php');

if (!isset($_SESSION['user']['authenticated'])) {
    header('Location: login.php');
    exit;
}

if (
    !isset($_POST['token'])
    or $_POST['token'] != $_SESSION['token']
) {
    header('Location: error.php?error=csrf');
    exit;
}

try {
    $orderModel = new OrderModel();
    $orderModel->completePurchase($_POST);
    unset($_SESSION['token']);
} catch (Exception $e) {
    header('Location: error.php?error=database');
    exit;
}

?>
<?php require_once('header.html')?>
<main>
    <p class="done-message">購入が完了しました。ご利用ありがとうございました。</p>
</main>
<?php require_once('footer.html')?>