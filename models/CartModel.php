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
        if (!($stockModel->checkStock($detailId, $num))) {
            return '在庫数を超えて商品をカートに入れることはできません。(在庫数：' . $stockModel->getNum($detailId) . '個)';
        }
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
        if (!($stockModel->checkStock($id, $num + 1))) {
            return '在庫数を超えて商品をカートに入れることはできません。(在庫数：' . $stockModel->getNum($id) . '個)';
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
}