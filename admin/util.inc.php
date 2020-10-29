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

function getPage()
{
    global $title;
    echo '<h1><button type="button" class="btn title-button" disabled>' . $title . '</button></h1>';
}

function getToken()
{
    return hash('sha256', session_id());
}