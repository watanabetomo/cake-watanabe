<?php
require_once('../autoload.php');

if (!isset($_SESSION['admin_authenticated'])) {
    header('Location: login.php');
    exit;
}

try {
    $productModel = new ProductModel();
    if (isset($_POST['delete'])) {
        $productModel->delete($_POST['delete_id']);
    }
    $productList = $productModel->displayResult(isset($_POST['db_connect']) ? $_POST['db_connect'] : '');
} catch (PDOException $e) {
    $error = 'データベースに接続できませんでした';
}

?>

<?php require_once('admin_header.html')?>
<link rel="stylesheet" href="../css/admin_product.css">
<main>
    <?php getPage()?>
    <p class="error"><?=isset($error) ? $error : ''?></p>
    <form action="" method="post">
        <input type="hidden" name="db_connect" value="search">
        <p class="search"><input type="text" name="keyword"> <input type="submit" name="search" value="絞り込む"> <input type="submit" name="all" value="すべて表示"></p>
    </form>
    <table border="1" class="main-table">
        <tr>
            <th>
                <form action="" method="post">
                    <input type="hidden" name="db_connect" value="sort">
                    <input type="hidden" name="column" value="id">
                    <input type="submit" name="order" class="icon" value="▲">
                    <p class="sorted">ID</p>
                    <input type="submit" name="order" class="icon" value="▼">
                </form>
            </th>
            <th>
                <form action="" method="post">
                    <input type="hidden" name="db_connect" value="sort">
                    <input type="hidden" name="column" value="name">
                    <input type="submit" name="order" class="icon" value="▲">
                    <p class="sorted">商品名</p>
                    <input type="submit" name="order" class="icon" value="▼">
                </form>
            </th>
            <th>画像</th>
            <th>登録日時</th>
            <th>
                <form action="" method="post">
                    <input type="hidden" name="db_connect" value="sort">
                    <input type="hidden" name="column" value="updated_at">
                    <input type="submit" name="order" class="icon" value="▲">
                    <p class="sorted">更新日時</p>
                    <input type="submit" name="order" class="icon" value="▼">
                </form>
            </th>
            <th><a href="product_edit.php?action=new" role="button" class="btn btn-sm">新規登録</a></th>
        </tr>
        <?php foreach ($productList as $product) :?>
            <tr>
                <td><?=h($product['id'])?></td>
                <td><?=h($product['name'])?></td>
                <td><?=isset($product['img']) ? '<img src="../' . IMG_PATH . h($product['img']) . '" alt="' . h($product['img']) . '">' : '未登録'?></td>
                <td><?=(new DateTime(h($product['created_at'])))->format('Y-m-d H:i:s')?></td>
                <td><?=!is_null($product['updated_at']) ? (new DateTime(h($product['updated_at'])))->format('Y-m-d H:i:s') : ''?></td>
                <td>
                    <p>
                        <a href="product_edit.php?action=edit&id=<?=h($product['id'])?>" class="btn btn-sm" style="margin-top:20px;">編集</a>
                        <form action="" method="post" onsubmit="return confirm('本当に削除しますか？')">
                            <input type="hidden" name="delete_id" value="<?=h($product['id'])?>">
                            <input type="submit" class="btn btn-sm" name="delete" value="削除">
                        </form>
                    </p>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</main>
<?php require_once('admin_footer.html')?>