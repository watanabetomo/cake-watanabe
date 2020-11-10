<?php
require_once('autoload.php');

if (!isset($_SESSION['authenticated'])) {
    header('Location: login.php');
    exit;
}

if ((isset($_SESSION['purchase_info']['token']) ? $_SESSION['purchase_info']['token'] : '') != getToken()) {
    header('Location: purchase_edit.php');
    exit;
}

try {
    $paymentModel = new MPaymentModel();
    $payments = $paymentModel->fetchAll();
    $productDetailModel = new ProductDetailmodel();
    $productModel = new ProductModel();
    $cartModel = new CartModel();
    $cart = $cartModel->fetchAll();
    $userModel = new UserModel();
    $user = $userModel->fetchById($_SESSION['userId']);
    $mPaymentModel = new MPaymentModel();
    $payment = $mPaymentModel->fetchByid($_SESSION['purchase_info']['payment']);
} catch (PDOException $e) {
    $error = 'データベースに接続できませんでした。';
}

if (isset($_POST['send'])) {
    header('Location: purchase_done.php');
    exit;
} elseif (isset($_POST['cancel'])) {
    header('Location: purchase_edit.php');
    exit;
}
?>

<?php require_once('header.html') ?>
<main>
    <p class="contents-title">確認</p>
    <p class="error"><?=isset($error) ? $error : ''?></p>
    <table class="table table-bordered table-center">
        <tr>
            <th>商品画像</th>
            <th>商品名</th>
            <th>個数</th>
            <th>サイズ</th>
            <th>単価</th>
            <th>税抜価格</th>
        </tr>
        <?php foreach($cart as $onCart):?>
            <?php
                $productDetail = $productDetailModel->fetchById($onCart['product_detail_id']);
                $product = $productModel->fetchSingleDetail($productDetail['product_id']);
            ?>
            <tr>
                <td><img src="<?=IMG_PATH . $product['img']?>" alt="<?=$product['img']?>"></td>
                <td><?=$product['name']?></td>
                <td><?=$onCart['num']?></td>
                <td><?=$productDetail['size']?></td>
                <td><?=number_format($productDetail['price'])?></td>
                <td><?=number_format($onCart['num'] * $productDetail['price'])?></td>
            </tr>
            <?php
                $totalPrice += $productDetail['price'] * $onCart['num'];
                $totalCount += $onCart['num'];
            ?>
        <?php endforeach;?>
        <?php $shipping = ($totalPrice * (1 + TAX) > 10000) ? 0 : 1000?>
        <tr>
            <td colspan="2">小計</td>
            <td><?=$totalCount?></td>
            <td></td>
            <td></td>
            <td><?=number_format($totalPrice)?></td>
        </tr>
        <tr>
            <td colspan="5">消費税</td>
            <td><?=number_format(floor($totalPrice * TAX))?></td>
        </tr>
        <tr>
            <td colspan="5">送料（税込み）</td>
            <td><?=number_format($shipping)?></td>
        </tr>
        <tr>
            <td colspan="5">総合計</td>
            <td><?=number_format(floor($totalPrice * (1 + TAX) + $shipping))?></td>
        </tr>
    </table>
    <p class="contents-title">送付先情報</p>
    <table class="table table-left">
        <tr>
            <th>郵便番号</th>
            <td><?=$_SESSION['purchase_info']['postal_code1']?> - <?=$_SESSION['purchase_info']['postal_code2']?></td>
        </tr>
        <tr>
            <th>住所</th>
            <td><?=$_SESSION['purchase_info']['pref'] . $_SESSION['purchase_info']['city'] . $_SESSION['purchase_info']['address'] . $_SESSION['purchase_info']['other']?></td>
        </tr>
        <tr>
            <th>電話番号</th>
            <td><?=$_SESSION['purchase_info']['tel1']?> - <?=$_SESSION['purchase_info']['tel2']?> - <?=$_SESSION['purchase_info']['tel3']?></td>
        </tr>
        <tr>
            <th>お名前</th>
            <td>
                <p><?=$_SESSION['purchase_info']['name_kana']?></p>
                <p><?=$_SESSION['purchase_info']['name']?></p>
            </td>
        </tr>
    </table>
    <p class="contents-title">請求先情報</p>
    <table class="table table-left">
        <tr>
            <th>郵便番号</th>
            <td><?=$user['postal_code1']?> - <?=$user['postal_code2']?></td>
        </tr>
        <tr>
            <th>住所</th>
            <td><?=$prefectures[$user['pref']] . $user['city'] . $user['address'] . $user['other']?></td>
        </tr>
        <tr>
            <th>電話番号</th>
            <td><?=$user['tel1']?> - <?=$user['tel2']?> - <?=$user['tel3']?></td>
        </tr>
        <tr>
            <th>メールアドレス</th>
            <td><?=$user['mail']?></td>
        </tr>
        <tr>
            <th>お名前</th>
            <td>
                <p><?=$user['name_kana']?></p>
                <p><?=$user['name']?></p>
            </td>
        </tr>
    </table>
    <p class="contents-title">お支払方法</p>
    <table class="table table-left">
        <tr>
            <th>支払方法</th>
            <td><?=$payment['name']?></td>
        </tr>
    </table>
    <form action="" method="post">
        <p class="purchase-button"><input type="submit" name="send" value="購入する"> <input type="submit" name="cancel" value="修正する"></p>
    </form>
</main>
<?php require_once('footer.html') ?>