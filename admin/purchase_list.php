<?php
require_once('../autoload.php');

if (!isset($_SESSION['admin']['authenticated'])) {
    header('Location: login.php');
    exit;
}

try {
    $orderDetailModel = new OrderDetailModel();

    $orderModel = new OrderModel();
    if (isset($_POST['cancel'])) {
        $orderModel->cancel($_POST['id']);
    }

    if (isset($_GET['page'])) {
        $page = $_GET['page'];
    } else {
        $page = 1;
    }
    $orders = $orderModel->paginate($page);
    $pageNum = $orderModel->countPage();
    $numPerPage = 5;
} catch (PDOException $e) {
    $error = 'データベースに接続できませんでした。<br>カスタマーサポートにお問い合わせください。';
}


?>

<?php require_once('admin_header.html')?>
<link rel="stylesheet" href="../css/admin_purchase_list.css">
<main>
    <?php getPage()?>
    <?php if (isset($error)) :?>
        <p class="error"><?=$error?></p>
    <?php elseif (empty($orders)) :?>
        <p class="message">注文情報はありません。</p>
    <?php else :?>
        <table class="purchase-list" border="1">
            <tr>
                <th>
                    <form action="" method="get">
                        <button type="submit" name="order" class="icon" value="ASC">▲</button>
                        <p class="sorted">注文日時</p>
                        <button type="submit" name="order" class="icon" value="DESC">▼</button>
                    </form>
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
                        <?=(new DateTime($order['created_at']))->format('Y-m-d H:i:s')?>
                    </td>
                    <td>
                        <?php
                            $productDetailModel = new ProductDetailModel();
                            $productModel = new ProductModel();
                            foreach ($orderDetails as $orderDetail) {
                                $productDetail = $productDetailModel->fetchById($orderDetail['product_detail_id']);
                                $img = $productModel->getImg($productDetail['product_id']);
                                echo isset($img) ? '<img src="../' . IMG_PATH . $img . '" alt="' . $img . '"><br>' : '画像がありません';
                            }
                        ?>
                    </td>
                    <td>
                        <?php foreach ($orderDetails as $orderDetail) :?>
                            <p class="product-name"><strong><?=$orderDetail['name']?></strong></p>
                            <p>
                                商品サイズ：<?=$orderDetail['size']?>cm<br>
                                商品単価：<?=$orderDetail['price']?>円<br>
                                注文個数：<?=$orderDetail['num']?>個
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
        <?php if ($pageNum > $numPerPage) :?>
            <p class="page">
                <?php if ($page == 1) :?>
                    前のページへ |
                <?php else :?>
                    <a href="?page=<?=$page - 1?>">前のページへ</a> |
                <?php endif;?>
                <?php for ($i = 1; $i <= ceil($pageNum / $numPerPage); $i++) :?>
                    <?php if ($page == $i) :?>
                        <?=$i?> |
                    <?php else :?>
                        <a href="?page=<?=$i?>"><?=$i?></a> |
                    <?php endif;?>
                <?php endfor;?>
                <?php if ($page == ceil($pageNum / $numPerPage)) :?>
                    次のページへ
                <?php else :?>
                    <a href="?page=<?=$page + 1?>">次のページへ</a>
                <?php endif;?>
            </p>
        <?php endif;?>
    <?php endif;?>
</main>
<?php require_once('admin_footer.html')?>
