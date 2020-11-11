<?php
require_once('../autoload.php');

if (!isset($_SESSION['admin_authenticated'])) {
    header('Location: login.php');
    exit;
}
?>

<?php require_once('admin_header.html')?>
<link rel="stylesheet" href="../css/admin_top.css">
<main></main>
<?php require_once('admin_footer.html')?>
