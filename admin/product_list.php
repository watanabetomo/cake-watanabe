<?php
require_once('autoload.php');

$title = '商品リスト';

if (!isset($_SESSION['authenticated'])) {
    header('Location: login.php');
    exit;
}

try {
    $productModel = new ProductModel();
} catch (PDOException $e) {
    $error['databaseError'] = 'データベースに接続できませんでした';
}

if (!isset($_GET['searched'])) {
    $productList = $productModel->fetchAllData();
} else {
    $productList = $productModel->search($_GET['searched']);
}

if (isset($_POST['delete'])) {
    try {
        $productModel->delete($_POST['id']);
        header('Location: product_list.php');
    } catch (PDOException $e) {
        $error['databaseError'] = '商品情報の削除に失敗しました';
    }
} elseif (isset($_POST['search'])) {
    if ($_POST['search'] != '') {
        header('Location: product_list.php?searched=' . $_POST['keyword']);
        exit;
    }
} elseif (isset($_POST['all'])) {
    header('Location: product_list.php');
    exit;
} elseif (isset($_POST['id_asc'])) {
    $productList = $productModel->sortIdAsc();
} elseif (isset($_POST['id_desc'])) {
    $productList = $productModel->sortIdDesc();
} elseif (isset($_POST['name_asc'])) {
    $productList = $productModel->sortNameAsc();
} elseif (isset($_POST['name_desc'])) {
    $productList = $productModel->sortNameDesc();
} elseif (isset($_POST['updated_at_asc'])) {
    $productList = $productModel->sortUpdatedAsc();
} elseif (isset($_POST['updated_at_desc'])) {
    $productList = $productModel->sortUpdatedDesc();
}
?>

<?php require_once('admin_header.html') ?>
<link rel="stylesheet" href="../css/admin_product_list.css">
<main>
    <?php require_once('secondadmin_header.html') ?>
    <?php getPage() ?>
    <?=isset($error['databeseError']) ? $error['databaseError'] : '';?>
    <form action="" method="post">
        <p class="search"><input type="text" name="keyword"> <input type="submit" name="search" value="絞り込む"> <input type="submit" name="all" value="すべて表示"></p>
    </form>
    <table border="1">
        <form action="" method="post">
            <tr>
                <th><input type="submit" name="id_desc" class="icon" value="▲"><p class="sorted">ID</p><input type="submit" name="id_asc" class="icon" value="▼"></th>
                <th><input type="submit" name="name_desc" class="icon" value="▲"><p class="sorted">商品名</p><input type="submit" name="name_asc" class="icon" value="▼"></th>
                <th>画像</th>
                <th>登録日時</th>
                <th><input type="submit" name="updated_at_desc" class="icon" value="▲"><p class="sorted">更新日時</p><input type="submit" name="updated_at_asc" class="icon" value="▼"></th>
                <th><a href="product_edit.php?new=true" role="button" class="btn btn-sm">新規登録</a></th>
            </tr>
        </form>
        <?php foreach ($productList as $product) : ?>
            <tr>
                <td><?=h($product['id'])?></td>
                <td><?=h($product['name'])?></td>
                <td><img src="../<?=IMG_PATH . h($product['img'])?>" alt="<?=h($product['img'])?>"></td>
                <td><?=(new DateTime(h($product['created_at'])))->format('Y-m-d H:i:s')?></td>
                <td><?=(new DateTime(h($product['updated_at'])))->format('Y-m-d H:i:s')?></td>
                <td>
                    <p>
                        <a href="product_edit.php?id=<?=h($product['id'])?>" class="btn btn-sm" style="margin-top:20px;">編集</a>
                        <form action="" method="post" onsubmit="return confirm('本当に削除しますか？')">
                            <input type="hidden" name="id" value="<?=h($product['id'])?>">
                            <input type="submit" class="btn btn-sm" name="delete" value="削除">
                        </form>
                    </p>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</main>
<?php require_once('admin_footer.html') ?>