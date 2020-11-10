<?php
require_once('autoload.php');

if (!isset($_SESSION['authenticated'])) {
    header('Location: login.php');
    exit;
}

try {
    $orderModel = new OrderModel();
    $orders = $orderModel->fetchAll();
} catch (PDOException $e) {
    $error = 'データベースに接続できませんでした。';
}
?>
<?php require_once('header.html') ?>
<main>
    <p class="contents-title">購入履歴一覧</p>
    <p class="error"><?=isset($error) ? $error : ''?></p>
    <?php if(empty($orders)):?>
        <p class="done-message">購入履歴はありません。</p>
    <?php else:?>
        <table class="table">
            <?php foreach($orders as $order):?>
                <tr>
                    <td><?=(new DateTime($order['created_at']))->format('Y年 m月 d日')?></td>
                    <td>
                        <?php
                            $productDetailModel = new ProductDetailModel();
                            $productDetail = $productDetailModel->fetchById($order['product_detail_id']);
                            $productModel = new ProductModel();
                            $img = $productModel->getImg($productDetail['product_id']);
                            echo isset($img['img']) ? '<img src="' . IMG_PATH . $img['img'] . '" alt="' . $img['img'] . '">' : '';
                        ?>
                    </td>
                    <td>
                        <p class="product-name"><?=$order['name']?></p>
                        <p>商品サイズ：<?=$order['size']?></p>
                        <p>商品単価：<?=$order['price']?></p>
                        <p>商品個数：<?=$order['num']?></p>
                    </td>
                    <td>
                        <form action="cart" method="post">
                            <input type="hidden" name="detail_id" value="<?=$order['product_detail_id']?>">
                            <p><input type="submit" name="es_submit" value="もう一度購入する"></p>
                        </form>
                    </td>
                </tr>
            <?php endforeach;?>
        </table>
    <?php endif?>
</main>
<?php require_once('footer.html') ?>