<?php
require_once('autoload.php');

if (!isset($_SESSION['authenticated'])) {
    header('Location: login.php');
    exit;
}

try {
    $productCategoryModel = new ProductCategoryModel();
    $productCategories = $productCategoryModel->fetchAllName();
} catch (PDOException $e) {
    echo $e->getMessage();
}

if (isset($_POST['back'])) {
    header('Location: product_list.php');
    exit;
}

if (isset($_POST['send'])) {
    $_SESSION['name'] = $_POST['name'];
    $_SESSION['category'] = $_POST['category'];
    $_SESSION['img'] = $_POST['img'];
    $_SESSION['delivery_info'] = $_POST['delivery_info'];
    if(isset($_GET['new'])){
        header('Location: product_conf.php?new=true');
        exit;
    }
    header('Location: product_conf.php');
    exit;
}

if (isset($_GET['new'])) {
    unset($_SESSION['name']);
    unset($_SESSION['category']);
    unset($_SESSION['img']);
    unset($_SESSION['delivery_info']);
}

if (isset($_GET['id'])) {
    $_SESSION['id'] = $_GET['id'];
    try {
        $productModel = new ProductModel();
        $productData = $productModel->fetchById($_GET['id']);
    } catch (PDOException $e) {
        echo 'データベースに接続できませんでした';
    }
}

if (!empty($_FILES)) {
    if ($_FILES['userfile']['error'] == UPLOAD_ERR_OK) {
        if (move_uploaded_file($_FILES['userfile']['tmp_name'], 'img/' . mb_convert_encoding($_FILES['userfile']['name'], 'cp932', 'utf8'))) {
            $message = 'ファイルをアップロードしました';
        } else {
            $message = 'ファイルの移動に失敗しました';
        }
    } elseif ($_FILES['userfile']['error'] == UPLOAD_ERR_NO_FILE) {
        $message = 'ファイルがアップロードされませんでした';
    } else {
        $message = 'ファイルのアップロードに失敗しました';
    }
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品データ編集</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/button.css">
    <link rel="stylesheet" href="../css/product_edit.css">
</head>

<body>
    <?php include('header.html') ?>
    <?php include('secondHeader.html'); ?>
    <main class="container">
        <h1><button type="button" class="btn btn-dark" disabled>商品データ編集</button></h1>
        <form action="" method="post">
            <table class="table table-bordered">
            <?php if (!isset($_GET['new'])) : ?>
                <tr>
                    <th>ID</th>
                    <td><?=h($_SESSION['id'])?></td>
                </tr>
            <?php endif; ?>
                <tr>
                    <th>商品名</th>
                    <td><input type="text" name="name" <?= isset($productData) ? 'value="' . $productData['name'] . '"' : ''; ?> <?= isset($_SESSION['name']) ? 'value="' . $_SESSION['name'] . '"' : ''; ?>></td>
                </tr>
                <tr>
                    <th>商品カテゴリー</th>
                    <td>
                        <select name="category">
                            <?php foreach ($productCategories as $category) : ?>
                                <option <?= (isset($productData) and $productData['category_name'] == $category['name']) ? 'selected' : ''; ?> <?= (isset($_SESSION['category']) and $_SESSION['category'] == $category['name']) ? 'selected' : ''; ?>><?= h($category['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <?php if (!isset($_GET['new'])) : ?>
                    <tr>
                        <th>画像</th>
                        <td><input type="file" name="img"></td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <th>配送情報</th>
                    <td><input type="text" name="delivery_info" <?= isset($productData) ? 'value="' . $productData['delivery_info'] . '"' : ''; ?> <?= isset($_SESSION['delivery_info']) ? 'value="' . $_SESSION['delivery_info'] . '"' : ''; ?>></td>
                </tr>
            </table>
            <p><input type="submit" name="send" class="btn" value="確認画面へ"> <input type="submit" name="back" class="btn" value="やめる"></p>
        </form>
    </main>
    <?php include('footer.html')?>
</body>

</html>