<?php
class OrderdetailModel extends Model
{
    public function registOrderDetail($orderId, $detailId, $name, $size, $price, $num)
    {
        $this->connect();
        $stmt = $this->dbh->prepare('INSERT INTO order_detail(order_id, product_detail_id, name, size, price, num) VALUES(?, ?, ?, ?, ?, ?)');
        $stmt->execute([$orderId, $detailId, $name, $size, $price, $num]);
    }
}