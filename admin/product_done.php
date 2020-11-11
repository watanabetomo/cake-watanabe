<?php
require_once('../autoload.php');

if (!isset($_SESSION['admin_authenticated'])) {
    header('Location: login.php');
    exit;
}
?>

<?php require_once('admin_header.html')?>
<link rel="stylesheet" href="../css/admin_product.css">
<main>
    <?php getPage()?>
    <h2 class="done">登録が完了しました</h2>
</main>
<?php require_once('admin_footer.html')?>
