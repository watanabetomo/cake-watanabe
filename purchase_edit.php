<?php
require_once('autoload.php');

if (!isset($_SESSION['user']['authenticated'])) {
    header('Location: login.php');
    exit;
}

if (
    !isset($_POST['purchase'])
    and !isset($_POST['fix'])
    and !isset($_POST['submit'])
    and !isset($_POST['address_search'])
) {
    header('Location: error.php?error=param');
    exit;
}

try {
    $productModel = new ProductModel();

    $productDetailModel = new ProductDetailmodel();

    $cartModel = new CartModel();
    $cart = $cartModel->fetchAll();

    $paymentModel = new MPaymentModel();
    $payments = $paymentModel->fetchAll();

    $userModel = new UserModel();
    $user = $userModel->fetchById($_SESSION['user']['user_id']);
} catch (Exception $e) {
    header('Location: error.php?error=database');
    exit;
}

$hitAddress = [];

if (isset($_POST['address_search'])) {
    if ($_POST['postal_code1'] == '') {
        $error['postal_code1'] = '郵便番号上3桁が入力されていません。';
    } elseif (!preg_match('/^[0-9]{3}$/', $_POST['postal_code1'])) {
        $error['postal_code1'] = '郵便番号上3桁は3桁の半角数字を入力してください。';
    }
    if ($_POST['postal_code2'] == '') {
        $error['postal_code2'] = '郵便番号下4桁が入力されていません。';
    } elseif (!preg_match('/^[0-9]{4}$/', $_POST['postal_code2'])) {
        $error['postal_code2'] = '郵便番号下4桁は4桁の半角数字を入力してください。';
    }
    if (!isset($error)) {
        $postal_code = $_POST['postal_code1'] . $_POST['postal_code2'];
        $json = json_decode(file_get_contents('https://zip-cloud.appspot.com/api/search?zipcode=' . $postal_code), true);
        if (!empty($json['results'][0])) {
            $hitAddress = $json['results'][0];
            $hitAddress['pref'] = $hitAddress['prefcode'];
            $hitAddress['city'] = $hitAddress['address2'] . $hitAddress['address3'];
            $hitAddress['address'] = '';
            $hitAddress['other'] = '';
        } else {
            $addressSearchError = '一致する住所がありません。';
        }
    }
}

$address = $hitAddress + $_POST + $user;
$checkedPayment = isset($_POST['payment']) ? $_POST['payment'] : '1';

?>

<?php require_once('header.html')?>
<main>
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
    <form action="purchase_conf.php#address" method="post">
        <input type="hidden" name="token" value="<?=getToken()?>">
        <input type="hidden" name="action" value="fix">
        <input type="hidden" name="sub_price" value="<?=number_format($cart['total_price'])?>">
        <input type="hidden" name="tax_price" value="<?=number_format(floor($cart['total_price'] * TAX))?>">
        <input type="hidden" name="shipping" value="<?=number_format($cart['shipping'])?>">
        <input type="hidden" name="total_price" value="<?=number_format(floor($cart['total_price'] * (1 + TAX) + $cart['shipping']))?>">
        <p class="contents-title" id="address">送付先情報<span class="sub-message">※登録住所以外へ送る場合は変更してください</span></p>
        <p class="toggle-radio"><input type="radio" name="sendFor" id="sendFor1" value="1"<?=(isset($_POST['action']) and $_POST['action'] == 'fix') ? ' checked' : ''?>>変更する <input type="radio" name="sendFor" id="sendFor2" value="2"<?=!(isset($_POST['action']) and $_POST['action'] == 'fix') ? ' checked' : ''?>>変更しない</p>
        <table class="table send-for table-left"<?=!(isset($_POST['action']) and $_POST['action'] == 'fix') ? ' style="display: none;"' : ''?>>
            <tr>
                <th>
                    郵便番号
                </th>
                <td>
                    <input type="text" id="postal_code1" name="postal_code1" value="<?=h($address['postal_code1'])?>"> - <input type="text" id="postal_code2" name="postal_code2" value="<?=h($address['postal_code2'])?>">
                    <input type="submit" name="address_search" formaction="purchase_edit.php?#address" formmethod="POST" value="住所検索">
                    <span class="error"><?=isset($error['postal_code1']) ? '<br>' . $error['postal_code1'] : ''?><?=isset($error['postal_code2']) ? '<br>' . $error['postal_code2'] : ''?><?=isset($addressSearchError) ? '<br>' . $addressSearchError : ''?></span>
                </td>
            </tr>
            <tr>
                <th>
                    住所
                </th>
                <td>
                    <p>
                        <select name="pref">
                            <?php foreach (PREFECTURES as $key => $value) :?>
                                <option value="<?=$key?>"<?=PREFECTURES[$address['pref']] == $value ? ' selected' : ''?>><?=$value?></option>
                            <?php endforeach;?>
                        </select>
                    </p>
                    <p><input type="text" id="city" name="city" value="<?=h($address['city'])?>"><span class="error"><?=isset($error['city']) ? $error['city'] : ''?></span></p>
                    <p><input type="text" id="address" name="address" value="<?=h($address['address'])?>"><span class="error"><?=isset($error['address']) ? $error['address'] : ''?></span></p>
                    <p><input type="text" name="other" value="<?=h($address['other'])?>"><span class="error"><?=isset($error['other']) ? $error['other'] : ''?></span></p>
                </td>
            </tr>
            <tr>
                <th>
                    電話番号
                </th>
                <td>
                    <p>
                        <input type="text" name="tel1" value="<?=h($address['tel1'])?>"> - <input type="text" name="tel2" value="<?=h($address['tel2'])?>"> - <input type="text" name="tel3" value="<?=h($address['tel3'])?>">
                        <span class="error"><?=isset($error['tel1']) ? '<br>' . $error['tel1'] : ''?><?=isset($error['tel2']) ? '<br>' . $error['tel2'] : ''?><?=isset($error['tel3']) ? '<br>' . $error['tel3'] : ''?></span>
                    </p>
                </td>
            </tr>
            <tr>
                <th>
                    お名前
                </th>
                <td>
                    <p><input type="text" name="name_kana" value="<?=h($address['name_kana'])?>"><span class="error"><?=isset($error['name_kana']) ? $error['name_kana'] : ''?></span></p>
                    <p><input type="text" name="name" value="<?=h($address['name'])?>"><span class="error"><?=isset($error['name']) ? $error['name'] : ''?></span></p>
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
                    <?=PREFECTURES[h($user['pref'])] . h($user['city']) . h($user['address']) . h($user['other'])?>
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
                <th>
                    支払方法
                </th>
                <td>
                    <?php foreach ($payments as $payment) :?>
                        <input type="radio" name="payment" class="radio" value="<?=h($payment['id'])?>"<?=($payment['id'] == $checkedPayment) ? ' checked' : ''?>><?=$payment['name']?>
                    <?php endforeach;?>
                </td>
            </tr>
        </table>
        <p class="purchase-button"><input type="submit" name="submit" class="btn btn-success" value="確認画面へ"></p>
    </form>
</main>
<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script type="text/javascript">
    $('#sendFor1').click(function() {
        $('.send-for').show();
    });
    $('#sendFor2').click(function() {
        $('.send-for').hide();
    });
</script>
<?php require_once('footer.html') ?>