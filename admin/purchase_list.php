<?php
require_once('../autoload.php');

if (!isset($_SESSION['admin']['authenticated'])) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 1;
}

try {
    $orderDetailModel = new OrderDetailModel();
    $orderModel = new OrderModel();
    $orders = $orderModel->pagination($page);
    $pageNum = $orderModel->countPage();
} catch (PDOException $e) {
    $error = 'データベースに接続できませんでした。<br>カスタマーサポートにお問い合わせください。';
}

if (isset($_POST['cancel'])) {
    try {
        $orderModel->cancel($_POST['id']);
    } catch (PDOException $e) {
        $error = '注文情報のキャンセルに失敗しました。<br>カスタマーサポートにお問い合わせください。';
    }
}

?>

<?php require_once('admin_header.html')?>
<link rel="stylesheet" href="../css/admin_purchase_list.css">
<main>
    <?php getPage()?>
    <p class="error"><?=isset($error) ? $error : ''?></p>
    <?php if (empty($orders)) :?>
        <p class="done-message">注文情報はありません。</p>
    <?php else :?>
        <table class="purchase-list" border="1">
            <tr>
                <th>
                    注文日
                </th>
                <th>
                    画像
                </th>
                <th>
                    商品詳細
                </th>
                <th>
                    キャンセル
                </th>
            </tr>
            <?php foreach ($orders as $order) :?>
                <?php
                    $orderDetails = $orderDetailModel->getOrderDetail($order['id']);
                ?>
                <tr>
                    <td>
                        <?=(new DateTime($order['created_at']))->format('Y年 m月 d日')?>
                    </td>
                    <td>
                        <?php
                        $productDetailModel = new ProductDetailModel();
                        $productModel = new ProductModel();
                        foreach ($orderDetails as $orderDetail) {
                            $productDetail = $productDetailModel->fetchById($orderDetail['product_detail_id']);
                            $img = $productModel->getImg($productDetail['product_id']);
                            echo isset($img) ? '<img src="../' . IMG_PATH . $img . '" alt="' . $img . '"><br>' : '';
                        }
                        ?>
                    </td>
                    <td>
                        <?php foreach ($orderDetails as $orderDetail) :?>
                            <p class="product-name"><strong><?=$orderDetail['name']?></strong></p>
                            <p>
                                商品サイズ：<?=$orderDetail['size']?>cm<br>
                                商品単価：<?=$orderDetail['price']?>円<br>
                                商品個数：<?=$orderDetail['num']?>個
                            </p>
                        <?php endforeach;?>
                    </td>
                    <td>
                        <form action="" method="post">
                            <input type="hidden" name="id" value="<?=$order['id']?>">
                            <p><input type="submit" name="cancel" value="キャンセル"></p>
                        </form>
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
            <?php for ($i = 1; $i <= ceil($pageNum / 5); $i++) :?>
                <?php if ($page == $i) :?>
                    <?=$i?> |
                <?php else :?>
                    <a href="?page=<?=$i?>"><?=$i?></a> |
                <?php endif;?>
            <?php endfor;?>
            <?php if ($page == ceil($pageNum / 5)) :?>
                次のページへ
            <?php else :?>
                <a href="?page=<?=$page + 1?>">次のページへ</a>
            <?php endif;?>
        </p>
    <?php endif?>
</main>
<?php require_once('admin_footer.html')?>
