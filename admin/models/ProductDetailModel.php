<?php
class ProductDetailModel extends Model
{
    /**
     * product_idでsizeとpriceを取ってくる
     *
     * @param int $id
     * @return array sizeとpriceの配列
     */
    public function fetchByProductId($id)
    {
        $this->connect();
        $stmt = $this->dbh->prepare('SELECT size, price, product_id FROM product_detail WHERE product_id = ? ORDER BY size IS NULL ASC');
        $stmt->execute([$id]);
        return $stmt->fetchAll();
    }
}