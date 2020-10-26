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
    echo '<h1 style="margin-left: 30px;"><button type="button" class="btn" disabled>' . $title . '</button></h1>';
}