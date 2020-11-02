<?php
require_once('AdminUserModel.php');
require_once('ProductModel.php');
require_once('ProductCategoryModel.php');
require_once('ProductDetailModel.php');

class Model
{
    protected $dbh;

    /**
     * PDOインスタンスを生成し、dbhプロパティに代入
     *
     * @return void
     */
    public function connect()
    {
        try {
            $this->dbh = new PDO('mysql:host=' . HOST . ';dbname=' . DBNAME, DBUSER, DBPASS);
            $this->dbh->exec('set names utf8');
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $e) {
            throw new Exception($e);
        }
    }

    public function getDbh()
    {
        return $this->dbh;
    }
}