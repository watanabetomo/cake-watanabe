<?php
require_once('autoload.php');

$prefectures = ['北海道', '青森県', '岩手県', '宮城県', '秋田県', '山形県', '福島県', '茨城県', '栃木県', '群馬県', '埼玉県', '千葉県', '東京都', '神奈川県', '新潟県', '富山県', '石川県', '福井県', '山梨県', '長野県', '岐阜県', '静岡県', '愛知県', '三重県', '滋賀県', '京都府', '大阪府', '兵庫県', '奈良県', '和歌山県', '鳥取県', '島根県', '岡山県', '広島県', '山口県', '徳島県', '香川県', '愛媛県', '高知県', '福岡県', '佐賀県', '長崎県', '熊本県', '大分県', '宮崎県', '鹿児島県', '沖縄県'];

if (isset($_POST['send'])) {
    header('Location: purchase_conf.php');
    exit;
}

try {
    $paymentModel = new MPaymentModel();
    $payments = $paymentModel->fetchAll();
} catch (PDOException $e) {
    $error['databaseError'] = 'データベースに接続できませんでした。';
}

if (isset($_POST['send'])) {
    if ($_POST['postal_code1'] == ''){
        $error['postal_code1'] = '郵便番号上3桁が入力されていません。';
    } elseif (!preg_match('/^[0-9]{3}$/', $_POST['postal_code1'])) {
        $error['postal_code1'] = '郵便番号上3桁が間違っています。';
    }
    if ($_POST['postal_code2'] == ''){
        $error['postal_code2'] = '郵便番号下4桁が入力されていません。';
    } elseif (!preg_match('/^[0-9]{4}$/', $_POST['postal_code2'])) {
        $error['postal_code2'] = '郵便番号下4桁が間違っています。';
    }
    if ($_POST['city'] == ''){
        $error['city'] = '市区町村が入力されていません。';
    } elseif (!preg_match('/^\w{1,15}$/', $_POST['city'])) {
        $error['city'] = '市区町村が間違っています。';
    }
    if ($_POST['address'] == ''){
        $error['address'] = '番地が入力されていません。';
    } elseif (!preg_match('/^\w{1,100}$/', $_POST['address'])) {
        $error['address'] = '番地が間違っています。';
    }
    if (!preg_match('/^\w{1,100}$/', $_POST['other'])) {
        $error['other'] = '建物名等が間違っています。';
    }
    if ($_POST['tel1'] == ''){
        $error['tel1'] = '市外局番が入力されていません。';
    } elseif (!preg_match('/^[0-9]{1,5}$/', $_POST['tel1'])) {
        $error['tel1'] = '市外局番が間違っています。';
    }
    if ($_POST['tel2'] == ''){
        $error['tel2'] = '電話番号（入力欄2）が入力されていません。';
    } elseif (!preg_match('/^[0-9]{1,5}$/', $_POST['tel2'])) {
        $error['tel2'] = '電話番号（入力欄2）が間違っています。';
    }
    if ($_POST['tel3'] == ''){
        $error['tel3'] = '電話番号（入力欄3）が入力されていません。';
    } elseif (!preg_match('/^[0-9]{1,5}$/', $_POST['tel3'])) {
        $error['tel3'] = '電話番号（入力欄3）が間違っています。';
    }
    if ($_POST['name_kana'] == ''){
        $error['name_kana'] = 'フリガナが入力されていません。';
    } elseif (!preg_match('/^\w{1,20}$/', $_POST['name_kana'])) {
        $error['name_kana'] = 'フリガナが間違っています。';
    }
    if ($_POST['name'] == ''){
        $error['name'] = '名前が入力されていません。';
    } elseif (!preg_match('/^\w{1,15}$/', $_POST['name'])) {
        $error['name'] = '名前が間違っています。';
    }
    if (!isset($error)) {
        header('Location: purchase_conf.php');
        exit;
    }
}

?>

<?php require_once('header.html') ?>
<main>
    <p class="contents-title">確認</p>
    <table class="table">
        <tr>
            <th>商品画像</th>
            <th>商品名</th>
            <th>個数</th>
            <th>単価</th>
            <th>税抜価格</th>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td colspan="2">小計</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td colspan="4">消費税</td>
            <td></td>
        </tr>
        <tr>
            <td colspan="4">送料（税込み）</td>
            <td></td>
        </tr>
        <tr>
            <td colspan="4">総合計</td>
            <td></td>
        </tr>
    </table>
    <form action="" method="post">
        <p class="contents-title">送付先情報<span style="font-size: 20px; margin-left: 10px;">※登録住所以外へ送る場合は変更してください</span></p>
        <input type="radio" name="sendFor" value="1" checked onclick="formSwitch()">変更しない
        <input type="radio" name="sendFor" value="2">変更する
        <table class="table">
            <tr>
                <th>郵便番号</th>
                <td><input type="text" name="postal_code1"> - <input type="text" name="postal_code2"></td>
            </tr>
            <tr>
                <th>住所</th>
                <td>
                    <p><select name="pref"> <?php foreach ($prefectures as $prefecture) : ?> <option value="<?= $prefecture ?>"><?= $prefecture ?></option> <?php endforeach; ?> </select></p>
                    <p><input type="text" name="city"></p>
                    <p><input type="text" name="address"></p>
                    <p><input type="text" name="other"></p>
                </td>
            </tr>
            <tr>
                <th>電話番号</th>
                <td><p><input type="text" name="tel1"> - <input type="text" name="tel2"> - <input type="text" name="tel3"></p></td>
            </tr>
            <tr>
                <th>お名前</th>
                <td>
                    <p><input type="text" name="name_kana"></p>
                    <p><input type="text" name="name"></p>
                </td>
            </tr>
        </table>
        <p class="contents-title">請求先情報</p>
        <table class="table">
            <tr>
                <th>郵便番号</th>
                <td></td>
            </tr>
            <tr>
                <th>住所</th>
                <td></td>
            </tr>
            <tr>
                <th>電話番号</th>
                <td></td>
            </tr>
            <tr>
                <th>メールアドレス</th>
                <td></td>
            </tr>
            <tr>
                <th>お名前</th>
                <td></td>
            </tr>
        </table>
        <p class="contents-title">お支払方法</p>
        <?php if(isset($error['databaseError'])):?>
            <p class="error"><?=$error['databaseError']?></p>
        <?php endif;?>
        <?php foreach($payments as $payment):?>
            <input type="radio" name="payment" class="radio" value="<?=$payment['id']?>"><?=$payment['name']?>
        <?php endforeach;?>
        <p class="purchase-button"><input type="submit" name="send" value="確認画面へ"></p>
    </form>
</main>
<script>
    function formSwitch(){
        document.getElementsById('address-form').style.display='none';
    }
</script>
<?php require_once('footer.html') ?>