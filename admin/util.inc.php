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
    global $title;
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