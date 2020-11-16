<?php
require_once('autoload.php');

$hitAddress = [];

if (!isset($_SESSION['authenticated'])) {
    header('Location: login.php');
    exit;
}

try {
    $productDetailModel = new ProductDetailmodel();
    $productModel = new ProductModel();

    $paymentModel = new MPaymentModel();
    $payments = $paymentModel->fetchAll();

    $cartModel = new CartModel();
    $cart = $cartModel->fetchAll();

    $userModel = new UserModel();
    $user = $userModel->fetchById($_SESSION['userId']);
} catch (Exception $e) {
    $error['database'] = '商品情報の取得に失敗しました。<br>カスタマーサポートにお問い合わせください。';
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
    if ($_POST['address2'] == '') {
        $error['address2'] = '市区町村が入力されていません。';
    } elseif (!preg_match('/^[0-9A-Za-zぁ-んァ-ヶー一-龠]{1,15}$/u', $_POST['address2'])) {
        $error['address2'] = '市区町村が間違っています。';
    }
    if ($_POST['address3'] == '') {
        $error['address3'] = '番地が入力されていません。';
    } elseif (!preg_match('/^[0-9A-Za-zぁ-んァ-ヶー一-龠\-]{1,100}$/u', $_POST['address3'])) {
        $error['address3'] = '番地が間違っています。';
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
        foreach ($_POST as $key => $value) {
            $_SESSION['purchase_info'][$key] = $value;
        }
        unset($_SESSION['purchase_info']['send']);
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
        $json = json_decode(file_get_contents("https://zip-cloud.appspot.com/api/search?zipcode=${postal_code}"), true);
        if (!empty($json['results'])) {
            $hitAddress = $json["results"][0];
        }
        if (empty($hitAddress)) {
            $unhitAddressError = '一致する住所がありません。';
        }
    }
}

$hitAddress = $hitAddress + $_POST;

?>

