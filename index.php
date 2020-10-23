<?php
require_once('admin/autoload.php');

try{
    $productCategoryModel = new ProductCategoryModel();
    $productCategories = $productCategoryModel->fetchAllName();
}catch(PDOException $e){
    echo 'データベースとの接続に失敗しました';
}

?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="keywords" content="バースデーケーキ,誕生日ケーキ,宅配,通販,お取り寄せ">
    <meta name="description" content="誕生日ケーキの通販宅配お取り寄せ。只今バースデーケーキの注文が殺到しております。ヨーロッパ諸国から取り寄せるフルーツ、ナッツ、チョコレート等、最高級の素材を使用しているため、生産数が限られており、お届けまでに多少お時間を頂戴しておりますが、丁寧に仕上げ包装し、必ずご満足頂けるような美しいケーキをお届けさせて頂きます。">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="robots" content="noindex">
    <title>洋菓子店カサミンゴー | バースデーケーキ | 誕生日ケーキ | 宅配 | 通販 | お取り寄せ</title>
    <!--[if lt IE 9]>
<script src="js/html5shiv.js"></script>
<script src="js/css3-mediaqueries.js"></script>
<![endif]-->
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/sp.css">
    <link rel="stylesheet" href="js/jquery.bxslider.css">
    <!--[if lt IE 9]>
<link rel="stylesheet" href="css/old_ie.css">
<![endif]-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="js/jquery.bxslider.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.bxslider').bxSlider({
                mode: 'fade',
                auto: true,
                speed: 4000,
                pager: false,
                controls: false,
                pause: 5000,
            });
        });
    </script>
    <script>
        $(function () {
            var showFlag = false;
            var topBtn = $('#pagetop');
            topBtn.css('bottom', '-100px');
            var showFlag = false;
            //スクロールが100に達したらボタン表示
            $(window).scroll(function () {
                if ($(this).scrollTop() > 100) {
                    if (showFlag == false) {
                        showFlag = true;
                        topBtn.stop().animate({
                            'bottom': '20px'
                        }, 200);
                    }
                } else {
                    if (showFlag) {
                        showFlag = false;
                        topBtn.stop().animate({
                            'bottom': '-100px'
                        }, 200);
                    }
                }
            });
            //スクロールしてトップ
            topBtn.click(function () {
                $('body,html').animate({
                    scrollTop: 0
                }, 500);
                return false;
            });
        });
    </script>
    <script>
        $(function () {
            $(".accordion p").click(function () {
                $(this).next("ul").slideToggle();
                $(this).children("span").toggleClass("open");
            });
        });
    </script>
