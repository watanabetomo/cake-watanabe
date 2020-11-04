<?php
require_once('admin/autoload.php');

try {
    $cartModel = new CartModel();
    $cartModel->truncateCart();
    $oderModel = new OrderModel();
} catch (PDOException $e) {
    $error['databaseError'] = 'データベースに接続できませんでした';
}
$name = '渡部';
try {
    mb_language("Japanese");
    mb_internal_encoding("UTF-8");
$mailBody = <<<EOT
$name様
EOT;
    $mail = mb_send_mail('t.watanabe@ebacorp.jp', '【洋菓子店カサミンゴー】ご購入商品確認メール', $mailBody, 'From: chelseano55@gmail.com');
} catch (Exception $e) {
    $error['mail'] = "メールの送信に失敗しました";
}

?>
<?php require_once('header.html') ?>
<main>
    <?php if (isset($error)): ?>
        <?php foreach($error as $errorMessage): ?>
            <p class="error"><?=$errorMessage?></p>
        <?php endforeach;?>
    <?php endif; ?>
    <p class="done-message">購入が完了しました。ご利用ありがとうございました。</p>
</main>
<?php require_once('footer.html') ?>