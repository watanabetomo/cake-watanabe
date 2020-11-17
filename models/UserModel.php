<?php
class UserModel extends Model
{
    /**
     * login_idをもとにユーザデータを取得する
     *
     * @param int $id
     * @return array ユーザデータ
     */
    public function fetchByLoginId($id)
    {
        $sql = 'SELECT id, login_pass, name FROM user WHERE login_id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * idをもとにユーザデータを取得する
     *
     * @param int $id
     * @return array ユーザデータ
     */
    public function fetchById($id)
    {
        $sql = 'SELECT * FROM user WHERE id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * last_login_dateを更新する
     *
     * @param int $id
     * @return void
     */
    public function updateLoginDate($id)
    {
        $sql = 'UPDATE user SET last_login_date = NOW() WHERE id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$id]);
    }
}