<?php
require_once('admin/autoload.php');
require_once('vendor/autoload.php');

try {
    $cartModel = new CartModel();
    $cartModel->truncateCart();
    $oderModel = new OrderModel();
} catch (PDOException $e) {
    $error['databaseError'] = 'データベースに接続できませんでした';
}

try {
    $transport = new Swift_SmtpTransport(
        SMTP_HOST,
        SMTP_PORT,
        SMTP_PROTOCOL
    );
    $transport->setUsername(GMAIL_SITE);
    $transport->setPassword(GMAIL_APPPASS);
    $mailer = new Swift_Mailer($transport);

    $message = new Swift_Message(MAIL_TITLE);
    $message->setFrom(MAIL_FROM);
    $message->setTo(['chelseano55@gmail.com']);

$mailBody = <<<EOT
<h1>テスト</h1>
EOT;

    $message->setBody($mailBody, 'text/html');
    $result = $mailer->send($message);
} catch (Exception $e) {
    $error = "メールの送信に失敗しました";
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