<?php
require_once('admin/autoload.php');

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
    $error['databaseError'] = 'データベースに接続できませんでした。';
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
    mb_regex_encoding("UTF-8");
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
        $_SESSION['postal_code1'] = $_POST['postal_code1'];
        $_SESSION['postal_code2'] = $_POST['postal_code2'];
        $_SESSION['pref'] = $_POST['pref'];
        $_SESSION['city'] = $_POST['city'];
        $_SESSION['address'] = $_POST['address'];
        $_SESSION['other'] = $_POST['other'];
        $_SESSION['tel1'] = $_POST['tel1'];
        $_SESSION['tel2'] = $_POST['tel2'];
        $_SESSION['tel3'] = $_POST['tel3'];
        $_SESSION['name_kana'] = $_POST['name_kana'];
        $_SESSION['name'] = $_POST['name'];
        $_SESSION['payment'] = $_POST['payment'];
        $_SESSION['token'] = $_POST['token'];
        header('Location: purchase_conf.php');
        exit;
    }
}

?>

<?php require_once('header.html') ?>
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
                $totalPrice += $productDetail['price'];
                $totalCount += $onCart['num'];
            ?>
        <?php endforeach;?>
        <tr>
            <td colspan="2">小計</td>
            <td><?=$totalCount?></td>
            <td></td>
            <td></td>
            <td><?=number_format($totalPrice)?></td>
        </tr>
        <tr>
            <td colspan="5">消費税</td>
            <td><?=number_format($totalPrice * TAX)?></td>
        </tr>
        <tr>
            <td colspan="5">送料（税込み）</td>
            <td><?=($totalPrice * TAX > 10000) ? 0 : number_format(1000) ;?></td>
        </tr>
        <tr>
            <td colspan="5">総合計</td>
            <td><?=number_format($totalPrice * (1 + TAX))?></td>
        </tr>
    </table>
    <form action="" method="post">
        <input type="hidden" name="token" value="<?=getToken()?>">
        <p class="contents-title">送付先情報<span style="font-size: 20px; margin-left: 10px;">※登録住所以外へ送る場合は変更してください</span></p>
        <input type="radio" name="sendFor" id="sendFor2" value="2" checked>変更する
        <input type="radio" name="sendFor" id="sendFor1" value="1">変更しない
        <table class="table send-for">
            <tr>
                <th>郵便番号</th>
                <td><input type="text" name="postal_code1" value="<?=$user['postal_code1']?>"> - <input type="text" name="postal_code2" value="<?=$user['postal_code2']?>"><span class="error"><?=isset($error['postal_code1']) ? $error['postal_code1'] : '';?><?=isset($error['postal_code2']) ? $error['postal_code2'] : '';?></span></td>
            </tr>
            <tr>
                <th>住所</th>
                <td>
                    <p><select name="pref"> <?php foreach ($prefectures as $prefecture) : ?> <option value="<?= $prefecture ?>" <?=($prefecture == $prefectures[$user['pref']]) ? 'selected' : ''?>><?= $prefecture ?></option> <?php endforeach; ?> </select></p>
                    <p><input type="text" name="city" value="<?=$user['city']?>"><span class="error"><?=isset($error['city']) ? $error['city'] : '';?></span></p>
                    <p><input type="text" name="address" value='<?=$user['address']?>'><span class="error"><?=isset($error['address']) ? $error['address'] : '';?></span></p>
                    <p><input type="text" name="other" value='<?=$user['other']?>'><span class="error"><?=isset($error['other']) ? $error['other'] : '';?></span></p>
                </td>
            </tr>
            <tr>
                <th>電話番号</th>
                <td>
                    <p><input type="text" name="tel1" value="<?=$user['tel1']?>"> - <input type="text" name="tel2" value="<?=$user['tel2']?>"> - <input type="text" name="tel3"  value="<?=$user['tel3']?>"><span class="error"><?=isset($error['tel1']) ? $error['tel1'] : '';?><?=isset($error['tel2']) ? $error['tel2'] : '';?><?=isset($error['tel3']) ? $error['tel3'] : '';?></span></p>
                </td>
            </tr>
            <tr>
                <th>お名前</th>
                <td>
                    <p><input type="text" name="name_kana" value="<?=$user['name_kana']?>"><span class="error"><?=isset($error['name_kana']) ? $error['name_kana'] : '';?></span></p>
                    <p><input type="text" name="name" value="<?=$user['name']?>"><span class="error"><?=isset($error['name']) ? $error['name'] : '';?></span></p>
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
        <?php if (isset($error['databaseError'])) : ?>
            <p class="error"><?= $error['databaseError'] ?></p>
        <?php endif; ?>
        <table class="table table-left">
            <tr>
                <th>支払方法</th>
                <td><?php foreach ($payments as $payment) : ?> <input type="radio" name="payment" class="radio" value="<?= $payment['id'] ?>"><?= $payment['name'] ?> <?php endforeach; ?></td>
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