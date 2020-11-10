<?php
require_once('autoload.php');

if (!isset($_SESSION['authenticated'])) {
    header('Location: login.php');
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
} catch (PDOException $e) {
    $error['database'] = 'データベースに接続できませんでした。';
}

if (isset($_POST['send'])) {
    if ($_POST['postal_code1'] == '') {
        $error['postal_code1'] = '郵便番号上3桁が入力されていません。';
    } elseif (!preg_match('/^[0-9]{3}$/', $_POST['postal_code1'])) {
        $error['postal_code1'] = '郵便番号上3桁が間違っています。';
    }
    if ($_POST['postal_code2'] == '') {
        $error['postal_code2'] = '郵便番号下4桁が入力されていません。';
    } elseif (!preg_match('/^[0-9]{4}$/', $_POST['postal_code2'])) {
        $error['postal_code2'] = '郵便番号下4桁が間違っています。';
    }
    if ($_POST['city'] == '') {
        $error['city'] = '市区町村が入力されていません。';
    } elseif (!preg_match('/^[0-9A-Za-zぁ-んァ-ヶー一-龠]{1,15}$/u', $_POST['city'])) {
        $error['city'] = '市区町村が間違っています。';
    }
    if ($_POST['address'] == '') {
        $error['address'] = '番地が入力されていません。';
    } elseif (!preg_match('/^[0-9A-Za-zぁ-んァ-ヶー一-龠\-]{1,100}$/u', $_POST['address'])) {
        $error['address'] = '番地が間違っています。';
    }
    if (!preg_match('/^[0-9A-Za-zぁ-んァ-ヶー一-龠\-]{0,100}$/u', $_POST['other'])) {
        $error['other'] = '建物名等が間違っています。';
    }
    if ($_POST['tel1'] == '') {
        $error['tel1'] = '市外局番が入力されていません。';
    } elseif (!preg_match('/^[0-9]{1,5}$/', $_POST['tel1'])) {
        $error['tel1'] = '市外局番が間違っています。';
    }
    if ($_POST['tel2'] == '') {
        $error['tel2'] = '電話番号（入力欄2）が入力されていません。';
    } elseif (!preg_match('/^[0-9]{1,5}$/', $_POST['tel2'])) {
        $error['tel2'] = '電話番号（入力欄2）が間違っています。';
    }
    if ($_POST['tel3'] == '') {
        $error['tel3'] = '電話番号（入力欄3）が入力されていません。';
    } elseif (!preg_match('/^[0-9]{1,5}$/', $_POST['tel3'])) {
        $error['tel3'] = '電話番号（入力欄3）が間違っています。';
    }
    if ($_POST['name_kana'] == '') {
        $error['name_kana'] = 'フリガナが入力されていません。';
    } elseif (!preg_match('/^[A-Za-zぁ-んァ-ヶー一-龠]{1,20}$/u', $_POST['name_kana'])) {
        $error['name_kana'] = 'フリガナが間違っています。';
    }
    if ($_POST['name'] == '') {
        $error['name'] = '名前が入力されていません。';
    } elseif (!preg_match('/^[A-Za-zぁ-んァ-ヶー一-龠]{1,15}$/u', $_POST['name'])) {
        $error['name'] = '名前が間違っています。';
    }
    if (!isset($error)) {
        foreach($_POST as $key => $value) {
            $_SESSION['purchase_info'][$key] = $value;
        }
        header('Location: purchase_conf.php');
        exit;
    }
} elseif (isset($_POST['address_search'])) {
    if ($_POST['postal_code1'] == '') {
        $error['postal_code1'] = '郵便番号上3桁が入力されていません。';
    } elseif (!preg_match('/^[0-9]{3}$/', $_POST['postal_code1'])) {
        $error['postal_code1'] = '郵便番号上3桁が間違っています。';
    }
    if ($_POST['postal_code2'] == '') {
        $error['postal_code2'] = '郵便番号下4桁が入力されていません。';
    } elseif (!preg_match('/^[0-9]{4}$/', $_POST['postal_code2'])) {
        $error['postal_code2'] = '郵便番号下4桁が間違っています。';
    }
    if (!isset($error)) {
        $postal_code = $_POST['postal_code1'] . $_POST['postal_code2'];
        $url = "https://zip-cloud.appspot.com/api/search?zipcode=${postal_code}";
        $json = json_decode(file_get_contents($url), true);
        $address = $json["results"];
    }
}

?>

