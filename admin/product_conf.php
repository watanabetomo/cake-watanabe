<?php
require_once('autoload.php');

if (!isset($_SESSION['authenticated'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_SESSION['name']) and !isset($_SESSION['category']) and !isset($_SESSION['img']) and !isset($_SESSION['delivery_info'])) {
    header('Location: product_edit.php');
    exit;
}

if (isset($_POST['cancel'])) {
    $new = isset($_GET['new']) ? '?new=true' : '';
    header('Location: product_edit.php' .  $new);
    exit;
}

if (isset($_POST['send'])) {
    try {
        $productModel = new ProductModel();
        $productCategoryModel = new ProductCategoryModel();
        $category_id = $productCategoryModel->getIdByName($_SESSION['category']);
        if (isset($_GET['new'])) {
            $productModel->register($_SESSION['name'], $category_id['id'], $_SESSION['delivery_info'], $_SESSION['login_id']);
            header('Location: product_done.php');
            exit;
        }
        $productModel->update($_SESSION['id'], $_SESSION['name'], $category_id['id'], $_SESSION['delivery_info'], $_SESSION['login_id']);
        header('Location: product_done.php');
        exit;
    } catch (PDOException $e) {
        $error['databaseError'] = $e->getMessage();
        //'データベースに接続できませんでした';
    }
}

?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登録確認</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/admin_product_edit.css">
    <link rel="stylesheet" href="../css/admin_util.css">
    <link rel="stylesheet" href="../css/admin_footer_fixbottom.css">
</head>

<body>
    <div class="container"><?php include('header.html') ?> <?php include('secondHeader.html'); ?>
        <main>
            <?php getPage('商品データ編集') ?>
            <p class="error"><?= isset($error['databaseError']) ? $error['databaseError'] : ''; ?></p>
            <form action="" method="post">
                <table class="table table-bordered">
                    <?php if (!isset($_GET['new'])) : ?>
                        <tr>
                            <th>ID</th>
                            <td><?= h($_SESSION['id']) ?></td>
                        </tr>
                    <?php endif; ?> <tr>
                        <th>商品名</th>
                        <td><?= h($_SESSION['name']) ?></td>
                    </tr>
                    <tr>
                        <th>商品カテゴリー</th>
                        <td><?= h($_SESSION['category']) ?></td>
                    </tr>
                    <tr>
                        <th>配送情報</th>
                        <td><?= h($_SESSION['delivery_info']) ?></td>
                    </tr>
                </table>
                <p class="submit-button"><input type="submit" name="send" class="btn" value="登録"> <input type="submit" name="cancel" class="btn" value="キャンセル"></p>
            </form>
        </main> <?php include('footer.html') ?></div>
</body>

</html>