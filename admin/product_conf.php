<?php
require_once('../autoload.php');

if (!isset($_SESSION['admin']['authenticated'])) {
    header('Location: login.php');
    exit;
}

if (
    !isset($_GET['action'])
    or ($_GET['action'] != 'new' and ($_GET['action'] != 'edit' or !isset($_GET['id']) or !preg_match('/^[1-9][0-9]*$/', $_GET['id'])))
    or !isset($_POST['send'])
) {
    header('Location: product_list.php');
    exit;
}

if (isset($_POST['category_id'])) {
    try {
        $productCategoryModel = new ProductCategoryModel();
        $category = $productCategoryModel->getName($_POST['category_id']);
    } catch (Exception $e) {
        $error = '商品情報の取得及び' . ($_GET['action'] == 'edit' ? '更新' : '登録') . 'に失敗しました。<br>システム管理者にお問い合わせください。';
    }
}

?>

<?php require_once('admin_header.html')?>
<link rel="stylesheet" href="../css/admin_product.css">
<main>
    <?php getPage()?>
    <p class="error"><?=isset($error) ? $error : ''?></p>
    <table border="1">
        <?php if ($_GET['action'] != 'new') :?>
            <tr>
                <th>ID</th>
                <td colspan="3"><?=$_GET['id']?></td>
            </tr>
        <?php endif; ?>
        <tr>
            <th>商品名</th>
            <td colspan="3"><?=h($_POST['name'])?></td>
        </tr>
        <tr>
            <th>商品カテゴリー</th>
            <td colspan="3"><?=h($category['name'])?></td>
        </tr>
        <tr>
            <th>配送情報</th>
            <td colspan="3"><?=h($_POST['delivery_info'])?></td>
        </tr>
        <tr>
            <th>表示順(商品)</th>
            <td colspan="3"><?=h($_POST['turn'])?></td>
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
                <td><?=h($_POST['details'][$i]['size'])?></td>
                <td><?=h($_POST['details'][$i]['price'])?></td>
            </tr>
        <?php endfor;?>
    </table>
    <form action="product_done.php?action=<?=$_GET['action']?><?=isset($_GET['id']) ? '&id=' . $_GET['id'] : ''?>" method="post">
        <input type="hidden" name="name" value="<?=$_POST['name']?>">
        <input type="hidden" name="product_category_id" value="<?=$_POST['category_id']?>">
        <input type="hidden" name="delivery_info" value="<?=$_POST['delivery_info']?>">
        <input type="hidden" name="turn" value="<?=$_POST['turn']?>">
        <?php for ($i = 0; $i < 5; $i++) :?>
            <input type="hidden" name="details[<?=$i?>][size]" value="<?=$_POST['details'][$i]['size']?>">
            <input type="hidden" name="details[<?=$i?>][price]" value="<?=$_POST['details'][$i]['price']?>">
        <?php endfor;?>
        <p class="submit-button register-btn"><input type="submit" name="register" class="btn" value="<?=($_GET['action'] == 'edit' ? '更新' : '登録')?>完了する"></p>
    </form>
    <form action="product_edit.php?action=<?=$_GET['action']?><?=isset($_GET['id']) ? '&id=' . $_GET['id'] : ''?>" method="post">
        <input type="hidden" name="name" value="<?=$_POST['name']?>">
        <input type="hidden" name="product_category_id" value="<?=$_POST['category_id']?>">
        <input type="hidden" name="delivery_info" value="<?=$_POST['delivery_info']?>">
        <input type="hidden" name="turn" value="<?=$_POST['turn']?>">
        <?php for ($i = 0; $i < 5; $i++) :?>
            <input type="hidden" name="details[<?=$i?>][size]" value="<?=$_POST['details'][$i]['size']?>">
            <input type="hidden" name="details[<?=$i?>][price]" value="<?=$_POST['details'][$i]['price']?>">
        <?php endfor;?>
        <p class="submit-button"><input type="submit" name="fix" class="btn" value="修正する"></p>
    </form>
</main>
<?php require_once('admin_footer.html')?>