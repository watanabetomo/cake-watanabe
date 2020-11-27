<?php
require_once('../autoload.php');

?>

<?php require_once('admin_header.html')?>
<link rel="stylesheet" href="../css/admin_purchase_list.css">
<main>
    <?php getPage()?>
    <p class="error"><?=isset($error) ? $error : ''?></p>

</main>
<?php require_once('admin_footer.html')?>