<?php
require_once('autoload.php');

if (!isset($_SESSION['user']['authenticated'])) {
    header('Location: login.php');
    exit;
}

if ((isset($_SESSION['purchase_info']['token']) ? $_SESSION['purchase_info']['token'] : '') != getToken()) {
    header('Location: purchase_edit.php');
    exit;
}

try {
    $paymentModel = new MPaymentModel();
    $payment = $paymentModel->fetchById($_SESSION['purchase_info']['payment']);
    $productDetailModel = new ProductDetailmodel();
    $productModel = new ProductModel();
    $cartModel = new CartModel();
    $cart = $cartModel->fetchAll();
    $userModel = new UserModel();
    $user = $userModel->fetchById($_SESSION['user']['userId']);
    $user['pref'] = $prefectures[$user['pref']];
} catch (Exception $e) {
    $error = '商品情報の取得に失敗しました。<br>カスタマーサポートにお問い合わせください。';
}

$purchaseInfo = $_SESSION['purchase_info'] + $user;

?>

<?php require_once('header.html')?>
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
        <?php foreach ($cart as $prodOfTheCart) :?>
            <?php
                $productDetail = $productDetailModel->fetchById($prodOfTheCart['product_detail_id']);
                $product = $productModel->fetchSingleDetail($productDetail['product_id']);
            ?>
            <tr>
                <td><?=isset($product['img']) ? '<img src="' . IMG_PATH . h($product['img']) . '" alt="' . h($product['img']) . '">' : '画像なし'?></td>
                <td><?=h($product['name'])?></td>
                <td><?=h($prodOfTheCart['num'])?></td>
                <td><?=h($productDetail['size'])?>cm</td>
                <td><?=number_format(h($productDetail['price']))?>円</td>
                <td><?=number_format(h($prodOfTheCart['num']) * h($productDetail['price']))?>円</td>
            </tr>
            <?php
                $totalPrice += $productDetail['price'] * $prodOfTheCart['num'];
                $totalCount += $prodOfTheCart['num'];
            ?>
        <?php endforeach;?>
        <?php $shipping = ($totalPrice * (1 + TAX) > 10000) ? 0 : 1000?>
        <tr>
            <td colspan="2">小計</td>
            <td><?=$totalCount?></td>
            <td></td>
            <td></td>
            <td><?=number_format($totalPrice)?>円</td>
        </tr>
        <tr>
            <td colspan="5">消費税</td>
            <td><?=number_format(floor($totalPrice * TAX))?>円</td>
        </tr>
        <tr>
            <td colspan="5">送料（税込み）</td>
            <td><?=number_format($shipping)?>円</td>
        </tr>
        <tr>
            <td colspan="5">総合計</td>
            <td><?=number_format(floor($totalPrice * (1 + TAX) + $shipping))?>円</td>
        </tr>
    </table>
    <p class="contents-title">送付先情報</p>
    <table class="table table-left">
        <tr>
            <th>郵便番号</th>
            <td><?=$purchaseInfo['postal_code1'] . ' - ' . $purchaseInfo['postal_code2']?></td>
        </tr>
        <tr>
            <th>住所</th>
            <td><?=$purchaseInfo['pref'] . $purchaseInfo['city'] . $purchaseInfo['address'] . $purchaseInfo['other']?></td>
        </tr>
        <tr>
            <th>電話番号</th>
            <td><?=$purchaseInfo['tel1'] . ' - ' . $purchaseInfo['tel2'] . ' - ' . $purchaseInfo['tel3']?></td>
        </tr>
        <tr>
            <th>お名前</th>
            <td>
                <p><?=$purchaseInfo['name_kana']?></p>
                <p><?=$purchaseInfo['name']?></p>
            </td>
        </tr>
    </table>
    <p class="contents-title">請求先情報</p>
    <table class="table table-left">
        <tr>
            <th>郵便番号</th>
            <td><?=h($user['postal_code1']) . ' - ' . h($user['postal_code2'])?></td>
        </tr>
        <tr>
            <th>住所</th>
            <td><?=h($user['pref']) . h($user['city']) . h($user['address']) . h($user['other'])?></td>
        </tr>
        <tr>
            <th>電話番号</th>
            <td><?=h($user['tel1']) . ' - ' . h($user['tel2']) . ' - ' . h($user['tel3'])?></td>
        </tr>
        <tr>
            <th>メールアドレス</th>
            <td><?=h($user['mail'])?></td>
        </tr>
        <tr>
            <th>お名前</th>
            <td>
                <p><?=h($user['name_kana'])?></p>
                <p><?=h($user['name'])?></p>
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
    <ul class="form">
        <li>
            <form action="purchase_done.php" method="post">
                <p><input type="submit" name="send" class="btn btn-success" value="購入する"></p>
            </form>
        </li>
        <li>
            <form action="purchase_edit.php?action=fix#address" method="post">
                <?php foreach ($purchaseInfo as $key => $value) :?>
                    <input type="hidden" name="<?=$key?>" value="<?=$value?>">
                <?php endforeach;?>
                <p><input type="submit" name="fix" class="btn btn-danger" value="修正する"></p>
            </form>
        </li>
    </ul>
</main>
<?php require_once('footer.html')?>