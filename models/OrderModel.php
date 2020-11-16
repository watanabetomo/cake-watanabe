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
    public function commitOrder( $user_id,$name,$name_kana,$mail,$tel1,$tel2,$tel3,$postal_code1,$postal_code2,$pref,$city,$address,$other,$payment_id,$sub_price,$shipping_price,$tax,$total_price,$dbh)
    {
        $stmt = $dbh->prepare('INSERT INTO `order`(user_id, name, name_kana, mail, tel1, tel2, tel3, postal_code1, postal_code2, pref, city, address, other, payment_id, sub_price, shipping_price, tax, total_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$user_id, $name, $name_kana, $mail, $tel1, $tel2, $tel3, $postal_code1, $postal_code2, $pref, $city, $address, $other, $payment_id, $sub_price, $shipping_price, $tax, $total_price]);
    }

    /**
     * orderテーブルのid最大値を取得する
     *
     * @return array orderテーブルのidの最大値(要素一つの配列)
     */
    public function getMaxId()
    {
        $stmt = $this->dbh->query('SELECT MAX(id) FROM `order`');
        return $stmt->fetch();
    }
}
