<?php
require_once('autoload.php');

if (!isset($_SESSION['authenticated'])) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 1;
}

try {
    $orderModel = new OrderModel();
    $orders = $orderModel->pagination($page);
    $pageNum = $orderModel->countPage();
} catch (PDOException $e) {
    $error = 'データベースに接続できませんでした。';
}
?>
<?php require_once('header.html')?>
<main>
    <p class="contents-title">購入履歴一覧</p>
    <p class="error"><?=isset($error) ? $error : ''?></p>
    <?php if (empty($orders)) :?>
        <p class="done-message">購入履歴はありません。</p>
    <?php else :?>
        <table class="table">
            <?php foreach ($orders as $order) :?>
                <?php
                $orderDetailModel = new OrderDetailModel();
                $orderDetails = $orderDetailModel->getOrderDetail($order['id']);
            ?>
                <tr>
                    <td><?=(new DateTime($order['created_at']))->format('Y年 m月 d日')?><p>注文id：<?=$order['id']?></p></td>
                    <td>
                        <?php
                        $productDetailModel = new ProductDetailModel();
                        $productModel = new ProductModel();
                        foreach ($orderDetails as $orderDetail) {
                            $productDetail = $productDetailModel->fetchById($orderDetail['product_detail_id']);
                            $img = $productModel->getImg($productDetail['product_id']);
                            echo isset($img['img']) ? '<img src="' . IMG_PATH . $img['img'] . '" alt="' . $img['img'] . '"><br>' : '';
                        }
                        ?>
                    </td>
                    <td>
                        <?php foreach ($orderDetails as $orderDetail) :?>
                            <p class="product-name"><?=$orderDetail['name']?></p>
                            <p>
                                商品サイズ：<?=$orderDetail['size']?><br>
                                商品単価：<?=$orderDetail['price']?><br>
                                商品個数：<?=$orderDetail['num']?>
                            </p>
                            <form action="cart.php" method="post">
                                <input type="hidden" name="detail_id" value="<?=$orderDetail['product_detail_id']?>">
                                <p><input type="submit" name="es_submit" value="もう一度購入する"></p>
                            </form>
                        <?php endforeach;?>
                    </td>
                </tr>
            <?php endforeach;?>
        </table>
        <p class="page">
            <?php if ($page == 1) :?>
                前のページへ |
            <?php else :?>
                <a href="?page=<?=$page - 1?>">前のページへ</a> |
            <?php endif;?>
            <?php for ($i = 1; $i <= ceil($pageNum[0] / 5); $i++) :?>
                <?php if ($page == $i) :?>
                    <?=$i?> |
                <?php else :?>
                    <a href="?page=<?=$i?>"><?=$i?></a> |
                <?php endif;?>
            <?php endfor;?>
            <?php if ($page == ceil($pageNum[0] / 5)) :?>
                次のページへ
            <?php else :?>
                <a href="?page=<?=$page + 1?>">次のページへ</a>
            <?php endif;?>
        </p>
    <?php endif?>
</main>
<?php require_once('footer.html')?>