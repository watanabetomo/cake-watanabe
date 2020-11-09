<?php
class AdminUserModel extends Model
{
    /**
     * ログインidをもとにadmin_userテーブルからデータを取得し、fetchしたものを返す
     *
     * @param String $id
     * @return array admin_userのレコード一件分
     */
    public function fetchAdminUser($id)
    {
        $this->connect();
        $stmt = $this->dbh->prepare('SELECT * FROM admin_user WHERE delete_flg = false AND login_id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}