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

if (isset($_GET['new'])) {
    unset($_SESSION['id']);
    unset($_SESSION['name']);
    unset($_SESSION['category']);
    unset($_SESSION['delivery_info']);
    unset($_SESSION['turn']);
    for ($i=1; $i<=5; $i++) {
        unset($_SESSION['size_' . $i]);
        unset($_SESSION['price_' . $i]);
    }
} elseif (isset($_GET['id'])) {
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
                $error['fileUploadError'] = 'ファイルの移動に失敗しました';
            } else {
                try {
                    $productModel = new ProductModel();
                    $productModel->imgUpload($_SESSION[id], $_FILES['img']['name']);
                    header('Location: product_edit.php?id=' . $_SESSION['id']);
                } catch (PDOException $e) {
                    $error['databaseError'] = '画像のアップロードに失敗しました';
                }
            }
            exec('sudo chmod 0755 ../' . IMG_PATH);
        } elseif ($_FILES['img']['error'] == UPLOAD_ERR_NO_FILE) {
            $error['fileUploadError'] = 'ファイルがアップロードされませんでした';
        } else {
            $error['fileUploadError'] = 'ファイルのアップロードに失敗しました';
        }
    }
}
?>

<?php require_once('header.html') ?>
<link rel="stylesheet" href="../css/admin_product_edit.css">
<main>
    <?php require_once('secondHeader.html'); ?>
    <?php getPage(); ?>
    <p class="error"><?=isset($error['databaseError']) ? $error['databaseError'] : ''?></p>
    <form action="product_conf.php<?=isset($_GET['new']) ? '?new=true' : ''?>" method="post">
        <input type="hidden" name="token" value="<?=getToken()?>">
        <table border="1">
            <?php if (!isset($_GET['new'])) : ?>
                <tr>
                    <th>ID</th>
                    <td colspan="3"><?=h($_SESSION['id'])?></td>
                </tr>
            <?php endif; ?>
            <tr>
                <th>商品名</th>
                <td colspan="3"><input type="text" name="name" <?=isset($productData) ? 'value="' . $productData[0]['name'] . '"' : '';?><?=isset($_SESSION['name']) ? 'value="' . $_SESSION['name'] . '"': '';?>></td>
            </tr>
            <tr>
                <th>商品カテゴリー</th>
                <td colspan="3">
                    <select name="category">
                        <?php foreach ($productCategories as $category) : ?>
                            <option <?=(isset($productData) and $productData[0]['category_name'] == $category['name']) ? 'selected' : '';?><?=(isset($_SESSION['category']) and $_SESSION['category'] == $category['name']) ? 'selected' : '';?>><?=h($category['name'])?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th>配送情報</th>
                <td colspan="3">
                    <input type="text" name="delivery_info" <?=isset($productData) ? 'value="' . $productData[0]['delivery_info'] . '"' : '';?><?=isset($_SESSION['delivery_info']) ? 'value="' . $_SESSION['delivery_info'] . '"' : '';?>>
                </td>
            </tr>
            <tr>
                <th>表示順(商品)</th>
                <td colspan="3">
                    <input type="number" name="turn" <?=isset($productData) ? 'value="' . $productData[0]['turn'] . '"' : '';?><?=isset($_SESSION['turn']) ? 'value="' . $_SESSION['turn'] . '"' : '';?>>
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
                    <td><input type="number" name="size_<?=$i?>" <?=isset($productData) ? 'value="' . $productData[$i - 1]['size'] . '"' : '';?><?=isset($_SESSION['size_' . $i]) ? 'value="' . $_SESSION['size_' . $i] . '"' : '';?>></td>
                    <td><input type="number" name="price_<?=$i?>" <?=isset($productData) ? 'value="' . $productData[$i - 1]['price'] . '"' : '';?><?=isset($_SESSION['price_' . $i]) ? 'value="' . $_SESSION['price_' . $i] . '"' : '';?>></td>
                </tr>
            <?php endfor;?>
        </table>
        <p class="submit-button"><input type="submit" name="send" class="btn" value="確認画面へ"></p>
    </form>
    <?php if (!isset($_GET['new'])) : ?>
        <p class="error"><?=isset($error['fileUploadError']) ? $error['fileUploadError'] : ''?></p>
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
<?php require_once('footer.html') ?>
