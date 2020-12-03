<?php
require_once('autoload.php');

$flg = false;
foreach (array_keys($errorMessages) as $error) {
    if ($_GET['error'] == $error) {
        $flg = true;
    }
}
if (
    !isset($_GET['error'])
    or !$flg
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
