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

if(!isset($_GET['searched'])){
    $productList = $productModel->fetchAllData();
}else{
    $productList = $productModel->search($_GET['searched']);
}

if (isset($_POST['delete'])) {
    try{
        $productModel->delete($_POST['id']);
        header('Location: product_list.php');
    }catch(PDOException $e){
        $error['databaseError'] = $e;
    }
}

if(isset($_POST['search'])){
    if($_POST['search'] !== ''){
        header('Location: product_list.php?searched=' . $_POST['keyword']);
        exit;
    }
}

if(isset($_POST['all'])){
    header('Location: product_list.php');
    exit;
}
?>

<?php require_once('header.html') ?>
<link rel="stylesheet" href="../css/admin_product_list.css">
<main>
    <?php require_once('secondHeader.html') ?>
    <?php getPage() ?>
    <?=isset($error['databeseError']) ? $error['databaseError'] : '';?>
    <form action="" method="post">
        <p class="search"><input type="text" name="keyword"> <input type="submit" name="search" value="絞り込む"> <input type="submit" name="all" value="すべて表示"></p>
    </form>
    <table class="table-bordered" style="margin: 0 auto;">
        <tr>
            <th><p class="icon">▲</p><p class="sorted">ID</p><p class="icon">▼</p></th>
            <th><p class="icon">▲</p><p class="sorted">商品名</p><p class="icon">▼</p></th>
            <th>画像</th>
            <th>登録日時</th>
            <th><p class="icon">▲</p><p class="sorted">更新日時</p><p class="icon">▼</p></th>
            <th><a href="product_edit.php?new=true" role="button" class="btn btn-sm">新規登録</a></th>
        </tr>
        <?php foreach ($productList as $product) : ?>
            <tr>
                <td><?=h($product['id'])?></td>
                <td><?=h($product['name'])?></td>
                <td><img src="../<?=IMG_PATH . h($product['img'])?>" alt="<?=h($product['img'])?>"></td>
                <td><?=h($product['created_at'])?></td>
                <td><?=h($product['updated_at'])?></td>
                <td>
                    <p><a href="product_edit.php?id=<?=h($product['id'])?>" class="btn btn-sm">編集</a></p>
                    <p>
                        <form action="" method="post" onsubmit="return confirm('本当に削除しますか？')">
                            <input type="hidden" name="id" value="<?= h($product['id']) ?>">
                            <input type="submit" class="btn btn-sm" name="delete" value="削除">
                        </form>
                    </p>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</main>
<?php require_once('footer.html') ?>