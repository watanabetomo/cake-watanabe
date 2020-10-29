<?php
require_once('autoload.php');

$_SESSION['userName'] = 'watanabe';
// if (!isset($_SESSION['authenticated'])) {
//     header('Location: login.php');
//     exit;
// }
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>カート</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
    <link rel="stylesheet" href="css/cart.css">
</head>

<body>
    <header>
        <p class="logout"><a href="logout.php">ログアウト</a></p>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="#">洋菓子店カサミンゴー</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        ようこそ<?= $_SESSION['userName'] ?>さん
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <main>
        <h2 class="page-title">カート</h2>
        <div class="wrapper">
            <div class="box1">
                <table class="table-bordered">
                    <tr>
                        <th>削除</th>
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
                        <td></td>
                    </tr>
                </table>
                <form action="" method="post">
                    <p class="submit-button"><input type="submit" class="btn btn-primary" name="continue" value="買い物を続ける">　<input type="submit" class="btn btn-danger" name="clear" value="カートを空にする"></p>
                </form>
            </div>
            <div class="box2">
                <div class="card">
                    <div class="card-body">
                        <h3 class="sub-title">合計金額（税込）</h3>
                        <table class="table">
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
                        <form action="" method="post">
                            <p class="parchase"><input type="submit" name="parchase" value="レジに進む" class="btn btn-success"></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>

</html>