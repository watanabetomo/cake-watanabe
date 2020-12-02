<?php
class OrderModel extends Model
{
    /**
     * 注文情報の登録
     *
     * @param int $user_id
     * @param String $name
     * @param String $name_kana
     * @param String $mail
     * @param int $tel1
     * @param int $tel2
     * @param int $tel3
     * @param int $postal_code1
     * @param int $postal_code2
     * @param int $pref
     * @param String $city
     * @param String $address
     * @param String $other
     * @param int $payment_id
     * @param int $sub_price
     * @param int $shipping_price
     * @param int $tax
     * @param int $total_price
     * @param PDO $dbh
     * @return void
     */
    public function commitOrder(
        $user_id,
        $name,
        $name_kana,
        $mail,
        $tel1,
        $tel2,
        $tel3,
        $postal_code1,
        $postal_code2,
        $pref,
        $city,
        $address,
        $other,
        $payment_id,
        $sub_price,
        $shipping_price,
        $tax,
        $total_price,
        $dbh
    ) {
        $sql =
            'INSERT '
            . 'INTO '
                . '`order` '
            . '('
                . 'user_id, '
                . 'name, '
                . 'name_kana, '
                . 'mail, '
                . 'tel1, '
                . 'tel2, '
                . 'tel3, '
                . 'postal_code1, '
                . 'postal_code2, '
                . 'pref, '
                . 'city, '
                . 'address, '
                . 'other, '
                . 'payment_id, '
                . 'sub_price, '
                . 'shipping_price, '
                . 'tax, '
                . 'total_price'
            . ') VALUES ('
                . '?, '
                . '?, '
                . '?, '
                . '?, '
                . '?, '
                . '?, '
                . '?, '
                . '?, '
                . '?, '
                . '?, '
                . '?, '
                . '?, '
                . '?, '
                . '?, '
                . '?, '
                . '?, '
                . '?, '
                . '?'
            . ')'
        ;
        $stmt = $dbh->prepare($sql);
        $stmt->execute([
            $user_id,
            $name,
            $name_kana,
            $mail,
            $tel1,
            $tel2,
            $tel3,
            $postal_code1,
            $postal_code2,
            $pref,
            $city,
            $address,
            ($other == '' ? null : $other),
            $payment_id,
            $sub_price,
            $shipping_price,
            $tax,
            $total_price
        ]);
    }

    /**
     * ページネーション
     *
     * @param int $offset
     * @return array order
     */
    public function paginate($offset)
    {
        $this->connect();
        $stmt = $this->dbh->query('SELECT * FROM `order` LIMIT ' . ($offset - 1) * 5 . ', 5');
        return $stmt->fetchAll();
    }

    /**
     * ページ数をカウント
     *
     * @return ページ数
     */
    public function countPage()
    {
        $this->connect();
        return $this->dbh->query('SELECT COUNT(*) FROM `order`')->fetch(PDO::FETCH_COLUMN);
    }