<?php require_once('header.html') ?>
<main>
    <p class="contents-title">確認</p>
    <p class="error"><?=isset($error['database']) ? $error['database'] : ''?></p>
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
                <td><?=isset($product['img']) ? '<img src="' . IMG_PATH . $product['img'] . '" alt="' . $product['img'] . '">' : ''?></td>
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
    <form action="#address" method="post">
        <input type="hidden" name="token" value="<?=getToken()?>">
        <input type="hidden" name="sub_price" value="<?=floor($totalPrice)?>">
        <input type="hidden" name="shipping" value="<?=($totalPrice * (1 + TAX) > 10000) ? 0 : 1000?>">
        <input type="hidden" name="total_price" value="<?=floor($totalPrice * (1 + TAX) + $shipping)?>">
        <input type="hidden" name="tax" value="<?=TAX?>">
        <input type="hidden" name="mail" value="<?=$user['mail']?>">
        <p class="contents-title" id="address">送付先情報<span style="font-size: 20px; margin-left: 10px;">※登録住所以外へ送る場合は変更してください</span></p>
        <p class="toggle-radio"><input type="radio" name="sendFor" id="sendFor2" value="2" checked>変更する <input type="radio" name="sendFor" id="sendFor1" value="1">変更しない</p>
        <table class="table send-for table-left">
            <tr>
                <th>郵便番号</th>
                <td><input type="text" name="postal_code1" value="<?=isset($_POST['postal_code1']) ? $_POST['postal_code1'] : $user['postal_code1']?>"> - <input type="text" name="postal_code2" value="<?=isset($_POST['postal_code2']) ? $_POST['postal_code2'] : $user['postal_code2']?>"> <input type="submit" name="address_search" value="住所検索"><span class="error"><?=isset($error['postal_code1']) ? $error['postal_code1'] : '';?><?=isset($error['postal_code2']) ? $error['postal_code2'] : '';?></span></td>
            </tr>
            <tr>
                <th>住所</th>
                <td>
                    <p><select name="pref"> <?php foreach ($prefectures as $prefecture) : ?> <option value="<?=$prefecture?>" <?=(isset($address[0]['address1']) and $address[0]['address1'] == $prefecture) ? 'selected' : (($prefecture == $prefectures[$user['pref']]) ? 'selected' : '')?>><?=$prefecture?></option> <?php endforeach; ?> </select></p>
                    <p><input type="text" name="city" value="<?=isset($_POST['city']) ? $_POST['city'] : (isset($address[0]['address2']) ? $address[0]['address2'] : $user['city'])?>"><span class="error"><?=isset($error['city']) ? $error['city'] : '';?></span></p>
                    <p><input type="text" name="address" value='<?=isset($_POST['address']) ? $_POST['address'] : (isset($address[0]['address3']) ? $address[0]['address3'] : $user['address'])?>'><span class="error"><?=isset($error['address']) ? $error['address'] : '';?></span></p>
                    <p><input type="text" name="other" value='<?=isset($_POST['other']) ? $_POST['other'] : $user['other']?>'><span class="error"><?=isset($error['other']) ? $error['other'] : '';?></span></p>
                </td>
            </tr>
            <tr>
                <th>電話番号</th>
                <td>
                    <p><input type="text" name="tel1" value="<?=isset($_POST['tel1']) ? $_POST['tel1'] : $user['tel1']?>"> - <input type="text" name="tel2" value="<?=isset($_POST['tel2']) ? $_POST['tel2'] : $user['tel2']?>"> - <input type="text" name="tel3"  value="<?=isset($_POST['tel3']) ? $_POST['tel3'] : $user['tel3']?>"><span class="error"><?=isset($error['tel1']) ? $error['tel1'] : '';?><?=isset($error['tel2']) ? $error['tel2'] : '';?><?=isset($error['tel3']) ? $error['tel3'] : '';?></span></p>
                </td>
            </tr>
            <tr>
                <th>お名前</th>
                <td>
                    <p><input type="text" name="name_kana" value="<?=isset($_POST['name_kana']) ? $_POST['name_kana'] : $user['name_kana']?>"><span class="error"><?=isset($error['name_kana']) ? $error['name_kana'] : '';?></span></p>
                    <p><input type="text" name="name" value="<?=isset($_POST['name']) ? $_POST['name'] : $user['name']?>"><span class="error"><?=isset($error['name']) ? $error['name'] : '';?></span></p>
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
                <td><?php foreach ($payments as $payment) : ?> <input type="radio" name="payment" class="radio" value="<?=$payment['id']?>" <?=($payment['name'] == '各種クレジットカード決済') ? 'checked' : ''?>><?=$payment['name']?> <?php endforeach; ?></td>
            </tr>
        </table>
        <p class="purchase-button"><input type="submit" name="send" value="確認画面へ"></p>
    </form>
</main>
<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script type="text/javascript">
    $('#sendFor1').click(function() {
        $('.send-for').hide();
    });
    $('#sendFor2').click(function() {
        $('.send-for').show();
    });
</script>
<?php require_once('footer.html') ?>