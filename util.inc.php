<?php

/**
 * htmlspecialcharsに変換
 *
 * @param String $str
 * @return String 引数をhtmlspecialchars変換した文字列
 */
function h($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * ページタイトルをボタン形式で表示
 *
 * @return void
 */
function getPage()
{
    $getParam = [
        'edit' => '編集',
        'new' => '登録'
    ];
    $upperPageTitle = [
        'product' => '商品',
        'purchase'=> '注文情報'
    ];
    $lowerPageTitle = [
        'list' => '一覧',
        'conf' => '確認',
        'done' => '完了',
        'edit' => ''
    ];
    $url = explode('_', pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME));
    $page = $url[count($url) -1];
    unset($url[count($url) - 1]);
    echo '<h1><button type="button" class="btn title-button" disabled>' . $upperPageTitle[implode('_', $url)] . (isset($_GET['action']) ? $getParam[$_GET['action']] : '') . $lowerPageTitle[$page] . '</button></h1>';
}

/**
 * トークン発行
 *
 * @return String トークン
 */
function getToken()
{
    $_SESSION['token'] = hash('sha256', uniqid());
    return $_SESSION['token'];
}

/**
 * エラー画面のメッセージを出力
 *
 * @return String
 */
function geterrorMessage()
{
    return ERROR_MESSAGES[$_GET['error']];
}