<?php require_once('header.html')?>
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
        <?php foreach ($cart as $prodOfTheCart) :?>
            <?php
                $productDetail = $productDetailModel->fetchById($prodOfTheCart['product_detail_id']);
                $product = $productModel->fetchSingleDetail($productDetail['product_id']);
            ?>
            <tr>
                <td><?=isset($product['img']) ? '<img src="' . IMG_PATH . h($product['img']) . '" alt="' . h($product['img']) . '">' : ''?></td>
                <td><?=h($product['name'])?></td>
                <td><?=h($prodOfTheCart['num'])?></td>
                <td><?=h($productDetail['size'])?></td>
                <td><?=number_format(h($productDetail['price']))?></td>
                <td><?=number_format(h($prodOfTheCart['num']) * h($productDetail['price']))?></td>
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
    <form action="purchase_edit.php?action=fix#address" method="post">
        <input type="hidden" name="token" value="<?=getToken()?>">
        <input type="hidden" name="sub_price" value="<?=floor($totalPrice)?>">
        <input type="hidden" name="shipping" value="<?=($totalPrice * (1 + TAX) > 10000) ? 0 : 1000?>">
        <input type="hidden" name="total_price" value="<?=floor($totalPrice * (1 + TAX) + $shipping)?>">
        <input type="hidden" name="mail" value="<?=$user['mail']?>">
        <p class="contents-title" id="address">送付先情報<span style="font-size: 20px; margin-left: 10px;">※登録住所以外へ送る場合は変更してください</span></p>
        <p class="toggle-radio"><input type="radio" name="sendFor" id="sendFor1" value="1" <?=(isset($_GET['action']) and $_GET['action'] == 'fix') ? 'checked' : ''?>>変更する <input type="radio" name="sendFor" id="sendFor2" value="2" <?=!isset($_GET['action']) ? 'checked' : ''?>>変更しない</p>
        <table class="table send-for table-left" <?=!isset($_GET['action']) ? 'style="display: none;"' : ''?>>
            <tr>
                <th>郵便番号</th>
                <td>
                    <input type="text" name="postal_code1" value="<?=isset($_POST['postal_code1']) ? h($_POST['postal_code1']) : h($user['postal_code1'])?>"> - <input type="text" name="postal_code2" value="<?=isset($_POST['postal_code2']) ? h($_POST['postal_code2']) : h($user['postal_code2'])?>">
                    <input type="submit" name="address_search" value="住所検索">
                    <span class="error"><?=isset($error['postal_code1']) ? $error['postal_code1'] : ''?><?=isset($error['postal_code2']) ? $error['postal_code2'] : ''?><?=isset($unhitAddressError) ? $unhitAddressError : ''?></span>
                </td>
            </tr>
            <tr>
                <th>住所</th>
                <td>
                    <p>
                        <select name="address1">
                            <?php foreach ($prefectures as $prefecture) :?>
                                <?php
                                    $pref = '';
                                    if (isset($hitAddress['address1'])) {
                                        if ($hitAddress['address1'] == $prefecture) {
                                            $pref = 'selected';
                                        }
                                    } else {
                                        if ($prefectures[$user['pref']] == $prefecture) {
                                            $pref = 'selected';
                                        }
                                    }
                                ?>
                                <option <?=$pref?>><?=$prefecture?></option>
                            <?php endforeach;?>
                        </select>
                    </p>
                    <p><input type="text" name="address2" value="<?=isset($hitAddress['address2']) ? h($hitAddress['address2']) : $user['city']?>"><span class="error"><?=isset($error['address2']) ? $error['address2'] : ''?></span></p>
                    <p><input type="text" name="address3" value="<?=isset($hitAddress['address3']) ? h($hitAddress['address3']) : $user['address']?>"><span class="error"><?=isset($error['address3']) ? $error['address3'] : ''?></span></p>
                    <p><input type="text" name="other" value="<?=isset($_POST['other']) ? h($_POST['other']) : $user['other']?>"><span class="error"><?=isset($error['other']) ? $error['other'] : ''?></span></p>
                </td>
            </tr>
            <tr>
                <th>電話番号</th>
                <td>
                    <p>
                        <input type="text" name="tel1" value="<?=isset($_POST['tel1']) ? h($_POST['tel1']) : h($user['tel1'])?>"> - <input type="text" name="tel2" value="<?=isset($_POST['tel2']) ? h($_POST['tel2']) : h($user['tel2'])?>"> - <input type="text" name="tel3"  value="<?=isset($_POST['tel3']) ? h($_POST['tel3']) : h($user['tel3'])?>">
                        <span class="error"><?=isset($error['tel1']) ? $error['tel1'] : ''?><?=isset($error['tel2']) ? $error['tel2'] : ''?><?=isset($error['tel3']) ? $error['tel3'] : ''?></span>
                    </p>
                </td>
            </tr>
            <tr>
                <th>お名前</th>
                <td>
                    <p><input type="text" name="name_kana" value="<?=isset($_POST['name_kana']) ? h($_POST['name_kana']) : h($user['name_kana'])?>"><span class="error"><?=isset($error['name_kana']) ? $error['name_kana'] : ''?></span></p>
                    <p><input type="text" name="name" value="<?=isset($_POST['name']) ? h($_POST['name']) : h($user['name'])?>"><span class="error"><?=isset($error['name']) ? $error['name'] : ''?></span></p>
                </td>
            </tr>
        </table>
        <p class="contents-title">請求先情報</p>
        <table class="table table-left">
            <tr>
                <th>郵便番号</th>
                <td><?=h($user['postal_code1'])?> - <?=h($user['postal_code2'])?></td>
            </tr>
            <tr>
                <th>住所</th>
                <td><?=$prefectures[$user['pref']] . h($user['city']) . h($user['address']) . h($user['other'])?></td>
            </tr>
            <tr>
                <th>電話番号</th>
                <td><?=h($user['tel1'])?> - <?=h($user['tel2'])?> - <?=h($user['tel3'])?></td>
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
                <td>
                    <?php foreach ($payments as $payment) :?>
                        <input type="radio" name="payment" class="radio" value="<?=$payment['id']?>" <?=($payment['name'] == '各種クレジットカード決済') ? 'checked' : ''?>><?=$payment['name']?>
                    <?php endforeach;?>
                </td>
            </tr>
        </table>
        <p class="purchase-button"><input type="submit" name="send" class="btn btn-success" value="確認画面へ"></p>
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