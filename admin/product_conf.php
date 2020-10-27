<?php
require_once('autoload.php');

$title = '商品データ登録確認';

if (!isset($_SESSION['authenticated'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_SESSION['conf'])) {
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
        <table class="table table-bordered">
            <?php if (!isset($_GET['new'])) : ?>
                <tr>
                    <th>ID</th>
                    <td><?=h($_SESSION['id'])?></td>
                </tr>
            <?php endif; ?>
            <tr>
                <th>商品名</th>
                <td><?=h($_SESSION['name'])?></td>
            </tr>
            <tr>
                <th>商品カテゴリー</th>
                <td><?=h($_SESSION['category'])?></td>
            </tr>
            <tr>
                <th>配送情報</th>
                <td><?=h($_SESSION['delivery_info'])?></td>
            </tr>
        </table>
        <p class="submit-button"><input type="submit" name="send" class="btn" value="登録"> <input type="submit" name="cancel" class="btn" value="キャンセル"></p>
    </form>
</main>
<?php require_once('footer.html') ?>