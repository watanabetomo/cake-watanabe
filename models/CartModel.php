<?php
class CartModel extends Model
{
    /**
     * カートに商品を追加
     *
     * @param int $detailId
     * @return void
     */
    public function addToCart($detailId)
    {
        $sql =
            'SELECT '
                . '* '
            . 'FROM '
                . 'cart '
            . 'WHERE '
                . 'product_detail_id = ?'
        ;
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$detailId]);
        $cart = $stmt->fetch();
        if (empty($cart)) {
            $sql =
                'INSERT '
                . 'INTO '
                    . 'cart '
                . '('
                    . 'user_id, '
                    . 'product_detail_id, '
                    . 'num'
                . ') VALUES ('
                    . '?, '
                    . '?, '
                    . '1'
                . ')'
            ;
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute([$_SESSION['user']['user_id'], $detailId]);
        } else {
            $this->addNum($detailId);
        }
    }

    /**
     * カートの中身を全件取得
     *
     * @return array cartの全件
     */
    public function fetchAll()
    {
        $sql =
            'SELECT '
                . '* '
            . 'FROM '
                . 'cart';
        $stmt = $this->dbh->query($sql);
        $cart = $stmt->fetchAll();
        $totalPrice = 0;
        $totalCount = 0;
        $productDetailModel = new ProductDetailModel();
        foreach ($cart as $item) {
            $productDetail = $productDetailModel->fetchById($item['product_detail_id']);
            $totalCount += $item['num'];
            $totalPrice += $item['num'] * $productDetail['price'];
        }
        return [
            'cart' => $cart,
            'totalPrice' => $totalPrice,
            'totalCount' => $totalCount,
            'shipping' => ($totalPrice > 10000) ? 0 : 1000
        ];
    }

    /**
     * カートから特定の商品を削除
     *
     * @param int $id
     * @return void
     */
    public function delete($id)
    {
        $sql =
            'DELETE '
            . 'FROM '
                . 'cart '
            . 'WHERE '
                . 'id = ?'
            ;
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$id]);
    }

    /**
     * 商品の数を変更
     *
     * @param int $num
     * @param int $id
     * @return void
     */
    public function changeNum($num, $id)
    {
        $sql =
            'UPDATE '
                . 'cart '
            . 'SET '
                . 'num = ? '
            . 'WHERE '
                . 'id = ?'
        ;
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$num, $id]);
    }

    /**
     * numに1を加算する
     *
     * @param int $id
     * @return void
     */
    public function addNum($id)
    {
        $sql =
            'SELECT '
                . 'num '
            . 'FROM '
                . 'cart '
            . 'WHERE '
                . 'product_detail_id = ?'
        ;
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$id]);
        $num = $stmt->fetch();
        $sql =
            'UPDATE '
                . 'cart '
            . 'SET '
                . 'num = ? '
            . 'WHERE '
                . 'product_detail_id = ?'
        ;
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$num['num'] + 1, $id]);
    }

    /**
     * カートの中を全削除
     *
     * @return void
     */
    public function deleteFromCart()
    {
        $sql =
            'DELETE '
            . 'FROM '
                . 'cart'
        ;
        $this->dbh->query($sql);
    }

    /**
     * cartを空にし、order, order_detailに注文情報を登録する。メールの送信をする。
     *
     * @return void
     */
    public function purchaseComplete($purchaseInfo)
    {
        global $prefectures;
        try {
            $cart = $this->fetchAll();
            $this->dbh->exec('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED');
            $this->dbh->beginTransaction();
            $userModel = new UserModel();
            $user = $userModel->fetchById($_SESSION['user']['user_id']);
            $orderModel = new OrderModel();
            $orderModel->commitOrder(
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
                TAX * 100,
                $purchaseInfo['total_price'],
                $this->dbh,
                2
            );
            $id = $this->dbh->lastInsertId();
            $productDetailModel = new ProductDetailModel();
            $productModel = new ProductModel();
            $oederDetailModel = new OrderDetailModel();
            $stockModel = new StockModel();
            foreach ($cart['cart'] as $item) {
                $productDetail = $productDetailModel->fetchById($item['product_detail_id']);
                $product = $productModel->fetchSingleProduct($productDetail['product_id']);
                $oederDetailModel->registOrderDetail(
                    $id,
                    $item['product_detail_id'],
                    $product['name'],
                    $productDetail['size'],
                    $productDetail['price'],
                    $item['num'],
                    $this->dbh
                );
                $stockModel->fluctuate($item['num'], $item['product_detail_id'], 1, $this->dbh);
            }
            $this->deleteFromCart();
            $mailBody =
                $_SESSION['user']['user_name'] . "様\n\n"
                . "お世話になっております。\n"
                . "洋菓子店カサミンゴーカスタマーサポートです。\n\n"
                . $_SESSION['user']['user_name'] . "様が購入手続きをされました商品について\n"
                . "お間違えのないようメールをお送りいたしました。\n"
                . "今一度ご購入商品等にお間違えなどないよう、ご確認いただけましたら幸いでございます。\n\n"
                . "--------------------------------------\n\n"
                . "【購入情報】\n\n";
            foreach ($cart['cart'] as $item) {
                $productDetail = $productDetailModel->fetchById($item['product_detail_id']);
                $product = $productModel->fetchSingleProduct($productDetail['product_id']);
                $mailBody .=
                    $product['name'] . "\n"
                    . $productDetail['size'] . "cm\n"
                    . $productDetail['price'] . "円\n"
                    . $item['num'] . "点\n\n"
                    . "-----------------------\n\n";
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
                . '〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜';
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