</head>
<body>
    <div id="wrapper">
        <header>
            <div id="slider">
                <ul class="bxslider">
                    <li><img src="img/slide01.jpg"></li>
                    <li><img src="img/slide02.jpg"></li>
                    <li><img src="img/slide03.jpg"></li>
                    <li><img src="img/slide04.jpg"></li>
                </ul>
                <div id="logo"><img src="img/logo.png" alt="洋菓子店カサミンゴー"></div>
            </div>
            <ul class="menu">
                <li class="menu__single">
                    <span><a href="http://casamingo.gift/index_qa.htm" class="init-bottom">LINEUP</a></span>
                    <ul class="menu__second-level">
                        <li><a href="#birthday">バースデーケーキ</a></li>
                        <li><a href="#select">限定セット</a></li>
                        <li><a href="#cheesecake">チーズケーキ</a></li>
                        <li><a href="#chocolatecake">チョコレートケーキ</a></li>
                        <li><a href="#shortcake">ショートケーキ/タルト</a></li>
                        <li><a href="#seasoncake">期間限定ケーキ</a></li>
                        <li><a href="http://casamingo.gift/index_ichiran.htm">サイズ別</a></li>
                    </ul>
                </li>
                <li class="menu__single">
                    <span><a href="http://casamingo.gift/index_qa.htm" class="init-bottom">INFO</a></span>
                    <ul class="menu__second-level">
                        <li><a href="http://casamingo.gift/index_qa.htm">よくあるご質問</a></li>
                        <li><a href="#contents08">ご注文方法</a></li>
                        <li><a href="#contents09">時間帯お届け</a></li>
                        <li><a href="#contents10">梱包について</a></li>
                        <li><a href="#contents11">ご紹介</a></li>
                        <li><a href="http://casamingo.gift/index_tokutei.htm">特定商取引法</a></li>
                        <li><a href="http://casamingo.gift/index_koe.htm">お客様の声</a></li>
                        <li><a href="http://casamingo.gift/sp/index.html">スマホサイト</a></li>
                    </ul>
                </li>
                <li class="menu__single">
                    <span><a href="">MAIL</a></span>
                </li>
                <li class="menu__single">
                    <span><a href="http://www.amazon.co.jp/gp/redirect.html?ie=UTF8&amp;location=http%3A%2F%2Fwww.amazon.co.jp%2Fb%3Fie%3DUTF8%26me%3DA1LTMHN3LQOUNT%26qid%3D1224330145&amp;tag=suumr202-22&amp;linkCode=ur2&amp;camp=247&amp;creative=7399">
                            amazon</a></span>
                </li>
            </ul>
            <div class="postage">
                <a href="http://casamingo.gift/souryou.htm"><strong>本州送料３４０円</strong>
                    ５０００円以上お買い上げのお客様は<strong>期間限定本州送料無料</strong></a>
            </div>
        </header>
        <div id="main">
            <div class="contents01">
                <p><strong>年間７０００台以上の全国配送実績</strong> 本場ヨーロッパで修行を繰り返し、本場の味をお届けいたします。<br>
                    今週の一番人気は特注ハート型シュス木苺レアチーズケーキです！</p>
            </div>
            <div id="birthday">
                <h1><a href="http://casamingo.gift/index_hb.htm">Birthday cake</a></h1>
                <h2><a href="http://casamingo.gift/index_hb.htm"><img src="img/hb.gif" alt="birthday cake"></a></h2>
                <div class="catch">
                    <p>大切な方にきっとお喜び頂ける美味しいケーキをお届けいたします。<br>
                        <span>バースデーケーキ、記念日ケーキに名前やメッセージを入れて、世界に1台だけのケーキを贈りましょう。</span></p>
                </div>
                <div id="plate">
                    <div class="box1">
                        <img src="img/plate.jpg" alt="誕生日プレート">
                        <p><strong>チョコレートプレートに名前や文字を入れて世界に１台だけのケーキを贈りましょう。</strong><br>
                            現在当店では東南アジアのほぼ中心、赤道直下に位置する北マレー半島から取り寄せる最高品質のホワイトチョコレートにてプレートをお作りしております。<br>
                            大切な記念日に相応しいハイクオリティーの商品はきっとお喜び頂けると思います。<br>
                            <span>(例　Happy Birthday Akiko,Happy Birthday あきこ(ハート),お母さんありがとう,First Anniversary,etc...</span></p>
                    </div>
                    <div class="box2">
                        <p>Happy Birthdayプレート￥４００／Happy Birthday+名前(ローマ字orひらがな)プレート　￥５００／ハート型プレート　￥６００<br>
                            <span>誕生日や記念日にご利用のお客様はプレート２００円割引キャンペーン実施中</span><br>
                            プレートはケーキご注文時に備考欄よりお申し付け下さい。ろうそくはご希望本数を無料プレゼントいたします。</p>
                    </div>
                </div>
            </div><!--/#birthday-->
            <div id="select">
                <h1><a href="#select">[期間限定]厳選人気商品２種類セット<br>
                        <strong>送料無料</strong></a></h1>
                <div id="contents">
                    <div class="contents_l">
                        <h2><a href="#open01"><img src="img/select1.gif"></a></h2>
                        <p><strong>シュス木苺レアチーズケーキ<br></strong><br>
                            直径15cm ￥4,000</p>
                        <p><strong>ウィーンの銘菓　ザッハ－トルテ<br></strong><br>
                            直径12cm ￥3,200<br></p>
                        <br>
                        <p><s>通常価格￥7,200</s><br>
                            <strong>↓<br>
                                セット価格￥6,300</strong><br>
                            <span>(￥5,000以上　本州送料無料)</span></p>
                        <p><span>通常1～4日以内に発送。</span></p>
                        <div class="button1">
                            <form method="post" action="http://cart.ec-sites.jp/cart_step2/pc/pk00/">
                                <input name="es_item_qty" value="1" type="hidden">
                                <input name="es_charset" value="sjis" type="hidden">
                                <input name="es_item_id" value="32" type="hidden">
                                <input name="es_shop_id" value="1482" type="hidden">
                                <input name="es_stock_attr_flag" value="0" type="hidden">
                                <input name="es_submit" value="限定セットを注文" type="submit">
                            </form>
                        </div><!--/.button1-->
                        <div class="qa">
                            <form action="https://cart.ec-sites.jp/cart_step2/item-question/top/shid/1482/item_id/32/" target="_blank">
                                <input value="お問い合わせ" type="submit">
                            </form>
                        </div><!--/.qa-->
                    </div><!--/.contents_l-->
                    <div class="contents_c">
                        <h2><a href="#open02"><img src="img/select2.gif"></a></h2>
                        <p><strong>タルト・オー・シトロン<br>レモンのタルト</strong><br>
                            直径16cm ￥3,650</p>
                        <p><strong>洋なしのチーズタルト<br></strong><br>
                            直径16cm ￥3,650<br></p>
                        <br>
                        <p><s>通常価格￥7,300</s><br>
                            <strong>↓<br>
                                セット価格￥6,800</strong><br>
                            <span>(￥5,000以上　本州送料無料)</span></p>
                        <p><span>只今２週間待ちです。</span></p>
                        <div class="button1">
                            <form method="post" action="http://cart.ec-sites.jp/cart_step2/pc/pk00/">
                                <input name="es_item_qty" value="1" type="hidden">
                                <input name="es_charset" value="sjis" type="hidden">
                                <input name="es_item_id" value="93" type="hidden">
                                <input name="es_shop_id" value="1482" type="hidden">
                                <input name="es_stock_attr_flag" value="0" type="hidden">
                                <input name="es_submit" value="限定セットを注文" type="submit">
                            </form>
                        </div><!--/.button1-->
                        <div class="qa">
                            <form action="https://cart.ec-sites.jp/cart_step2/item-question/top/shid/1482/item_id/93/" target="_blank">
                                <input value="お問い合わせ" type="submit">
                            </form>
                        </div><!--/.qa-->
                    </div><!--/.contents_c-->
                    <div class="contents_r">
                        <h2><a href="#open03"><img src="img/select3.gif"></a></h2>
                        <p><strong>シュヴァルツベルダー<br>キルシュトルテ</strong><br>
                            直径15cm ￥3,800</p>
                        <p><strong>ウィーンの銘菓　ザッハ－トルテ<br></strong><br>
                            直径12cm ￥3,200<br></p>
                        <br>
                        <p><s>通常価格￥7,000</s><br>
                            <strong>↓<br>
                                セット価格￥6,300</strong><br>
                            <span>(￥5,000以上　本州送料無料)</span></p>
                        <p><span>通常1～2日以内に発送。</span></p>
                        <div class="button1">
                            <form method="post" action="http://cart.ec-sites.jp/cart_step2/pc/pk00/">
                                <input name="es_item_qty" value="1" type="hidden">
                                <input name="es_charset" value="sjis" type="hidden">
                                <input name="es_item_id" value="33" type="hidden">
                                <input name="es_shop_id" value="1482" type="hidden">
                                <input name="es_stock_attr_flag" value="0" type="hidden">
                                <input name="es_submit" value="限定セットを注文" type="submit">
                            </form>
                        </div><!--/.button1-->
                        <div class="qa">
                            <form action="https://cart.ec-sites.jp/cart_step2/item-question/top/shid/1482/item_id/33/" target="_blank">
                                <input value="お問い合わせ" type="submit">
                            </form>
                        </div><!--/.qa-->
                    </div><!--/.contents_r-->
                    <div id="modal">
                        <div id="open01">
                            <a href="#contents" class="close_overlay">×</a>
                            <div class="modal_window">
                                <h3>[期間限定]厳選人気商品２種類セット</h3>
                                <p><strong>シュス木苺レアチーズケーキ　直径15cm ￥4,000</strong><br>
                                    北海道産の最高級カッテージチーズ＆生クリームと真っ赤な木苺の組み合わせ。<br>
                                    フランスの契約農家から取り寄せるこだわりの木苺と相性抜群です。</p>
                                <p><strong>ウィーンの銘菓　ザッハ－トルテ　直径12cm ￥3,200</strong><br>
                                    ガーナから取り寄せるこだわりのヴァローナチョコレートを使用した最高級チョコレートケーキ<br>
                                    完璧にテンパリングされたとろけるようなチョコレートと芸術的なアロマとアプリコットの風味をお楽しみください。</p>
                                <p><s>通常価格￥7,200</s><strong>→セット価格￥6,300</strong><span>(￥5,000以上　本州送料無料)</span></p>
                                <img src="img/cream.jpg" alt="クリームプレゼント">
                                <p>サービスでザッハートルテ用に泡立てた最高級生クリームがプレゼントされます。</p>
                                <a href="#contents">【×】CLOSE</a>
                            </div><!--/.modal_window-->
                        </div><!--/#open01-->
                        <div id="open02">
                            <a href="#contents" class="close_overlay">×</a>
                            <div class="modal_window">
                                <h3>[期間限定]厳選人気商品２種類セット</h3>
                                <p><strong>タルト・オー・シトロン　レモンのタルト　直径16cm ￥3,650</strong><br>
                                    サクサクのタルト生地に、目にも鮮やかなレモンクリームがたっぷり詰まった逸品。<br>
                                    口に入れれば、レモンのすっぱさがパッと広がり、その余韻が香りと共に口いっぱいに広がり続けます。</p>
                                <p><strong>洋なしのチーズタルト　直径16cm ￥3,650</strong><br>
                                    北海道産の最高級カッテージチーズと最高級生クリームを贅沢に使用した逸品。<br>
                                    綺麗にカットされた選りすぐりのやわらかい洋なしを飾り、自家製無添加の木苺ソースで仕上げました。</p>
                                <p><s>通常価格￥7,300</s><strong>→セット価格￥6,800</strong><span>(￥5,000以上　本州送料無料)</span></p>
                                <a href="#contents">【×】CLOSE</a>
                            </div><!--/.modal_window-->
                        </div><!--/#open02-->
                        <div id="open03">
                            <a href="#contents" class="close_overlay">×</a>
                            <div class="modal_window">
                                <h3>[期間限定]厳選人気商品２種類セット</h3>
                                <p><strong>シュヴァルツベルダーキルシュトルテ　直径15cm ￥3,800</strong><br>
                                    ドイツのショートケーキを北海道の驚くほど軽い厳選純生クリームで仕上げた逸品。<br>
                                    選び抜かれた最高級生クリームの味をお楽しみください。</p>
                                <p><strong>ウィーンの銘菓　ザッハ－トルテ　直径12cm ￥3,200</strong><br>
                                    ガーナから取り寄せるこだわりのヴァローナチョコレートを使用した最高級チョコレートケーキ<br>
                                    完璧にテンパリングされたとろけるようなチョコレートと芸術的なアロマとアプリコットの風味をお楽しみください。</p>
                                <p><s>通常価格￥7,000</s><strong>→セット価格￥6,300</strong><span>(￥5,000以上　本州送料無料)</span></p>
                                <img src="img/cream.jpg" alt="クリームプレゼント">
                                <p>サービスでザッハートルテ用に泡立てた最高級生クリームがプレゼントされます。</p>
                                <a href="#contents">【×】CLOSE</a>
                            </div><!--/.modal_window-->
                        </div><!--/#open03-->
                    </div><!--/#modal-->
                </div><!--/#contents-->
            </div><!--/#select-->
            <?php for($i=0; $i<count($productCategories); $i++):?>
            <div id="cheesecake">
                <h1><a href=""><?=$productCategories[$i]['name']?></a></h1>
                <div class="contents02">
            <?php
            try{
                $productModel = new ProductModel();
                $products = $productModel->fetchByCategoryId($productCategories[$i]['id']);
            }catch(PDOException $e){
                echo $e->getMessage();
            }
            ?>
            <?php for($j=0; $j<count($products); $j++):?>
                    <div class="contents02_<?=$j + 1?>">
                        <h2><a href=""><img src="img/<?=$products[$j]['img']?>"></a></h2>
                        <p><strong><?=$products[$j]['name']?><br></strong><br></p>
                        <p><span>通常1～4日以内に発送。</span></p>
                        <ul class="accordion">
                            <li>
                                <p>ご注文</p>
                                <ul>
                                    <?php
                                    try{
                                        $productDetailModel = new ProductDetailModel();
                                        $productDetails = $productDetailModel->fetchByProductId($products[$j]['id']);
                                    }catch(PDOException $e){
                                        echo $e->getMessage();
                                    }
                                    ?>
                                    <?php for($k=0; $k<count($productDetails); $k++):?>
                                    <li>
                                        <form method="post" action="http://cart.ec-sites.jp/cart_step2/pc/pk00/">
                                            <input name="es_item_qty" value="1" type="hidden">
                                            <input name="es_charset" value="sjis" type="hidden">
                                            <input name="es_item_id" value="<?=$productDetails[$k]['product_id']?>" type="hidden">
                                            <input name="es_shop_id" value="1482" type="hidden">
                                            <input name="es_stock_attr_flag" value="0" type="hidden">
                                            <input name="es_submit" value="<?=$productDetails[$k]['size']?>cm ￥<?=number_format($productDetails[$k]['price'])?>" type="submit">
                                        </form>
                                    </li>
                                    <?php endfor;?>
                                </ul>
                            </li>
                        </ul><!--/.accordion-->
                        <div class="qa">
                            <form action="https://cart.ec-sites.jp/cart_step2/item-question/top/shid/1482/item_id/54/" target="_blank">
                                <input value="お問い合わせ" type="submit">
                            </form>
                        </div><!--/.qa-->
                    </div><!--/.contents02_4-->
                            <?php endfor; ?>
                </div><!--/.contents02-->
                <div class="info">
                    <p>大きなサイズは多少お時間を頂戴しております。</p>
                </div>
            </div><!--/#cheesecake-->
                            <?php endfor; ?>
                    <div class="info">
                        <p>数量限定につき完売の際はご容赦ください。</p>
                    </div>
                    <div id="modal02">
                        <div id="open04">
                            <a href="#contents04" class="close_overlay">×</a>
                            <div class="modal_window">
                                <h3>期間限定ケーキ</h3>
                                <p><strong>タルト・オー・シトロン　レモンのタルト</strong><br>
                                    サクサクのタルト生地に、目にも鮮やかなレモンクリームがたっぷり詰まった逸品。<br>
                                    口に入れれば、レモンのすっぱさがパッと広がり、その余韻が香りと共に口いっぱいに広がり続けます。</p>
                                <p><strong>16cm ￥3,650</strong>｜<strong>18cm ￥4,800</strong></p>
                                <a href="#contents">【×】CLOSE</a>
                            </div><!--/.modal_window-->
                        </div><!--/#open04-->
                        <div id="open05">
                            <a href="#contents04" class="close_overlay">×</a>
                            <div class="modal_window">
                                <h3>季節限定・通販限定ケーキ</h3>
                                <p><strong>ムース・オー・カシス</strong><br>
                                    ヨーロッパから取り寄せるこだわりの黒すぐりと北海道の生クリームの甘酸っぱいふわふわムース。<br>
                                    スポンジには北海道産純生クリーム、そしてこだわりの黒すぐりジャムとナッツをふんだんに使用。</p>
                                <p><strong>15cm ￥4,000</strong>｜<strong>20cm ￥6,300</strong></p>
                                <a href="#contents">【×】CLOSE</a>
                            </div><!--/.modal_window-->
                        </div><!--/#open05-->
                    </div><!--/#modal02-->
                </div><!--/#contents04-->
            </div><!--/#seasoncake-->
            <div id="contents05">
                <p><strong>テレビや雑誌でご紹介いただきました。</strong></p><br>
                <p>11/01　<a href="https://www.eposcard.co.jp/index.html">マルイで使えるEPOS CARD NEWSのお取り寄せコーナーで「感動のパーティーケーキ」と絶賛紹介されました。</a></p>
                <p>06/01　クロネコヤマトが厳選！「北信越美味めぐり」トップページに、厳選されたおすすめの逸品として紹介されました。</p>
                <p>05/21　Do！sul!どうする？６月号に紹介されました。</p>
                <p>04/16　Do！sul!どうする？５月号に紹介されました。</p>
                <p>03/06　Do！sul!どうする？４月号に紹介されました。</p>
                <p>02/25　<a href="http://www.goodnagano.com/info/issue/index.php?bcd=ko">長野こまち４月号に紹介されました。</a></p>
                <p>02/03　SBC信越放送テレビ「３時は！ららら♪」に紹介されました。</p>
                <p>11/06　<a href="http://shukan.bunshun.jp/category/magazine">有名作家ご推薦の店として週刊文春１１月６日号に紹介されました。</a></p>
                <p>07/02　SBC信越放送テレビ「信州まるごとワイド『キャッチ』」に紹介されました。</p>
                <p>06/04　NHKテレビ「暮らしのティータイム」に出演しました。</p>
                <div class="sample1">
                    <img src="img/sample07.jpg">
                </div>
                <div class="sample2">
                    <img src="img/sample01.jpg"><img src="img/sample02.jpg"><img src="img/sample03.jpg"><img src="img/sample04.jpg"><img src="img/sample05.jpg"><img src="img/sample06.jpg">
                </div>
            </div><!--/#contents05-->
            <div id="contents06">
                <p><strong>全国お菓子人気ランキングで１位に輝きました。</strong></p><br>
                <p><a href="http://www.e-shops.jp/sh/rk13/html/">日本最大級ネットショップランキングで１位に輝きました。</a></p>
            </div><!--/#contents06-->
            <div id="contents07">
                <p><strong>お客様の声</strong></p><br>
                <p><span>お買い上げ商品：ウィーンの銘菓ザッハートルテ</span></p>
                <p><span>★★★★★</span></p>
                <p>上品な味わいで口溶けがクセになるきめ細かいチョコですね。<br>チョコはビターですが、中のジャム（素材を生かした適度な甘さ）とうまく調和していて美味しかったです。<br>今まで色々食べましたが、このケーキは絶品だと思います！お味は大人向けだと思います！<br>届いた日に一人で半分ペロっと食べてしまいました。すごくオススメ！！
                </p>
                <br><br>
                <p><span>お買い上げ商品：シュス木苺レアチーズケーキ</span></p>
                <p><span>★★★★★</span></p>
                <p>先週、和歌山県に小さなバースデーケーキを送っていただきました。<br>
                    その節は、急な依頼にご対応下さいましてありがとうございました。<br>誕生日ケーキが出てきた途端にパーティームードになり、おかげさまでHappyなお祝いができました。<br>お祝いにはやっぱりケーキですね♪そしてお味は最高でした！<br>
                    夕食後、直ぐでしたが、とっても美味しくて二人でペロリと食べてしまいました。<br>今回のケーキは是非また食べたいですが、他のケーキも食べてみたいと思いました。
                </p><br>
                <p><a href="http://casamingo.gift/index_koe.htm">もっと見る>>></a></p>
            </div><!--/#contents07-->
            <div id="contents08">
                <p><strong>ご注文方法</strong></p><br>
                <p><span>「ご注文」ボタンからサイズをお選び下さい。</span></p>
                <p>商品を選び注文が進むと、到着日、到着時間を指定する画面へ進みます。<br>
                    その後、記念日チョコレートプレートを選択する画面が出てきますので、<br>
                    ご希望のメッセージにてお作りさせていただきます。全商品にプレートがお付けできます。</p>
                <p><span>お急ぎの方はご希望商品をお電話にてお申し付け下さい。指定場所にご指定日時必着でお届けいたします。<br>
                        平日１８時までのご注文で本州は最短翌日午前中お届けが可能な場合があります。</span></p>
                <img src="img/tel.png" alt="電話番号">
                <a href="mailto:miyakawa@casamingo.jp"><img src="img/mail.png" alt="メール"></a>
                <p>只今大変ご注文が殺到しておりますので、できるだけ「ご注文」ボタンからご注文下さい。</p>
                <p><a href="http://www.amazon.co.jp/gp/redirect.html?ie=UTF8&amp;location=http%3A%2F%2Fwww.amazon.co.jp%2Fb%3Fie%3DUTF8%26me%3DA1LTMHN3LQOUNT%26qid%3D1224330145&amp;tag=suumr202-22&amp;linkCode=ur2&amp;camp=247&amp;creative=7399">
                        amazonからのご注文も可能です。</a></p>
            </div><!--/#contents08-->
            <div id="contents09">
                <p><strong>時間帯お届けサービス</strong></p><br>
                <img src="img/time.png" alt="時間帯お届け">
            </div><!--/#contents09-->
            <div id="contents10">
                <div class="pack">
                    <p><span>梱包について</span></p>
                    <p>愛情がたっぷりと注がれた洋菓子は衛生面に最大限の注意を払い丁寧に梱包しております。</p>
                </div>
                <div class="pack_l">
                    <img src="img/pack1.jpg">
                    <p>箱には環境に優しいマシュマロクッションやエアークッションをしっかり詰めて包装いたします。</p>
                </div>
                <div class="pack_r">
                    <img src="img/pack2.jpg">
                    <p>累計50000台以上の発送実績から絶対に壊れない梱包方法を見つけました。
                        作り立ての味がお楽しみ頂けるよう工夫されております。</p>
                </div>
                <div class="pack_l">
                    <img src="img/pack3.jpg">
                    <p>ギフトにふさわしい豪華デコレーションボックスでお届けさせていただきます。<br>
                        ギフト専門店につき納品書は同封されません。</p>
                </div>
            </div><!--/#contents10-->
            <div id="contents11">
                <div class="pack">
                    <p><span>食材へのこだわり</span></p>
                </div>
                <div class="pack_l">
                    <img src="img/syokuzai1.gif">
                    <p>ヨーロッパ諸国から取り寄せるフルーツ、ナッツ、チョコレート等、最高級の素材を使用しているため、生産数が限られており、お届けまでに多少お時間を頂戴しておりますが、丁寧に仕上げ包装し、必ずご満足頂けるような美しいケーキをお届けさせて頂きます。</p>
                </div>
                <div class="pack_r">
                    <img src="img/syokuzai2.gif">
                    <p>当店では健康を気になさる方でも安心してお楽しみいただけるよう、香料、着色料、保存料等を添加せずに製造しております。</p>
                </div>
                <div class="pack_l">
                    <img src="img/syokuzai3.gif">
                    <p>当社の洋菓子は職人が厳選素材を贅沢に使用し、６時間以上かけて一台一台お作りしているため、一日に製造できる数も限られております。あらかじめご了承下さい。</p>
                </div>
            </div><!--/#contents11-->
            <div id="contents12">
                <p><strong>作り手紹介</strong></p><br>
                <p><span>シェフ パティシエ 宮川鈴弘</span><br>
                    １０歳の頃から独学でドイツ語を勉強、１６歳からオーストリア・インスブルックの洋菓子店、<br>
                    コンディトライ・ヴァリエ、コンディトライ・ワハター等々の屈指の名店で修行を積み、徹底的に<br>鮮度にこだわった洋菓子の宅配専門店として「洋菓子店カサミンゴー」をオープン。<br>行列が出来る店として話題に。中でもシュス木苺レアチーズケーキは鈴弘氏お勧めの逸品。</p>
            </div><!--/#contents12-->
        </div><!--/#main-->
        <p id="pagetop"><a href="#wrapper"><img src="img/pagetop.png" alt="ページトップボタン"></a></p>
        <footer>
            <small>Copyright Casa Mingo All Rights Reserved.　Kitaowaribe397-1 Nagano-shi Nagano 3810014 JAPAN</small>
        </footer>
    </div><!--/#wrapper-->
</body>
</html>