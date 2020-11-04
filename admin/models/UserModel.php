<?php
class UserModel extends Model
{
    public function fetchByLoginId($id)
    {
        $this->connect();
        $stmt = $this->dbh->prepare('SELECT id, login_pass, name FROM user WHERE login_id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function fetchById($id)
    {
        $this->connect();
        $stmt = $this->dbh->prepare('SELECT * FROM user WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function updateLoginDate($id)
    {
        $this->connect();
        $stmt = $this->dbh->prepare('UPDATE user SET last_login_date = NOW() WHERE id = ?');
        $stmt->execute([$id]);
    }

}