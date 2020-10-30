<?php
require_once('autoload.php');

$title = '登録完了';

if (!isset($_SESSION['authenticated'])) {
    header('Location: login.php');
    exit;
}
?>

<?php require_once('admin_header.html') ?>
<link rel="stylesheet" href="../css/admin_product_edit.css">
<main>
    <?php require_once('secondadmin_header.html'); ?>
    <?php getPage() ?>
    <h2 class="done">登録が完了しました</h2>
</main>
<?php require_once('admin_footer.html') ?>
