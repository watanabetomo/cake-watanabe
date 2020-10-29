<?php
class User extends Model
{
    public function fetchById($id)
    {
        $this->connect();
        $stmt = $this->dbh->prepare('SELECT login_pass FROM user WHERE login_id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

}