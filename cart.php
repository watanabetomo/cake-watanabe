<?php
require_once('autoload.php');

if (!isset($_SESSION['user']['authenticated'])) {
    header('Location: login.php');
    exit;
}

try {
    $productModel = new ProductModel();
    $productDetailModel = new ProductDetailModel();
    $cartModel = new CartModel();
    if (isset($_POST['add_to_cart'])) {
        $productDetail = $productDetailModel->fetchById($_POST['detail_id']);
        $cartModel->addToCart($_POST['detail_id']);
    } elseif (isset($_POST['delete'])) {
        $cartModel->delete($_POST['id']);
    } elseif (isset($_POST['change'])) {
        if ($_POST['num'] > 0) {
            $cartModel->changeNum($_POST['num'], $_POST['id']);
        }elseif ($_POST['num'] == 0) {
            $cartModel->delete($_POST['id']);
        } else {
            $numError = '商品点数は0以上の数値を入力してください';
        }
    } elseif (isset($_POST['clear'])) {
        $cartModel->deleteFromCart();
    }
    $cart = $cartModel->fetchAll();
} catch (Exception $e) {
    $databaseError = '商品情報の取得に失敗しました。<br>カスタマーサポートにお問い合わせください。';
}

?>
<?php require_once('header.html')?>
<main>
    <p class="error"><?=isset($databaseError) ? $databaseError : ''?></p>
    <div class="wrapper">
        <div class="box1">
            <div class="card">
                <div class="card-body">
                    <form action="purchase_edit.php" method="post">
                        <?php if (!empty($cart['cart'])) :?>
                            <p class="purchase"><input type="submit" name="purchase" value="レジに進む" class="btn btn-success"></p>
                        <?php endif;?>
                        <p class="sub-title">合計金額（税込）</p>
                        <table class="table table-right">
                            <tr>
                                <th>小計</th>
                                <td><?=!empty($cart['cart']) ? number_format(h($cart['totalPrice'])) . '円' : ''?></td>
                            </tr>
                            <tr>
                                <th>商品点数</th>
                                <td><?=!empty($cart['cart']) ? h($cart['totalCount']) . '点' : ''?></td>
                            </tr>
                            <tr>
                                <th>送料</th>
                                <td><?=!empty($cart['cart']) ? number_format(h($cart['shipping'])) . '円' : ''?></td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>
        </div>
        <div class="box2">
            <p class="contents-title">カート</p>
            <p class="error"><?=isset($numError) ? $numError : ''?></p>
            <?php if (!empty($cart['cart'])) :?>
                <table class="table-bordered table-center cart">
                    <tr>
                        <th>
                            削除
                        </th>
                        <th>
                            商品画像
                        </th>
                        <th>
                            商品名
                        </th>
                        <th>
                            個数
                        </th>
                        <th>
                            サイズ
                        </th>
                        <th>
                            単価
                        </th>
                        <th>
                            税抜価格
                        </th>
                    </tr>
                    <?php foreach ($cart['cart'] as $item) :?>
                        <?php
                            $productDetail = $productDetailModel->fetchById($item['product_detail_id']);
                            $product = $productModel->fetchSingleDetail($productDetail['product_id']);
                        ?>
                        <tr>
                            <td>
                                <form action="cart.php" method="post">
                                    <input type="hidden" name="id" value="<?=$item['id']?>">
                                    <p style="margin: 10px;"><input type="submit" name="delete" value="削除"></p>
                                </form>
                            </td>
                            <td>
                                <?=isset($product['img']) ? '<img src="' . IMG_PATH . h($product['img']) . '" alt="' . h($product['img']) . '">' : '画像なし'?>
                            </td>
                            <td>
                                <?=h($product['name'])?>
                            </td>
                            <td>
                                <form action="cart.php" method="post">
                                    <input type="hidden" name="id" value="<?=$item['id']?>">
                                    <input type="number" name="num" value="<?=h($item['num'])?>" style="width: 70px; margin: 10px 10px;">
                                    <p><input type="submit" name="change" value="変更"></p>
                                </form>
                            </td>
                            <td>
                                <?=h($productDetail['size'])?>cm
                            </td>
                            <td>
                                <?=number_format(h($productDetail['price']))?>円
                            </td>
                            <td>
                                <?=number_format(h($item['num']) * h($productDetail['price']))?>円
                            </td>
                        </tr>
                    <?php endforeach;?>
                </table>
                <form action="" method="post">
                    <p class="submit-button"><a href="index.php" class="btn btn-primary">買い物を続ける</a> <input type="submit" class="btn btn-danger" name="clear" style="margin-left: 10px;" value="カートを空にする"></p>
                </form>
            <?php else:?>
                <p class="empty-message">現在、カートの中身は空です。</p>
            <?php endif;?>
        </div>
    </div>
</main>
<?php require_once('footer.html')?>