    /**
     * cartを空にし、order, order_detailに注文情報を登録する。メールの送信をする。
     *
     * @return void
     */
    public function completePurchase($purchaseInfo)
    {
        global $prefectures;
        try {
            $this->dbh->exec('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED');
            $this->dbh->beginTransaction();
            $userModel = new UserModel();
            $user = $userModel->fetchById($_SESSION['user']['user_id']);
            $this->commitOrder(
                $_SESSION['user']['user_id'],
                $purchaseInfo['name'],
                $purchaseInfo['name_kana'],
                $user['mail'],
                $purchaseInfo['tel1'],
                $purchaseInfo['tel2'],
                $purchaseInfo['tel3'],
                $purchaseInfo['postal_code1'],
                $purchaseInfo['postal_code2'],
                $purchaseInfo['pref'],
                $purchaseInfo['city'],
                $purchaseInfo['address'],
                $purchaseInfo['other'],
                $purchaseInfo['payment'],
                $purchaseInfo['sub_price'],
                $purchaseInfo['shipping'],
                $purchaseInfo['tax_price'],
                $purchaseInfo['total_price'],
                $this->dbh
            );
            $id = $this->dbh->lastInsertId();
            $productDetailModel = new ProductDetailModel();
            $productModel = new ProductModel();
            $oederDetailModel = new OrderDetailModel();
            $cartModel = new CartModel();
            $cart = $cartModel->fetchAll();
            foreach ($cart['cart'] as $item) {
                $productDetail = $productDetailModel->fetchById($item['product_detail_id']);
                $product = $productModel->fetchSingleProduct($productDetail['product_id']);
                $oederDetailModel->registerOrderDetail(
                    $id,
                    $item['product_detail_id'],
                    $product['name'],
                    $productDetail['size'],
                    $productDetail['price'],
                    $item['num'],
                    $this->dbh
                );
            }
            $cartModel->deleteFromCart();
            $mailBody =
                $_SESSION['user']['user_name'] . "様\n\n"
                . "お世話になっております。\n"
                . "洋菓子店カサミンゴーカスタマーサポートです。\n\n"
                . $_SESSION['user']['user_name'] . "様が購入手続きをされました商品について\n"
                . "お間違えのないようメールをお送りいたしました。\n"
                . "今一度ご購入商品等にお間違えなどないよう、ご確認いただけましたら幸いでございます。\n\n"
                . "--------------------------------------\n\n"
                . "【購入情報】\n\n"
            ;
            foreach ($cart['cart'] as $item) {
                $productDetail = $productDetailModel->fetchById($item['product_detail_id']);
                $product = $productModel->fetchSingleProduct($productDetail['product_id']);
                $mailBody .=
                    $product['name'] . "\n"
                    . $productDetail['size'] . "cm\n"
                    . $productDetail['price'] . "円\n"
                    . $item['num'] . "点\n\n"
                    . "-----------------------\n\n"
                ;
            }
            $mPaymentModel = new MPaymentModel();
            $payment = $mPaymentModel->fetchByid($purchaseInfo['payment']);
            $mailBody .=
                '小計： ' . $purchaseInfo['sub_price'] . "円\n"
                . '消費税： ' . $purchaseInfo['tax_price'] . "円\n"
                . '送料： ' . $purchaseInfo['shipping'] . "円\n"
                . '合計： ' . $purchaseInfo['total_price'] . "円\n\n"
                . "--------------------------------------\n\n"
                . "【送付先情報】\n\n"
                . 'お名前： ' . $purchaseInfo['name'] . "\n"
                . 'フリガナ： ' . $purchaseInfo['name_kana'] . "\n"
                . '電話番号： ' . $purchaseInfo['tel1'] . ' - ' . $purchaseInfo['tel2'] . ' - ' . $purchaseInfo['tel3'] . "\n"
                . '郵便番号： ' . $purchaseInfo['postal_code1'] . ' - ' . $purchaseInfo['postal_code2'] . "\n"
                . '都道府県： ' . $purchaseInfo['pref'] . "\n"
                . '市区町村： ' . $purchaseInfo['city'] . "\n"
                . '番地： ' . $purchaseInfo['address'] . "\n"
                . 'マンション名等： ' . $purchaseInfo['other'] . "\n\n"
                . "--------------------------------------\n\n"
                . "【請求先情報】\n\n"
                . 'お名前： ' . $user['name'] . "\n"
                . 'フリガナ： ' . $user['name_kana'] . "\n"
                . '電話番号： ' . $user['tel1'] . ' - ' . $user['tel2'] . ' - ' . $user['tel3'] . "\n"
                . '郵便番号： ' . $user['postal_code1'] . ' - ' . $user['postal_code2'] . "\n"
                . '都道府県： ' . $prefectures[$user['pref']] . "\n"
                . '市区町村： ' . $user['city'] . "\n"
                . '番地： ' . $user['address'] . "\n"
                . 'マンション名等： ' . $user['other'] . "\n"
                . 'お支払方法： ' . $payment['name'] . "\n\n"
                . "--------------------------------------\n\n"
                . "商品ご到着まで。今しばらくお待ちください。\n\n"
                . "※このメールは自動送信メールです。\n"
                . "※返信をされてもご回答しかねますのでご了承ください。\n\n"
                . "〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜\n"
                . "洋菓子店カサミンゴー\n"
                . "TEL：000-0000-0000\n"
                . "住所：福島県郡山市中ノ目3-149-12\n"
                . "mail：t.watanabe@ebacorp.jp\n"
                . '〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜'
            ;
            mb_language('japanese');
            mb_internal_encoding('UTF-8');
            if (!mb_send_mail(
                $user['mail'],
                '【洋菓子店カサミンゴー】ご購入商品確認メール',
                $mailBody,
                'From:' . mb_encode_mimeheader('洋菓子店カサミンゴー')
            )) {
                throw new Exception;
            }
            $this->dbh->commit();
        } catch (Exception $e) {
            $this->dbh->rollBack();
            throw new Exception($e);
        }
    }
}
