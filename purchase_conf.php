<?php
require_once('autoload.php');

if (!isset($_SESSION['user']['authenticated'])) {
    header('Location: login.php');
    exit;
}

if ((isset($_POST['token']) ? $_POST['token'] : '') != $_SESSION['token']) {
    header('Location: error.php?error=param');
    exit;
}

if (isset($_POST['submit'])) {
    if ($_POST['sendFor'] == 1) {
        if ($_POST['postal_code1'] == '') {
            $error['postal_code1'] = '郵便番号上3桁が入力されていません。';
        } elseif (!preg_match('/^[0-9]{3}$/', $_POST['postal_code1'])) {
            $error['postal_code1'] = '郵便番号上3桁は3桁の数字を入力してください。';
        }
        if ($_POST['postal_code2'] == '') {
            $error['postal_code2'] = '郵便番号下4桁が入力されていません。';
        } elseif (!preg_match('/^[0-9]{4}$/', $_POST['postal_code2'])) {
            $error['postal_code2'] = '郵便番号下4桁は4桁の数字を入力してください。';
        }
        if ($_POST['city'] == '') {
            $error['city'] = '市区町村が入力されていません。';
        } elseif (!preg_match('/^[ぁ-んァ-ヶ一-龠]{1,15}$/u', $_POST['city'])) {
            $error['city'] = '市区町村は15文字以内の日本語で入力してください。';
        }
        if ($_POST['address'] == '') {
            $error['address'] = '番地が入力されていません。';
        } elseif (!preg_match('/^[0-9A-Za-zぁ-んァ-ヶー一-龠\-]{1,100}$/u', $_POST['address'])) {
            $error['address'] = '番地は100文字以内で入力してください。';
        }
        if (!preg_match('/^[0-9A-Za-zぁ-んァ-ヶー一-龠\-]{0,100}$/u', $_POST['other'])) {
            $error['other'] = '建物名等は100文字以内で入力してください。';
        }
        if ($_POST['tel1'] == '') {
            $error['tel1'] = '市外局番が入力されていません。';
        } elseif (!preg_match('/^[0-9]{1,5}$/', $_POST['tel1'])) {
            $error['tel1'] = '市外局番は1から5桁の数字を入力してください。';
        }
        if ($_POST['tel2'] == '') {
            $error['tel2'] = '電話番号（入力欄2）が入力されていません。';
        } elseif (!preg_match('/^[0-9]{1,5}$/', $_POST['tel2'])) {
            $error['tel2'] = '電話番号（入力欄2）は1から5桁の数字を入力してください。';
        }
        if ($_POST['tel3'] == '') {
            $error['tel3'] = '電話番号（入力欄3）が入力されていません。';
        } elseif (!preg_match('/^[0-9]{1,5}$/', $_POST['tel3'])) {
            $error['tel3'] = '電話番号（入力欄3）は1から5桁の数字を入力してください。';
        }
        if ($_POST['name_kana'] == '') {
            $error['name_kana'] = 'フリガナが入力されていません。';
        } elseif (!preg_match('/^[ァ-ヶ]{1,20}$/u', $_POST['name_kana'])) {
            $error['name_kana'] = 'フリガナは20文字以内の全角カタカナで入力してください。';
        }
        if ($_POST['name'] == '') {
            $error['name'] = '名前が入力されていません。';
        } elseif (!preg_match('/^[A-Za-zぁ-んァ-ヶ一-龠]{1,15}$/u', $_POST['name'])) {
            $error['name'] = '名前は15文字以内で入力してください。';
        }
    }
    if (isset($error)) {
        $_POST['action'] = 'fix';
        require_once('purchase_edit.php');
        exit;
    }
}

try {
    $productModel = new ProductModel();

    $productDetailModel = new ProductDetailmodel();

    $cartModel = new CartModel();
    $cart = $cartModel->fetchAll();

    $paymentModel = new MPaymentModel();
    $payment = $paymentModel->fetchById($_POST['payment']);

    $userModel = new UserModel();
    $user = $userModel->fetchById($_SESSION['user']['user_id']);
    $user['pref'] = $prefectures[$user['pref']];
} catch (Exception $e) {
    header('Location: error.php?error=database');
    exit;
}

$purchaseInfo = $_POST + $user;

?>

