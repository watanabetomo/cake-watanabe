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
        $stmt = $this->dbh->prepare('SELECT id, login_pass, name FROM user WHERE login_id = ?');
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
        $stmt = $this->dbh->prepare('SELECT * FROM user WHERE id = ?');
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
        $stmt = $this->dbh->prepare('UPDATE user SET last_login_date = NOW() WHERE id = ?');
        $stmt->execute([$id]);
    }
}