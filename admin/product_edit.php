<?php
require_once('autoload.php');

$title = '商品データ編集';

if (!isset($_SESSION['authenticated'])) {
    header('Location: login.php');
    exit;
}

try {
    $productCategoryModel = new ProductCategoryModel();
    $productCategories = $productCategoryModel->fetchAllName();
} catch (PDOException $e) {
    $error['databaseError'] = 'データベースに接続できませんでした';
}

if (isset($_POST['send'])) {
    $_SESSION['name'] = $_POST['name'];
    $_SESSION['category'] = $_POST['category'];
    $_SESSION['delivery_info'] = $_POST['delivery_info'];
    $_SESSION['conf'] = true;
    header('Location: product_conf.php' . (isset($_GET['new']) ? '?new=true' : ''));
    exit;
}


if (isset($_GET['new'])) {
    unset($_SESSION['name']);
    unset($_SESSION['category']);
    unset($_SESSION['delivery_info']);
}

if (isset($_GET['id'])) {
    $_SESSION['id'] = $_GET['id'];
    try {
        $productModel = new ProductModel();
        $productData = $productModel->fetchById($_GET['id']);
    } catch (PDOException $e) {
        $error['datebaseError'] = 'データベースに接続できませんでした';
    }
}

if (isset($_POST['upload'])) {
    if (!empty($_FILES['img'])) {
        if ($_FILES['img']['error'] == UPLOAD_ERR_OK) {
            exec('sudo chmod 0777 ../' . IMG_PATH);
            if (!move_uploaded_file($_FILES['img']['tmp_name'], '../' . IMG_PATH . mb_convert_encoding($_FILES['img']['name'], 'cp932', 'utf8'))) {
                $error[$fileUploadError] = 'ファイルの移動に失敗しました';
            }else{
                try{
                    $productModel = new ProductModel();
                    $productModel->imgUpload($_SESSION[id], $_FILES['img']['name']);
                    header('Location: product_edit.php?id=' . $_SESSION['id']);
                }catch(PDOException $e){
                    $error['databaseError'] = 'データベースに接続できませんでした';
                }
            }
            exec('sudo chmod 0755 ../' . IMG_PATH);
        } elseif ($_FILES['img']['error'] == UPLOAD_ERR_NO_FILE) {
            $error[$fileUploadError] = 'ファイルがアップロードされませんでした';
        } else {
            $error[$fileUploadError] = 'ファイルのアップロードに失敗しました';
        }
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
    <link rel="stylesheet" href="../css/admin_product_edit.css">
    <link rel="stylesheet" href="../css/admin_util.css">
</head>

<body>
    <div class="container">
        <?php include('header.html') ?>
        <?php include('secondHeader.html'); ?>
        <main>
            <?php getPage(); ?>
            <p class="error"><?=isset($error['databaseError']) ? $error['databaseError'] : ''?></p>
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
                        <td><input type="text" name="name" <?=isset($productData) ? 'value="' . $productData['name'] . '"' : '';?> <?=isset($_SESSION['name']) ? 'value="' . $_SESSION['name'] . '"' : '';?>></td>
                    </tr>
                    <tr>
                        <th>商品カテゴリー</th>
                        <td>
                            <select name="category">
                                <?php foreach ($productCategories as $category) : ?>
                                    <option <?=(isset($productData) and $productData['category_name'] == $category['name']) ? 'selected' : '';?> <?=(isset($_SESSION['category']) and $_SESSION['category'] == $category['name']) ? 'selected' : '';?>><?=h($category['name'])?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>配送情報</th>
                        <td>
                            <input type="text" name="delivery_info" <?=isset($productData) ? 'value="' . $productData['delivery_info'] . '"' : '';?> <?=isset($_SESSION['delivery_info']) ? 'value="' . $_SESSION['delivery_info'] . '"' : '';?>>
                        </td>
                    </tr>
                </table>
                <p class=submit-button><input type="submit" name="send" class="btn" value="確認画面へ"></p>
            </form>
            <?php if (!isset($_GET['new'])) : ?>
                <p class="error"><?=isset($error['fileUploadError']) ? $error['fileUploadError'] : ''?></p>
                <form id="upload" action="" method="post" enctype="multipart/form-data" onsubmit="return confirm('本当に画像をアップロードしますか？')">
                    <table class="table table-bordered" style="margin-top: 70px;">
                        <tr>
                            <th>ファイル選択</th>
                            <td><input type="file" name="img"></td>
                        </tr>
                        <tr>
                            <th>画像</th>
                            <td> <?=isset($productData) ? '<img src="../' . IMG_PATH . $productData['img'] . '" alt="' . $productData['img'] . '"' : ''?></td>
                        </tr>
                    </table>
                    <p class=submit-button><input id="submit_button" type="submit" class="btn" name="upload" value="登録"></p>
                </form>
            <?php endif; ?>
        </main>
        <?php include('footer.html') ?>
    </div>
</body>

</html>