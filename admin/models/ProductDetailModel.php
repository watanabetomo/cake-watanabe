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
        $stmt = $this->dbh->prepare('SELECT size, price, product_id, turn FROM product_detail WHERE product_id = ? ORDER BY turn ASC');
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
        try{
            $this->connect();
            $this->dbh->exec('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED');
            $stmt = $this->dbh->prepare('INSERT INTO product_detail(product_id, size, price, turn) VALUES(?, ?, ?, ?)');
            $this->dbh->beginTransaction();
            $stmt->execute([$id, $size, $price, $turn]);
            $this->dbh->commit();
        }catch(PDOException $e){
            throw new PDOException('商品詳細の登録に失敗しました');
            $this->dbh->rollback();
        }
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
        try{
            $this->connect();
            $this->dbh->exec('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED');
            $stmt = $this->dbh->prepare('UPDATE product_detail SET size = ?, price = ? WHERE product_id = ? AND turn = ?');
            $this->dbh->beginTransaction();
            $stmt->execute([$size, $price, $id, $turn]);
            $this->dbh->commit();
        }catch(PDOException $e){
            throw new PDOException('商品詳細の更新に失敗しました');
            $this->dbh->rollback();
        }
    }
}