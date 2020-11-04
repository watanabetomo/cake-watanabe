<?php
class OrderModel extends Model
{
    public function commitOrder($user_id, $name, $name_kana, $mail, $tel1, $tel2, $tel3, $postal_code1, $postal_code2, $pref, $city, $address, $other, $payment_id, $sub_price, $shipping_price, $tax, $total_price){
        $this->connect();
        $stmt = $this->dbh->prepare('INSERT INTO order(user_id, name, name_kana, mail, tel1, tel2, tel3, postal_code1, postal_code2, pref, city, address, other, payment_id, sub_price, shipping_price, tax, total_price, updated_at) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP(6)');
        $stmt->execute([$user_id, $name, $name_kana, $mail, $tel1, $tel2, $tel3, $postal_code1, $postal_code2, $pref, $city, $address, $other, $payment_id, $sub_price, $shipping_price, $tax, $total_price]);
    }
}