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
    $_SESSION['delivery_info'] = $_POST['delivery_info'];
    if (isset($_GET['new'])) {
        header('Location: product_conf.php?new=true');
        exit;
    }
    header('Location: product_conf.php');
    exit;
}

if (isset($_POST['upload'])) {
    if (!empty($_FILES)) {
        if ($_FILES['img']['error'] == UPLOAD_ERR_OK) {
            exec('sudo chmod 0777 ../' . IMG_PATH);
            if (!move_uploaded_file($_FILES['img']['tmp_name'], '../' . IMG_PATH . mb_convert_encoding($_FILES['img']['name'], 'cp932', 'utf8'))) {
                echo 'ファイルの移動に失敗しました';
            }
            exec('sudo chmod 0755 ../' . IMG_PATH);
        } elseif ($_FILES['img']['error'] == UPLOAD_ERR_NO_FILE) {
            echo 'ファイルがアップロードされませんでした';
        } else {
            echo 'ファイルのアップロードに失敗しました';
        }
    }
    header('Location: product_edit.php');
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
        echo 'データベースに接続できませんでした';
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />
    <link rel="stylesheet" href="../css/admin_header.css">
    <link rel="stylesheet" href="../css/admin_button.css">
    <link rel="stylesheet" href="../css/admin_product_edit.css">
    <link rel="stylesheet" href="../css/admin_util.css">
</head>

<body>
    <div class="container">
        <?php include('header.html') ?>
        <?php include('secondHeader.html'); ?>
        <main>
            <?php getPage('商品データ編集');?>
            <form action="" method="post">
                <table class="table table-bordered"> <?php if (!isset($_GET['new'])) : ?> <tr>
                            <th>ID</th>
                            <td><?= h($_SESSION['id']) ?></td>
                        </tr> <?php endif; ?> <tr>
                        <th>商品名</th>
                        <td><input type="text" name="name" <?= isset($productData) ? 'value="' . $productData['name'] . '"' : ''; ?> <?= isset($_SESSION['name']) ? 'value="' . $_SESSION['name'] . '"' : ''; ?>></td>
                    </tr>
                    <tr>
                        <th>商品カテゴリー</th>
                        <td> <select name="category"> <?php foreach ($productCategories as $category) : ?> <option <?= (isset($productData) and $productData['category_name'] == $category['name']) ? 'selected' : ''; ?> <?= (isset($_SESSION['category']) and $_SESSION['category'] == $category['name']) ? 'selected' : ''; ?>><?= h($category['name']) ?></option> <?php endforeach; ?> </select> </td>
                    </tr>
                    <tr>
                        <th>配送情報</th>
                        <td><input type="text" name="delivery_info" <?= isset($productData) ? 'value="' . $productData['delivery_info'] . '"' : ''; ?> <?= isset($_SESSION['delivery_info']) ? 'value="' . $_SESSION['delivery_info'] . '"' : ''; ?>></td>
                    </tr>
                </table>
                <p class=submit-button><input type="submit" name="send" class="btn" value="確認画面へ"> <input type="submit" name="back" class="btn" value="やめる"></p>
            </form> <?php if (!isset($_GET['new'])) : ?> <form id="upload" action="" method="post" enctype="multipart/form-data" style="margin-top: 70px;">
                    <table class="table table-bordered">
                        <tr>
                            <th>ファイル選択</th>
                            <td><input type="file" name="img"></td>
                        </tr>
                        <tr>
                            <th>画像</th>
                            <td> <?php if (!empty($_FILES)) : ?> <img src="../<?= IMG_PATH . $_FILES['img']['name'] ?>" alt="<?= $_FILES['img']['name'] ?>"> <?php endif; ?> </td>
                        </tr>
                    </table>
                    <p class=submit-button><a href="#modal" rel="modal:open" role="button" class="btn">登録</a></p>
                    <div id="modal" class="modal" style="height: 100px">
                        <p>本当に画像をアップロードしますか？</p> <input id="submit_button" type="submit" name="upload" class="btn btn-sm" value="OK"> <a href="" rel="modal:close" role="button" class="btn btn-sm">キャンセル</a>
                    </div>
                </form> <?php endif; ?>
        </main> <?php include('footer.html') ?></div>
    <script type="text/javascript">
        $("#submit_button").click(function() {
            $("#upload").submit();
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
</body>

</html>