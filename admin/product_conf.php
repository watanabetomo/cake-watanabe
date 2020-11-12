<?php
require_once('../autoload.php');

if (!isset($_SESSION['admin_authenticated'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_POST['send']) and !isset($_POST['register'])) {
    header('Location: product_edit.php?action=' . $_GET['action'] . (isset($_GET['id']) ? '&id=' . $_GET['id'] : ''));
    exit;
}

?>

<?php require_once('admin_header.html')?>
<link rel="stylesheet" href="../css/admin_product.css">
<main>
    <?php getPage()?>
    <p class="error"><?=isset($error) ? $error : ''?></p>
    <table border="1">
        <?php if (isset($_GET['action']) and $_GET['action'] != 'new') :?>
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
            <td colspan="3"><?=h($_POST['category'])?></td>
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
        <?php for ($i=0; $i<5; $i++) :?>
            <tr>
                <td><?=$i?></td>
                <td><?=h($_POST['size'][$i])?></td>
                <td><?=h($_POST['price'][$i])?></td>
            </tr>
        <?php endfor;?>
    </table>
    <form action="product_done.php<?=(isset($_GET['action'])) ? '?action=' . $_GET['action'] : ''?><?=isset($_GET['id']) ? '&id=' . $_GET['id'] : ''?>" method="post">
        <input type="hidden" name="name" value="<?=$_POST['name']?>">
        <input type="hidden" name="category" value="<?=$_POST['category']?>">
        <input type="hidden" name="delivery_info" value="<?=$_POST['delivery_info']?>">
        <input type="hidden" name="turn" value="<?=$_POST['turn']?>">
        <?php for ($i=0; $i<5; $i++) :?>
            <input type="hidden" name="size[]" value="<?=$_POST['size'][$i]?>">
            <input type="hidden" name="price[]" value="<?=$_POST['price'][$i]?>">
        <?php endfor;?>
        <p class="submit-button register-btn"><input type="submit" name="register" class="btn" value="登録完了する"></p>
    </form>
    <form action="product_edit.php<?=(isset($_GET['action'])) ? '?action=' . $_GET['action'] : ''?><?=isset($_GET['id']) ? '&id=' . $_GET['id'] : ''?>" method="post">
        <input type="hidden" name="name" value="<?=$_POST['name']?>">
        <input type="hidden" name="category" value="<?=$_POST['category']?>">
        <input type="hidden" name="delivery_info" value="<?=$_POST['delivery_info']?>">
        <input type="hidden" name="turn" value="<?=$_POST['turn']?>">
        <?php for ($i=0; $i<5; $i++) :?>
            <input type="hidden" name="size[]" value="<?=$_POST['size'][$i]?>">
            <input type="hidden" name="price[]" value="<?=$_POST['price'][$i]?>">
        <?php endfor;?>
        <p class="submit-button"><input type="submit" name="fix" class="btn" value="修正する"></p>
    </form>
</main>
<?php require_once('admin_footer.html')?>