<?php
class MPaymentModel extends Model
{
    /**
     * 支払方法を全件取得
     *
     * @return array m_paymentの全件
     */
    public function fetchAll()
    {
        $this->connect();
        $stmt = $this->dbh->query('SELECT * FROM m_payment');
        return $stmt->fetchAll();
    }
}