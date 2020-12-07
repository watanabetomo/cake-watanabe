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
    public function fluctuate($num, $id, $code, $dbh)
    {
        $sql =
            'UPDATE '
                . 'stock '
            . 'SET '
                . 'actual_num = ? '
            . 'WHERE '
                . 'product_detail_id = ?'
        ;
        $stmt = $dbh->prepare($sql);
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
                . 'actual_num '
            . 'FROM '
                . 'stock '
            . 'WHERE '
                . 'product_detail_id = ?'
        ;
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_COLUMN);
    }

    /**
     * 在庫と個数を比較
     *
     * @param int $id
     * @param int $num
     * @return boolean
     */
    public function checkStock($id, $num)
    {
        return $this->getNum($id) >= $num;
    }

    /**
     * 在庫があるかどうか
     *
     * @param int $detailId
     * @return boolean
     */
    public function isStock($detailId)
    {
        return $this->getNum($detailId) > 0;
    }

    /**
     * 新規登録
     *
     * @param int $detailId
     * @param int $num
     * @param PDO $dbh
     * @return void
     */
    public function registerNum($detailId, $num, $maxNum, $dbh)
    {
        $sql =
            'INSERT '
            . 'INTO '
                . 'stock '
            . '('
                . 'product_detail_id, '
                . 'actual_num'
            . ') VALUES ('
                . '?, '
                . '?, '
                . '?'
            . ')'
        ;
        $stmt = $dbh->prepare($sql);
        return $stmt->execute([$detailId, $num, $maxNum]);
    }

    /**
     * 購入上限個数の更新
     *
     * @param int $detailId
     * @param int $num
     * @param PDO $dbh
     * @return void
     */
    public function changeMaxNum($detailId, $num, $dbh)
    {
        $sql =
            'UPDATE '
                . 'stock '
            . 'SET '
                . 'max_num = ? '
            . 'WHERE '
                . 'product_detail_id = ?'
        ;
        $stmt = $dbh->prepare($sql);
        $stmt->execute([$num, $detailId]);
    }


}