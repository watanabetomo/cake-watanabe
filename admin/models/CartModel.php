<?php
class CartModel extends Model{
    /**
     * カートに商品を追加
     *
     * @param int $userId
     * @param int $detailId
     * @return void
     */
    public function addToCart($userId, $detailId){
        $this->connect();
        $stmt = $this->dbh->prepare('INSERT INTO cart(user_id, product_detail_id, num) VALUES(?, ?, 1)');
        $stmt->execute([$userId, $detailId]);
    }


    public function fetchAll()
    {
        $this->connect();
        $stmt = $this->dbh->query('SELECT * FROM cart');
        return $stmt->fetchAll();
    }

    public function delete($id)
    {
        $this->connect();
        $stmt = $this->dbh->prepare('DELETE FROM cart WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function changeNum($num, $id)
    {
        $this->connect();
        $stmt = $this->dbh->prepare('UPDATE cart SET num = ? WHERE id = ?');
        $stmt->execute([$num, $id]);
    }

    public function truncateCart()
    {
        $this->connect();
        $this->dbh->query('TRUNCATE TABLE cart');
    }
}