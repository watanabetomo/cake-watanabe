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
        $stmt = $this->dbh->query('SELECT * FROM m_payment');
        return $stmt->fetchAll();
    }

    /**
     * idをもとに支払方法を取得
     *
     * @param int $id
     * @return void
     */
    public function fetchByid($id)
    {
        $stmt = $this->dbh->prepare('SELECT * FROM m_payment WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}