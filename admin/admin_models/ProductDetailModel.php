<?php
class ProductDetailModel extends AdminModel
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
        $stmt = $this->dbh->prepare('SELECT * FROM product_detail WHERE product_id = ? ORDER BY size ASC');
        $stmt->execute([$id]);
        return $stmt->fetchAll();
    }

    /**
     * 商品詳細の登録
     *
     * @param int $id
     * @param int $size
     * @param int $price
     * @param int $turn
     * @return void
     */
    public function register($id, $size, $price, $turn)
    {
        $this->connect();
        $stmt = $this->dbh->prepare('INSERT INTO product_detail(product_id, size, price, turn) VALUES(?, ?, ?, ?)');
        $stmt->execute([$id, $size, $price, $turn]);
    }

    /**
     * product_idとturnを指定し、そのレコードのsizeとpriceを更新する
     *
     * @param int $id
     * @param int $size
     * @param int $price
     * @param int $turn
     * @return void
     */
    public function update($id, $size, $price, $turn)
    {
        $this->connect();
        $stmt = $this->dbh->prepare('UPDATE product_detail SET size = ?, price = ? WHERE product_id = ? AND turn = ?');
        $stmt->execute([$size, $price, $id, $turn]);
    }

    public function fetchById($id)
    {
        $this->connect();
        $stmt = $this->dbh->prepare('SELECT * FROM product_detail WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
