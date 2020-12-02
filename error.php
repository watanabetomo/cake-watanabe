<?php
require_once('autoload.php');

if (
    !isset($_GET['error'])
    or ($_GET['error'] != 'param' and $_GET['error'] != 'datebase')
) {
    header('Location: error.php?error=param');
    exit;
}

?>
<?php require_once('header.html')?>
<main>
    <p class="done-message"><?=getErrorMessage()?><br></p>
</main>
<?php require_once('footer.html')?>