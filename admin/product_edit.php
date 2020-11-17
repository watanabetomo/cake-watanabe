<?php
require_once('../autoload.php');

if (!isset($_SESSION['admin']['authenticated'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['action'])) {
    header('Location: product_list.php');
    exit;
}

if ($_GET['action'] != 'edit' and $_GET['action'] != 'new') {
    header('Location: product_list.php');
    exit;
}

try {
    $productModel = new ProductModel();
    $productData = [];
    if ($_GET['action'] == 'edit') {
        $productData = $productModel->fetchSingleProduct($_GET['id']);
    }
    $productData = $_POST + $productData;
} catch (Exception $e) {
    header('Location: product_list.php');
    exit;
}

try {
    $productCategoryModel = new ProductCategoryModel();
    $productCategories = $productCategoryModel->fetchAllName();
    if (isset($_POST['upload'])) {
        if (!empty($_FILES['img'])) {
            $productModel->uploadImg($_GET['id'], $_FILES['img']);
        }
    }
} catch (PDOException $e) {
    $databaseError = '商品情報の取得及び' . ($_GET['action'] == 'edit' ? '更新' : '登録') . 'に失敗しました。<br>システム管理者にお問い合わせください。';
} catch (Exception $e) {
    $fileUploadError = 'ファイルのアップロードに失敗しました。<br>システム管理者にお問い合わせください。';
}

?>

<?php require_once('admin_header.html')?>
<link rel="stylesheet" href="../css/admin_product.css">
<main>
    <?php getPage()?>
    <p class="error"><?=isset($databaseError) ? $databaseError : ''?></p>
    <form action="product_conf.php<?=(isset($_GET['action'])) ? '?action=' . $_GET['action'] : ''?><?=isset($_GET['id']) ? '&id=' . $_GET['id'] : ''?>" method="post">
        <table border="1">
            <?php if (isset($_GET['action']) and $_GET['action'] == 'edit') :?>
                <tr>
                    <th>ID</th>
                    <td colspan="3"><?=$_GET['id']?></td>
                </tr>
            <?php endif;?>
            <tr>
                <th>商品名</th>
                <td colspan="3"><input type="text" name="name" value="<?=isset($productData['name']) ? h($productData['name']) : ''?>"></td>
            </tr>
            <tr>
                <th>商品カテゴリー</th>
                <td colspan="3">
                    <select name="category_id">
                        <?php foreach ($productCategories as $category) :?>
                            <option value="<?=$category['id']?>"<?=(isset($productData['product_category_id']) and $productData['product_category_id'] == $category['id']) ? ' selected' : ''?>><?=$category['name']?></option>
                        <?php endforeach;?>
                    </select>
                </td>
            </tr>
            <tr>
                <th>配送情報</th>
                <td colspan="3">
                    <input type="text" name="delivery_info" value="<?=isset($productData['delivery_info']) ? h($productData['delivery_info']) : ''?>">
                </td>
            </tr>
            <tr>
                <th>表示順(商品)</th>
                <td colspan="3">
                    <input type="number" name="turn" value="<?=isset($productData['turn']) ? h($productData['turn']) : ''?>">
                </td>
            </tr>
            <tr>
                <th rowspan="6">商品詳細</th>
                <th>表示順(商品詳細)</th>
                <th>サイズ(cm)</th>
                <th>価格(円)</th>
            </tr>
            <?php for ($i = 0; $i < 5; $i++) :?>
                <tr>
                    <td><?=$i?></td>
                    <td><input type="number" name="details[<?=$i?>][size]" value="<?=isset($productData['details'][$i]['size']) ? h($productData['details'][$i]['size']) : ''?>"></td>
                    <td><input type="number" name="details[<?=$i?>][price]" value="<?=isset($productData['details'][$i]['price']) ? h($productData['details'][$i]['price']) : ''?>"></td>
                </tr>
            <?php endfor?>
        </table>
        <p class="submit-button"><input type="submit" name="send" class="btn" value="確認画面へ"></p>
    </form>
    <?php if (isset($_GET['action']) and $_GET['action'] == 'edit') :?>
        <p class="error"><?=isset($fileUploadError) ? $fileUploadError : ''?></p>
        <form id="upload" action="" method="post" enctype="multipart/form-data" onsubmit="return confirm('本当に画像をアップロードしますか？')">
            <table border="1" style="margin-top: 70px;">
                <tr>
                    <th>ファイル選択</th>
                    <td><input type="file" name="img"></td>
                </tr>
                <tr>
                    <th>画像</th>
                    <td><?=isset($productData) ? '<img src="../' . IMG_PATH . $productData['img'] . '" alt="' . $productData['img'] . '"' : ''?></td>
                </tr>
            </table>
            <p class="submit-button"><input type="submit" class="btn" name="upload" value="登録"></p>
        </form>
    <?php endif; ?>
</main>
<?php require_once('admin_footer.html') ?>
