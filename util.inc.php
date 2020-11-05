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
    if (preg_match('/^product_list.php/', explode('/', $_SERVER['REQUEST_URI'])[4])) {
        $title = '商品一覧';
    } elseif (preg_match('/^product_edit.php/', explode('/', $_SERVER['REQUEST_URI'])[4])) {
        $title = '商品データ編集';
    } elseif (preg_match('/^product_conf.php/', explode('/', $_SERVER['REQUEST_URI'])[4])) {
        $title = '商品データ登録確認';
    } elseif (preg_match('/^product_done.php/', explode('/', $_SERVER['REQUEST_URI'])[4])) {
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

$prefectures = ['北海道', '青森県', '岩手県', '宮城県', '秋田県', '山形県', '福島県', '茨城県', '栃木県', '群馬県', '埼玉県', '千葉県', '東京都', '神奈川県', '新潟県', '富山県', '石川県', '福井県', '山梨県', '長野県', '岐阜県', '静岡県', '愛知県', '三重県', '滋賀県', '京都府', '大阪府', '兵庫県', '奈良県', '和歌山県', '鳥取県', '島根県', '岡山県', '広島県', '山口県', '徳島県', '香川県', '愛媛県', '高知県', '福岡県', '佐賀県', '長崎県', '熊本県', '大分県', '宮崎県', '鹿児島県', '沖縄県'];

$totalPrice = 0;
$totalCount = 0;