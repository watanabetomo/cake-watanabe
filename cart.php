<?php
require_once('autoload.php');

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
    if (isset($_POST['es_submit'])) {
        $productDetail = $productDetailModel->fetchById($_POST['detail_id']);
        $cartModel->addToCart($_SESSION['userId'], $_POST['detail_id']);
    } elseif (isset($_POST['continue'])) {
        header('Location: index.php');
        exit;
    } elseif (isset($_POST['delete'])) {
        $cartModel->delete($_POST['deleteId']);
    } elseif (isset($_POST['change'])) {
        if ($_POST['num'] > 0) {
            $cartModel->changeNum($_POST['num'], $_POST['id']);
        } else {
            $error['num'] = "商品点数は1以上の数値を入力してください";
        }
    } elseif (isset($_POST['clear'])) {
        $cartModel->truncateCart();
    }
    $cart = $cartModel->fetchAll();
    foreach($cart as $onCart){
        $productDetail = $productDetailModel->fetchById($onCart['product_detail_id']);
        $countProduct += $onCart['num'];
        $totalPrice += $onCart['num'] * $productDetail['price'] * (TAX + 1);
    }
} catch (PDOException $e) {
    $error = 'データベースに接続できませんでした。';
}


?>
<?php require_once('header.html')?>
<main>
    <p class="error"><?=isset($error) ? $error : ''?></p>
    <div class="wrapper">
        <div class="box1">
            <div class="card">
                <div class="card-body">
                    <form action="purchase_edit.php" method="post">
                        <?php if(!empty($cart)):?>
                        <p class="purchase"><input type="submit" name="purchase" value="レジに進む" class="btn btn-success"></p>
                        <?php endif;?>
                        <h3 class="sub-title">合計金額（税込）</h3>
                        <table class="table table-right">
                            <tr>
                                <th>小計</th>
                                <td><?=!empty($cart) ? number_format(floor($totalPrice)) : ''?></td>
                            </tr>
                            <tr>
                                <th>商品点数</th>
                                <td><?=!empty($cart) ? $countProduct : ''?></td>
                            </tr>
                            <tr>
                                <th>送料</th>
                                <td><?=!empty($cart) ? (($totalPrice > 10000) ? 0 : number_format(1000)) : ''?></td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>
        </div>
        <div class="box2">
            <p class="contents-title">カート</p>
            <?php if (isset($error['num'])):?>
                <p class="error"><?=$error['num']?></p>
            <?php endif;?>
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
                <p class="empty-message">現在、カートの中身は空です。</p>
            <?php endif;?>
        </div>
    </div>
</main>
<?php require_once('footer.html')?>