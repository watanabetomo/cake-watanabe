<?php
require_once('../autoload.php');

if (!isset($_SESSION['admin_authenticated'])) {
    header('Location: login.php');
    exit;
}

try {
    $productCategoryModel = new ProductCategoryModel();
    $productModel = new ProductModel();
    $productCategories = $productCategoryModel->fetchAllName();
    if (isset($_GET['action']) and $_GET['action'] == "edit") {
        $productData = $productModel->fetchById($_GET['id']);
    }
    if (isset($_POST['upload'])) {
        if (!empty($_FILES['img'])) {
            $productModel->imgUpload($_GET['id'], $_FILES['img']['name']);
            header('Location: product_edit.php?id=' . $_GET['id']);
            if ($_FILES['img']['error'] == UPLOAD_ERR_OK) {
                exec('sudo chmod 0777 ../' . IMG_PATH);
                if (!move_uploaded_file($_FILES['img']['tmp_name'], '../' . IMG_PATH . mb_convert_encoding($_FILES['img']['name'], 'cp932', 'utf8'))) {
                    $error['fileUpload'] = 'ファイルの移動に失敗しました';
                }
                exec('sudo chmod 0755 ../' . IMG_PATH);
            } elseif ($_FILES['img']['error'] == UPLOAD_ERR_NO_FILE) {
                $error['fileUpload'] = 'ファイルがアップロードされませんでした';
            } else {
                $error['fileUpload'] = 'ファイルのアップロードに失敗しました';
            }
        }
    }
} catch (PDOException $e) {
    $error['database'] = 'データベースに接続できませんでした';
}
?>

<?php require_once('admin_header.html') ?>
<link rel="stylesheet" href="../css/admin_product_list.css">
<main>
    <?php getPage()?>
    <p class="error"><?=isset($error['database']) ? $error['database'] : ''?></p>
    <form action="product_conf.php<?=(isset($_GET['action']) and $_GET['action'] == 'new') ? '?action=new' : ''?><?=isset($_GET['id']) ? '?id=' . $_GET['id'] : ''?>" method="post">
        <table border="1">
            <?php if (isset($_GET['id'])) : ?>
                <tr>
                    <th>ID</th>
                    <td colspan="3"><?=$_GET['id']?></td>
                </tr>
            <?php endif; ?>
            <tr>
                <th>商品名</th>
                <td colspan="3"><input type="text" name="name" <?=isset($productData) ? 'value="' . $productData[0]['name'] . '"' : '';?><?=isset($_POST['name']) ? 'value="' . $_POST['name'] . '"': '';?>></td>
            </tr>
            <tr>
                <th>商品カテゴリー</th>
                <td colspan="3">
                    <select name="category">
                        <?php foreach ($productCategories as $category) : ?>
                            <option <?=(isset($productData) and $productData[0]['category_name'] == $category['name']) ? 'selected' : '';?><?=(isset($_POST['category']) and $_POST['category'] == $category['name']) ? 'selected' : '';?>><?=h($category['name'])?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th>配送情報</th>
                <td colspan="3">
                    <input type="text" name="delivery_info" <?=isset($productData) ? 'value="' . $productData[0]['delivery_info'] . '"' : '';?><?=isset($_POST['delivery_info']) ? 'value="' . $_POST['delivery_info'] . '"' : '';?>>
                </td>
            </tr>
            <tr>
                <th>表示順(商品)</th>
                <td colspan="3">
                    <input type="number" name="turn" <?=isset($productData) ? 'value="' . $productData[0]['turn'] . '"' : '';?><?=isset($_POST['turn']) ? 'value="' . $_POST['turn'] . '"' : '';?>>
                </td>
            </tr>
            <tr>
                <th rowspan="6">商品詳細</th>
                <th>表示順(商品詳細)</th>
                <th>サイズ (cm)</th>
                <th>価格 (円)</th>
            </tr>
            <?php for($i=1; $i<=5; $i++):?>
                <tr>
                    <td><?=$i?></td>
                    <td><input type="number" name="size_<?=$i?>" <?=isset($productData) ? 'value="' . $productData[$i - 1]['size'] . '"' : '';?><?=isset($_POST['size_' . $i]) ? 'value="' . $_POST['size_' . $i] . '"' : '';?>></td>
                    <td><input type="number" name="price_<?=$i?>" <?=isset($productData) ? 'value="' . $productData[$i - 1]['price'] . '"' : '';?><?=isset($_POST['price_' . $i]) ? 'value="' . $_POST['price_' . $i] . '"' : '';?>></td>
                </tr>
            <?php endfor;?>
        </table>
        <p class="submit-button"><input type="submit" name="send" class="btn" value="確認画面へ"></p>
    </form>
    <?php if (isset($_GET['action']) and $_GET['action'] != 'new') : ?>
        <p class="error"><?=isset($error['fileUpload']) ? $error['fileUpload'] : ''?></p>
        <form id="upload" action="" method="post" enctype="multipart/form-data" onsubmit="return confirm('本当に画像をアップロードしますか？')">
            <table border="1" style="margin-top: 70px;">
                <tr>
                    <th>ファイル選択</th>
                    <td><input type="file" name="img"></td>
                </tr>
                <tr>
                    <th>画像</th>
                    <td> <?=isset($productData) ? '<img src="../' . IMG_PATH . $productData[0]['img'] . '" alt="' . $productData[0]['img'] . '"' : ''?></td>
                </tr>
            </table>
            <p class="submit-button"><input type="submit" class="btn" name="upload" value="登録"></p>
        </form>
    <?php endif; ?>
</main>
<?php require_once('admin_footer.html') ?>
