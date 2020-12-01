<?php
class StockModel extends Model
{
    /**
     * 在庫数を増減させる
     *
     * @param int $num
     * @param int $id
     * @param int $code
     * @return void
     */
    public function fluctuate($num, $id, $code)
    {
        $sql =
            'UPDATE '
                . 'stock '
            . 'SET '
                . 'num = ? '
            . 'WHERE '
                . 'product_detail_id = ?'
        ;
        $stmt = $this->dbh->prepare($sql);
        if ($code == 1) {
            $num = $this->getNum($id) + $num;
        } elseif ($code == 0) {
            $num = $this->getNum($id) - $num;
        }
        $stmt->execute([$num, $id]);
    }

    /**
     * 在庫数を取得
     *
     * @param int $id
     * @return int num
     */
    public function getNum($id)
    {
        $sql =
            'SELECT '
                . 'num '
            . 'FROM '
                . 'stock '
            . 'WHERE '
                . 'product_detail_id = ?'
        ;
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_COLUMN);
    }

}