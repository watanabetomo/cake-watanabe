<?php
require_once('autoload.php');

$title = '登録完了';

if (!isset($_SESSION['authenticated'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登録完了</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/admin_product_edit.css">
    <link rel="stylesheet" href="../css/admin_util.css">
    <link rel="stylesheet" href="../css/admin_footer_fixbottom.css">
</head>

<body>
    <div class="container"><?php include('header.html') ?> <?php include('secondHeader.html'); ?> <main> <?php getPage() ?> <h2 class="done">登録が完了しました</h2>
        </main> <?php include('footer.html') ?></div>
</body>

</html>