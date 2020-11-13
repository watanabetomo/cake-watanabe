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
    public function registOrderDetail($orderId, $detailId, $name, $size, $price, $num, $dbh)
    {
        $stmt = $dbh->prepare('INSERT INTO order_detail(order_id, product_detail_id, name, size, price, num) VALUES(?, ?, ?, ?, ?, ?)');
        $stmt->execute([$orderId, $detailId, $name, $size, $price, $num]);
    }
}