<?php
class OrderdetailModel extends Model
{
    /**
     * 注文詳細の登録
     *
     * @param int $orderId
     * @param int $detailId
     * @param String $name
     * @param int $size
     * @param int $price
     * @param int $num
     * @param PDO $dbh
     * @return void
     */
    public function registerOrderDetail($orderId, $detailId, $name, $size, $price, $num, $dbh)
    {
        $sql =
            'INSERT '
            . 'INTO '
                . 'order_detail'
            . '('
                . 'order_id, '
                . 'product_detail_id, '
                . 'name, '
                . 'size, '
                . 'price, '
                . 'num'
            . ') VALUES ('
                . '?, '
                . '?, '
                . '?, '
                . '?, '
                . '?, '
                . '?'
            . ')'
        ;
        $stmt = $dbh->prepare($sql);
        $stmt->execute([$orderId, $detailId, $name, $size, $price, $num]);
    }

    /**
     * 注文詳細の取得
     *
     * @param int $id
     * @return array order_detail
     */
    public function getOrderDetail($id)
    {
        $this->connect();
        $stmt = $this->dbh->prepare('SELECT * FROM order_detail WHERE order_id = ?');
        $stmt->execute([$id]);
        return $stmt->fetchAll();
    }
}