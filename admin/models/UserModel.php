<?php
class User extends Model
{
    public function fetchById($id)
    {
        $this->connect();
        $stmt = $this->dbh->prepare('SELECT id, login_pass FROM user WHERE login_id = ?');
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