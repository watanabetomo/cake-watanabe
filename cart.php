<?php
require_once('admin/autoload.php');

$_SESSION['userName'] = 'watanabe';
$_SESSION['userId'] = 1;
if (!isset($_SESSION['authenticated'])) {
    header('Location: login.php');
    exit;
}

$totalPrice=0;
$countProduct=0;

try {
    $cartModel = new CartModel();
    $productModel = new ProductModel();
    $productDetailModel = new ProductDetailModel();
} catch (PDOException $e) {
    $error = 'データベースに接続できませんでした。';
}

if (isset($_POST['es_submit'])) {
    try {
        $productDetail = $productDetailModel->fetchById($_POST['detail_id']);
        try {
            $product = $productModel->fetchById($productDetail['product_id']);
            $cartModel->addToCart($_SESSION['userId'], $_POST['detail_id']);
        } catch (PDOException $e){
            $error = 'データベースに接続できませんでした。';
        }
    } catch (PDOException $e){
        $error = 'データベースに接続できませんでした。';
    }
} elseif (isset($_POST['continue'])) {
    header('Location: index.php');
    exit;
} elseif (isset($_POST['delete'])) {
    $cartModel->delete($_POST['deleteId']);
    header('Location: cart.php');
    exit;
} elseif (isset($_POST['change'])) {
    $cartModel->changeNum($_POST['num'], $_POST['id']);
    header('Location: cart.php');
    exit;
} elseif (isset($_POST['clear'])) {
    $cartModel->truncateCart();
    header('Location: cart.php');
    exit;
}

$cart = $cartModel->fetchAll();
foreach($cart as $onCart){
    $productDetail = $productDetailModel->fetchById($onCart['product_detail_id']);
    $countProduct += $onCart['num'];
    $totalPrice += $onCart['num'] * $productDetail['price'] * (TAX + 1);
}

?>
<?php require_once('header.html')?>
<main>
    <?php if (isset($error)): ?>
        <p class="error"><?=$error?></p>
    <?php endif; ?>
    <div class="wrapper">
        <div class="box1">
            <div class="card">
                <div class="card-body">
                    <form action="purchase_edit.php" method="post">
                        <p class="purchase"><input type="submit" name="purchase" value="レジに進む" class="btn btn-success"></p>
                        <h3 class="sub-title">合計金額（税込）</h3>
                        <table class="table table-right">
                            <tr>
                                <th>小計</th>
                                <td><?=number_format($totalPrice)?></td>
                            </tr>
                            <tr>
                                <th>商品点数</th>
                                <td><?=$countProduct?></td>
                            </tr>
                            <tr>
                                <th>送料</th>
                                <td><?=($totalPrice * (1 + TAX) > 10000) ? 0 : number_format(1000) ;?></td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>
        </div>
        <div class="box2">
            <p class="contents-title">カート</p>
            <?php if (!empty($cart)) :?>
            <table class="table-bordered table-center cart">
                <tr>
                    <th>削除</th>
                    <th>商品画像</th>
                    <th>商品名</th>
                    <th>個数</th>
                    <th>サイズ</th>
                    <th>単価</th>
                    <th>税抜価格</th>
                </tr>
                <?php foreach($cart as $onCart): ?>
                <?php
                    $productDetail = $productDetailModel->fetchById($onCart['product_detail_id']);
                    $product = $productModel->fetchSingleDetail($productDetail['product_id']);
                ?>
                <tr>
                    <td>
                        <form action="cart.php" method="post">
                            <input type="hidden" name="deleteId" value="<?=$onCart['id']?>">
                            <p style="margin: 10px;"><input type="submit" name="delete" value="削除"></p>
                        </form>
                    </td>
                    <td><img src="<?=IMG_PATH . $product['img']?>" alt="<?=$product['img']?>"></td>
                    <td><?=$product['name']?></td>
                    <td>
                        <form action="cart.php" method="post">
                            <input type="hidden" name="id" value="<?=$onCart['id']?>">
                            <input type="number" name="num" value="<?=$onCart['num']?>" style="width: 70px; margin: 10px 10px;">
                            <p><input type="submit" name="change" value="変更"></p>
                        </form>
                    </td>
                    <td><?=$productDetail['size']?>cm</td>
                    <td><?=number_format($productDetail['price'])?>円</td>
                    <td><?=number_format($onCart['num'] * $productDetail['price'])?>円</td>
                </tr>
                <?php endforeach; ?>
            </table>
            <form action="" method="post">
                <p class="submit-button"><input type="submit" class="btn btn-primary" name="continue" value="買い物を続ける"> <input type="submit" class="btn btn-danger" name="clear" value="カートを空にする"></p>
            </form>
            <?php else:?>
            <p>現在、カートの中身は空です。</p>
            <?php endif;?>
        </div>
    </div>
</main>
<?php require_once('footer.html')?>