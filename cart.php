<?php
require_once('autoload.php');

$_SESSION['userName'] = 'watanabe';
// if (!isset($_SESSION['authenticated'])) {
//     header('Location: login.php');
//     exit;
// }

if (isset($_POST['purchase'])){
    header('Location: purchase_edit.php');
    exit;
}

if (isset($_POST['continue'])) {
    header('Location: index.php');
    exit;
}
?>
<?php require_once('header.html')?>
<main>
    <div class="wrapper">
            <div class="box1">
                <div class="card">
                    <div class="card-body">
                        <form action="" method="post">
                            <p class="purchase"><input type="submit" name="purchase" value="レジに進む" class="btn btn-success"></p>
                        </form>
                        <h3 class="sub-title">合計金額（税込）</h3>
                        <table class="table table-right">
                            <tr>
                                <th>小計</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th>商品点数</th>
                                <td>100</td>
                            </tr>
                            <tr>
                                <th>送料</th>
                                <td></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="box2">
                <p class="contents-title">カート</p>
                <table class="table-bordered table-center">
                    <tr>
                        <th>削除</th>
                        <th>商品画像</th>
                        <th>商品名</th>
                        <th>個数</th>
                        <th>単価</th>
                        <th>税抜価格</th>
                    </tr>
                    <tr>
                        <td>
                            <form action="" method="post">
                                <input type="hidden" value="">
                                <p style="margin: 10px;"><input type="submit" value="削除"></p>
                            </form>
                        </td>
                        <td></td>
                        <td></td>
                        <td>
                            <form action="" method="post">
                                <input type="number" value="" style="width: 70px; margin: 10px 10px;">
                                <p><input type="submit" value="変更"></p>
                            </form>
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>
                <form action="" method="post">
                    <p class="submit-button"><input type="submit" class="btn btn-primary" name="continue" value="買い物を続ける"> <input type="submit" class="btn btn-danger" name="clear" value="カートを空にする"></p>
                </form>
            </div>
        </div>
    </main>
<?php require_once('footer.html')?>