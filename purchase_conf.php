<?php
require_once('admin/autoload.php');

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
        <p class="contents-title">送付先情報</p>
        <table class="table">
            <tr>
                <th>郵便番号</th>
                <td></td>
            </tr>
            <tr>
                <th>住所</th>
                <td>
                </td>
            </tr>
            <tr>
                <th>電話番号</th>
                <td></td>
            </tr>
            <tr>
                <th>お名前</th>
                <td>
                    <p></p>
                    <p></p>
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
        <table class="table">
            <tr>
                <th>支払方法</th>
                <td></td>
            </tr>
        </table>
        <p class="purchase-button"><input type="submit" name="send" value="購入する"> <input type="submit" name="cancel" value="修正する"></p>
    </form>
</main>
<?php require_once('footer.html') ?>