<?php
require_once('autoload.php');

$title = '商品データ登録確認';

if ((isset($_POST['token']) ? $_POST['token'] : '') != getToken()) {
    header('Location: product_edit.php');
    exit;
}

if (!isset($_SESSION['authenticated'])) {
    header('Location: login.php');
    exit;
}

if (isset($_POST['send'])) {
    $_SESSION['name'] = $_POST['name'];
    $_SESSION['category'] = $_POST['category'];
    $_SESSION['delivery_info'] = $_POST['delivery_info'];
    $_SESSION['turn'] = $_POST['turn'];
    for ($i=1; $i<=5; $i++) {
        $_SESSION['size_' . $i] = $_POST['size_' . $i];
        $_SESSION['price_' . $i] = $_POST['price_' . $i];
    }
} elseif (isset($_POST['register'])) {
    try {
        $productModel = new ProductModel();
        $productCategoryModel = new ProductCategoryModel();
        $productDetailModel = new ProductDetailModel();
        $category_id = $productCategoryModel->getIdByName($_SESSION['category']);
        if (isset($_GET['new'])) {
            try {
                $productModel->register($_SESSION['name'], $category_id['id'], $_SESSION['delivery_info'], $_SESSION['turn'], $_SESSION['login_id']);
                try {
                    for ($i=1; $i<=5; $i++) {
                        $productDetailModel->register(($productModel->getMaxId()['MAX(id)'] + 1), $_SESSION['size_' . $i], $_SESSION['price_' . $i], $i);
                    }
                    header('Location: product_done.php');
                    exit;
                } catch (PDOException $e) {
                    $error['databaseError'] = '商品詳細の登録に失敗しました';
                }
            } catch (PDOException $e) {
                $error['databaseError'] = '商品情報の登録に失敗しました';
            }
        } else {
            try {
                $productModel->update($_SESSION['id'], $_SESSION['name'], $category_id['id'], $_SESSION['delivery_info'], $_SESSION['turn'], $_SESSION['login_id']);
                try {
                    for ($i=1; $i<=5; $i++) {
                        $productDetailModel->update($_SESSION['id'], $_SESSION['size_' . $i], $_SESSION['price_' . $i], $i);
                    }
                    header('Location: product_done.php');
                    exit;
                } catch (PDOException $e) {
                    $error['databaseError'] = '商品詳細の更新に失敗しました';
                }
            } catch (PDOException $e) {
                $error['databaseError'] = '商品情報の更新に失敗しました';
            }
        }
    } catch (PDOException $e) {
        $error['databaseError'] = 'データベースに接続できませんでした';
    }
}

?>

<?php require_once('header.html') ?>
<link rel="stylesheet" href="../css/admin_product_edit.css">
<main>
    <?php require_once('secondHeader.html'); ?>
    <?php getPage() ?>
    <p class="error"><?=isset($error['databaseError']) ? $error['databaseError'] : '';?></p>
    <form action="" method="post">
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
                <td colspan="3"><?=h($_SESSION['name'])?></td>
            </tr>
            <tr>
                <th>商品カテゴリー</th>
                <td colspan="3"><?=h($_SESSION['category'])?></td>
            </tr>
            <tr>
                <th>配送情報</th>
                <td colspan="3"><?=h($_SESSION['delivery_info'])?></td>
            </tr>
            <tr>
                <th>表示順(商品)</th>
                <td colspan="3"><?=h($_SESSION['turn'])?></td>
            </tr>
            <tr>
                <th rowspan="6">商品詳細</th>
                <th>表示順(商品詳細)</th>
                <th>サイズ(cm)</th>
                <th>価格(円)</th>
            </tr>
            <?php for ($i=1; $i<=5; $i++) :?>
                <tr>
                    <td><?=$i?></td>
                    <td><?=h($_SESSION['size_' . $i . ''])?></td>
                    <td><?=h($_SESSION['price_' . $i . ''])?></td>
                </tr>
            <?php endfor;?>
        </table>
        <p class="submit-button"><input type="submit" name="register" class="btn" value="登録完了する"></p>
    </form>
</main>
<?php require_once('footer.html') ?>