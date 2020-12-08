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
        $stockModel = new StockModel();
        if (!$stockModel->isStock($detailId)) {
            return '在庫数より多くは購入できません。（在庫数：' . $stockModel->getNum($detailId) . '個）';
        }
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
        if (!empty($cart)) {
            return $this->addNum($detailId);
        }
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
                . 'cart'
        ;
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
            'total_price' => $totalPrice,
            'total_count' => $totalCount,
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
     * @return String
     */
    public function changeNum($num, $id, $detailId)
    {
        $stockModel = new StockModel();
        $result = $stockModel->checkStock($detailId, $num);
        $sql =
            'UPDATE '
                . 'cart '
            . 'SET '
                . 'num = ? '
            . 'WHERE '
                . 'id = ?'
        ;
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$result['num'], $id]);
        if (isset($result['message'])) {
            return $result['message'];
        }
    }

    /**
     * numに1を加算する
     *
     * @param int $id
     * @return String
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
        $num = $stmt->fetch(PDO::FETCH_COLUMN);
        $stockModel = new StockModel();
        $result = $stockModel->checkStock($id, $num + 1);
        if (isset($result['message'])) {
            return $result['message'];
        }
        $sql =
            'UPDATE '
                . 'cart '
            . 'SET '
                . 'num = ? '
            . 'WHERE '
                . 'product_detail_id = ?'
        ;
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$num + 1, $id]);
    }

    /**
     * カートの中を全削除（completePurchase用）
     *
     * @return void
     */
    public function deleteFromCart($dbh)
    {
        $sql =
            'DELETE '
            . 'FROM '
                . 'cart'
        ;
        $dbh->query($sql);
    }

    /**
     * カートの中を全削除
     *
     * @return void
     */
    public function clearCart()
    {
        $sql =
            'DELETE '
            . 'FROM '
                . 'cart'
        ;
        $this->dbh->query($sql);
    }
}