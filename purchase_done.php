<?php
require_once('admin/autoload.php');

if ((isset($_SESSION['token']) ? $_SESSION['token'] : '') != getToken()) {
    header('Location: purchase_edit.php');
    exit;
}

try {
    $cartModel = new CartModel();
    $cartModel->truncateCart();
    $oderModel = new OrderModel();
    try {
        mb_language("Japanese");
        mb_internal_encoding("UTF-8");
$mailBody = <<<EOT
$name様
EOT;
        $mail = mb_send_mail('t.watanabe@ebacorp.jp', '【洋菓子店カサミンゴー】ご購入商品確認メール', $mailBody, 'From: chelseano55@gmail.com');

        unset($_SESSION['postal_code1']);
        unset($_SESSION['postal_code2']);
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