<?php
class OrderModel extends Model
{
    /**
     * 注文情報の登録
     *
     * @param int $user_id
     * @param String $name
     * @param String $name_kana
     * @param String $mail
     * @param int $tel1
     * @param int $tel2
     * @param int $tel3
     * @param int $postal_code1
     * @param int $postal_code2
     * @param int $pref
     * @param String $city
     * @param String $address
     * @param String $other
     * @param int $payment_id
     * @param int $sub_price
     * @param int $shipping_price
     * @param int $tax
     * @param int $total_price
     * @param PDO $dbh
     * @return void
     */
    public function commitOrder(
        $user_id,
        $name,
        $name_kana,
        $mail,
        $tel1,
        $tel2,
        $tel3,
        $postal_code1,
        $postal_code2,
        $pref,
        $city,
        $address,
        $other,
        $payment_id,
        $sub_price,
        $shipping_price,
        $tax,
        $total_price,
        $dbh,
        $status
    ) {
        $sql =
            'INSERT '
            . 'INTO '
                . '`order` '
            . '('
                . 'user_id, '
                . 'name, '
                . 'name_kana, '
                . 'mail, '
                . 'tel1, '
                . 'tel2, '
                . 'tel3, '
                . 'postal_code1, '
                . 'postal_code2, '
                . 'pref, '
                . 'city, '
                . 'address, '
                . 'other, '
                . 'payment_id, '
                . 'sub_price, '
                . 'shipping_price, '
                . 'tax, '
                . 'total_price, '
                . 'status'
            . ') VALUES ('
                . '?, '
                . '?, '
                . '?, '
                . '?, '
                . '?, '
                . '?, '
                . '?, '
                . '?, '
                . '?, '
                . '?, '
                . '?, '
                . '?, '
                . '?, '
                . '?, '
                . '?, '
                . '?, '
                . '?, '
                . '?, '
                . '?'
            . ')'
        ;
        $stmt = $dbh->prepare($sql);
        $stmt->execute([
            $user_id,
            $name,
            $name_kana,
            $mail,
            $tel1,
            $tel2,
            $tel3,
            $postal_code1,
            $postal_code2,
            $pref,
            $city,
            $address,
            ($other == '' ? null : $other),
            $payment_id,
            $sub_price,
            $shipping_price,
            $tax,
            $total_price,
            $status
        ]);
    }

    /**
     * ページネーション
     *
     * @param int $offset
     * @return array order
     */
    public function pagination($offset)
    {
        $this->connect();
        $stmt = $this->dbh->query('SELECT * FROM `order` LIMIT ' . ($offset - 1) * 5 . ', 5');
        return $stmt->fetchAll();
    }

    /**
     * ページ数をカウント
     *
     * @return ページ数
     */
    public function countPage()
    {
        $this->connect();
        return $this->dbh->query('SELECT COUNT(*) FROM `order`')->fetch(PDO::FETCH_COLUMN);
    }

    /**
     * ステータスをキャンセルに変更する
     *
     * @param int $id
     * @return void
     */
    public function cancel($id)
    {
        $sql =
            'UPDATE '
                . 'order '
            . 'SET '
                . 'status = 3 '
            . 'WHERE '
                . 'id = ?'
        ;
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$id]);
    }
}
