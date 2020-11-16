<?php
require_once('../autoload.php');

if (!isset($_SESSION['admin_authenticated'])) {
    header('Location: login.php');
    exit;
}
if (isset($_POST['register'])) {
    try {
        $productModel = new ProductModel();
        if (isset($_GET['action']) and $_GET['action'] == 'new') {
            $productModel->register($_POST['name'], $_POST['product_category_id'], $_POST['delivery_info'], $_POST['turn'], $_SESSION['login_id'], $_POST['size'], $_POST['price']);
        } elseif (isset($_GET['action']) and $_GET['action'] == 'edit' and is_numeric($_GET['id'])) {
            $productModel->update($_GET['id'], $_POST['name'], $_POST['product_category_id'], $_POST['delivery_info'], $_POST['turn'], $_SESSION['login_id'], $_POST['size'], $_POST['price']);
        } else {
            header('Location: product_list.php');
            exit;
        }
    } catch (Exception $e) {
        $error = '商品情報の取得及び登録に失敗しました。<br>システム管理者にお問い合わせください。';
    }
}

?>

<?php require_once('admin_header.html')?>
<link rel="stylesheet" href="../css/admin_product.css">
<main>
    <?php getPage()?>
    <?php if (isset($error)) :?>
        <h2 class="done error"><?=$error?></h2>
    <?php else:?>
        <h2 class="done">登録が完了しました</h2>
    <?php endif;?>
</main>
<?php require_once('admin_footer.html')?>
