<?php
require_once('autoload.php');

if (isset($_SESSION['authenticated'])) {
    header('Location: cart.php');
    exit;
}

if (isset($_POST['login'])) {
    if ($_POST['id'] === '' or $_POST['pass'] === '') {
        $error = 'IDかパスワードが入力されていません';
    } else {
        try {
            $userModel = new UserModel();
            $user = $userModel->fetchByLoginId($_POST['id']);
            if (!empty($user) and $_POST['pass'] == $user['login_pass']) {
                session_regenerate_id(true);
                $_SESSION['authenticated'] = password_hash($_POST['id'] . $_POST['pass'], PASSWORD_DEFAULT);
                $userModel->updateLoginDate($user['id']);
                $_SESSION['userName'] = $user['name'];
                $_SESSION['userId'] = $user['id'];
                header('Location: cart.php');
                exit;
            }
            $error = 'IDかパスワードが間違っています';
        } catch (Exception $e) {
            $error = '会員情報の取得に失敗しました。<br>カスタマーサポートにお問い合わせください。';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
    <link rel="stylesheet" href="css/login.css">
</head>

<body>
    <div class="card">
        <div class="card-body">
            <h1>洋菓子店カサミンゴー 会員ログイン</h1>
            <?php if (isset($error)) :?>
                <p class="error"><?=$error?></p>
            <?php endif;?>
            <form action="" method="post">
                <table>
                    <tr>
                        <th>ログインID</th>
                        <td><input type="text" name="id" value=""></td>
                    </tr>
                    <tr>
                        <th>パスワード</th>
                        <td><input type="password" name="pass"></td>
                    </tr>
                </table>
                <p><input type="submit" name="login" value="ログイン"></p>
            </form>
        </div>
    </div>
</body>

</html>