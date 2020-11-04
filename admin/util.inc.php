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
    if (preg_match('/^product_list.php/', explode('/', $_SERVER['REQUEST_URI'])[3])) {
        $title = '商品一覧';
    } elseif (preg_match('/^product_edit.php/', explode('/', $_SERVER['REQUEST_URI'])[3])) {
        $title = '商品データ編集';
    } elseif (preg_match('/^product_conf.php/', explode('/', $_SERVER['REQUEST_URI'])[3])) {
        $title = '商品データ登録確認';
    } elseif (preg_match('/^product_done.php/', explode('/', $_SERVER['REQUEST_URI'])[3])) {
        $title = '登録完了';
    }
    echo '<h1><button type="button" class="btn title-button" disabled>' . $title . '</button></h1>';
}

/**
 * トークン発行
 *
 * @return String トークン
 */
function getToken()
{
    return hash('sha256', session_id());
}