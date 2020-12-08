<?php
class ProductDetailModel extends Model
{
    /**
     * product_idでsizeとpriceを取ってくる
     *
     * @param int $id
     * @return array sizeとpriceの配列
     */
    public function getDetails($id)
    {
        $sql =
            'SELECT '
                . 'product_detail.size, '
                . 'product_detail.price, '
                . 'stock.max_num '
            . 'FROM '
                . 'product_detail '
            . 'LEFT OUTER JOIN '
                . 'stock '
            . 'ON '
                . 'product_detail.id = stock.product_detail_id '
            . 'WHERE '
                . 'product_id = ?'
        ;
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetchAll();
    }

    /**
     * 在庫がある商品のみ取得
     *
     * @param int $id
     * @return array sizeとpriceの配列
     */
    public function fetchByProductId($id)
    {
        $sql =
            'SELECT '
                . 'product_detail.id AS id, '
                . 'product_detail.product_id, '
                . 'product_detail.size, '
                . 'product_detail.price, '
                . 'stock.actual_num '
            . 'FROM '
                . 'product_detail '
            . 'LEFT OUTER JOIN '
                . 'stock '
            . 'ON '
                . 'product_detail.id = stock.product_detail_id '
            . 'WHERE '
                . 'product_detail.product_id = ? '
            . 'ORDER BY '
                . 'product_detail.size ASC'
        ;
        $stmt = $this->dbh->prepare($sql);
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
     * @param PDO $dbh
     * @return void
     */
    public function register($id, $size, $price, $turn, $dbh)
    {
        $sql =
            'INSERT '
            . 'INTO '
                . 'product_detail '
            . '('
                . 'product_id, '
                . 'size, '
                . 'price, '
                . 'turn'
            . ') VALUES ('
                . '?, '
                . '?, '
                . '?, '
                . '?'
            . ')'
        ;
        $stmt = $dbh->prepare($sql);
        $stmt->execute([$id, $size, $price, $turn]);
    }

    /**
     * product_idとturnを指定し、そのレコードのsizeとpriceを更新する
     *
     * @param int $id
     * @param int $size
     * @param int $price
     * @param int $turn
     * @param PDO $dbh
     * @return void
     */
    public function update($id, $size, $price, $turn, $dbh)
    {
        $sql =
            'UPDATE '
                . 'product_detail '
            . 'SET '
                . 'size = ?, '
                . 'price = ? '
            . 'WHERE '
                . 'product_id = ? '
                . 'AND turn = ?'
        ;
        $stmt = $dbh->prepare($sql);
        $stmt->execute([$size, $price, $id, $turn]);
    }

    /**
     * idをもとに商品詳細を取得
     *
     * @param int $id
     * @return array 商品詳細
     */
    public function fetchById($id)
    {
        $sql =
            'SELECT '
                . '* '
            . 'FROM '
                . 'product_detail '
            . 'WHERE '
                . 'id = ?'
        ;
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getId($id, $turn)
    {
        $sql =
            'SELECT '
                . 'id '
            . 'FROM '
                . 'product_detail '
            . 'WHERE '
                . 'product_id = ? '
                . 'AND turn = ?'
        ;
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$id, $turn]);
        return $stmt->fetch(PDO::FETCH_COLUMN);
    }
}