<?php require_once('header.html')?>
<main>
    <p class="error"><?=isset($databaseError) ? $dataBaseError : ''?></p>
    <p class="contents-title">確認</p>
    <table class="table table-bordered table-center">
        <tr>
            <th>商品画像</th>
            <th>商品名</th>
            <th>個数</th>
            <th>サイズ</th>
            <th>単価</th>
            <th>税抜価格</th>
        </tr>
        <?php foreach ($cart['cart'] as $item) :?>
            <?php
                $productDetail = $productDetailModel->fetchById($item['product_detail_id']);
                $product = $productModel->fetchSingleDetail($productDetail['product_id']);
            ?>
            <tr>
                <td><?=isset($product['img']) ? '<img src="' . IMG_PATH . h($product['img']) . '" alt="' . h($product['img']) . '">' : '画像なし'?></td>
                <td><?=h($product['name'])?></td>
                <td><?=h($item['num'])?></td>
                <td><?=h($productDetail['size'])?>cm</td>
                <td><?=number_format(h($productDetail['price']))?>円</td>
                <td><?=number_format(h($item['num']) * h($productDetail['price']))?>円</td>
            </tr>
        <?php endforeach;?>
        <tr>
            <td colspan="2">小計</td>
            <td><?=h($cart['total_count'])?></td>
            <td></td>
            <td></td>
            <td><?=number_format(h($cart['total_price']))?>円</td>
        </tr>
        <tr>
            <td colspan="5">消費税</td>
            <td><?=number_format(floor(h($cart['total_price']) * TAX))?>円</td>
        </tr>
        <tr>
            <td colspan="5">送料（税込み）</td>
            <td><?=number_format(h($cart['shipping']))?>円</td>
        </tr>
        <tr>
            <td colspan="5">総合計</td>
            <td><?=number_format(floor(h($cart['total_price']) * (1 + TAX) + h($cart['shipping'])))?>円</td>
        </tr>
    </table>
    <p class="contents-title">送付先情報</p>
    <table class="table table-left">
        <tr>
            <th>
                郵便番号
            </th>
            <td>
                <?=h($purchaseInfo['postal_code1']) . ' - ' . h($purchaseInfo['postal_code2'])?>
            </td>
        </tr>
        <tr>
            <th>
                住所
            </th>
            <td>
                <?=h($purchaseInfo['pref']) . h($purchaseInfo['city']) . h($purchaseInfo['address']) . h($purchaseInfo['other'])?>
            </td>
        </tr>
        <tr>
            <th>
                電話番号
            </th>
            <td>
                <?=h($purchaseInfo['tel1']) . ' - ' . h($purchaseInfo['tel2']) . ' - ' . h($purchaseInfo['tel3'])?>
            </td>
        </tr>
        <tr>
            <th>
                お名前
            </th>
            <td>
                <p><?=h($purchaseInfo['name_kana'])?></p>
                <p><?=h($purchaseInfo['name'])?></p>
            </td>
        </tr>
    </table>
    <p class="contents-title">請求先情報</p>
    <table class="table table-left">
        <tr>
            <th>
                郵便番号
            </th>
            <td>
                <?=h($user['postal_code1']) . ' - ' . h($user['postal_code2'])?>
            </td>
        </tr>
        <tr>
            <th>
                住所
            </th>
            <td>
                <?=h($user['pref']) . h($user['city']) . h($user['address']) . h($user['other'])?>
            </td>
        </tr>
        <tr>
            <th>
                電話番号
            </th>
            <td>
                <?=h($user['tel1']) . ' - ' . h($user['tel2']) . ' - ' . h($user['tel3'])?>
            </td>
        </tr>
        <tr>
            <th>
                メールアドレス
            </th>
            <td>
                <?=h($user['mail'])?>
            </td>
        </tr>
        <tr>
            <th>
                お名前
            </th>
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
            <td><?=h($payment['name'])?></td>
        </tr>
    </table>
    <ul class="form">
        <li>
            <form action="purchase_done.php" method="post">
                <?php foreach ($purchaseInfo as $key => $value) :?>
                    <input type="hidden" name="<?=$key?>" value="<?=$value?>">
                <?php endforeach;?>
                <p><input type="submit" name="send" class="btn btn-success" value="購入する"></p>
            </form>
        </li>
        <li>
            <form action="purchase_edit.php?#address" method="post">
                <?php foreach ($purchaseInfo as $key => $value) :?>
                    <input type="hidden" name="<?=$key?>" value="<?=$value?>">
                <?php endforeach;?>
                <p><input type="submit" name="fix" class="btn btn-danger" value="修正する"></p>
            </form>
        </li>
    </ul>
</main>
<?php require_once('footer.html')?>