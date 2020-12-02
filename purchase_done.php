<?php
require_once('autoload.php');

if (!isset($_SESSION['user']['authenticated'])) {
    header('Location: login.php');
    exit;
}

if ((isset($_POST['token']) ? $_POST['token'] : '') != $_SESSION['token']) {
    header('Location: error.php?error=param');
    exit;
}

try {
    $orderModel = new OrderModel();
    $orderModel->completePurchase($_POST);
} catch (Exception $e) {
    header('Location: error.php?error=database');
    exit;
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