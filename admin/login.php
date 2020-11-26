<?php
require_once('../autoload.php');

if (isset($_POST['send'])) {
    if ($_POST['id'] === '' or $_POST['pass'] === '') {
        $error = 'IDかパスワードが入力されていません';
    } else {
        try {
            $adminUserModel = new AdminUserModel();
            $adminUser = $adminUserModel->fetchAdminUser($_POST['id']);
            if (!empty($adminUser) and password_verify($_POST['pass'], $adminUser['login_pass'])) {
                session_regenerate_id(true);
                $_SESSION['admin']['authenticated'] = password_hash($_POST['id'] . $_POST['pass'], PASSWORD_DEFAULT);
                $_SESSION['admin']['userName'] = $adminUser['name'];
                $_SESSION['admin']['login_id'] = $adminUser['id'];
                if (intval(date('H')) > 4 and intval(date('H')) <= 11) {
                    $_SESSION['admin']['now'] = 'おはようございます。';
                } elseif (intval(date('H')) > 11 and intval(date('H')) <= 17) {
                    $_SESSION['admin']['now'] = 'こんにちは。';
                } else {
                    $_SESSION['admin']['now'] = 'こんばんわ。';
                }
                header('Location: top.php');
                exit;
            }
            $error = 'IDかパスワードが間違っています';
        } catch (Exception $e) {
            $error = '管理者情報の取得に失敗しました。<br>システム管理者にお問い合わせください。';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者ログイン</title>
    <link rel="stylesheet" href="../css/admin_login.css">
</head>

<body class="login-body">
    <h1>洋菓子店カサミンゴー商品管理システム 管理者ログイン</h1>
    <?php if (isset($error)) :?>
        <p style="color: red;"><?=$error?></p>
    <?php endif; ?>
    <form action="" method="post">
        <table class="login-table">
            <tr>
                <td>ログインID</td>
                <td><input type="text" name="id" value="<?=isset($_POST['id']) ? h($_POST['id']) : ''?>"></td>
            </tr>
            <tr>
                <td>パスワード</td>
                <td><input type="password" name="pass"></td>
            </tr>
        </table>
        <p><input type="submit" name="send" value="認証"></p>
    </form>
</body>

</html>