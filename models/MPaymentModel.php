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
        $sql = 'SELECT * FROM m_payment';
        $stmt = $this->dbh->query($sql);
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
        $sql = 'SELECT * FROM m_payment WHERE id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}