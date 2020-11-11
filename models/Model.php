<?php
require_once('UserModel.php');
require_once('OrderModel.php');
require_once('OrderDetailModel.php');
require_once('CartModel.php');
require_once('MPaymentModel.php');
require_once('ProductModel.php');
require_once('ProductCategoryModel.php');
require_once('ProductDetailModel.php');
require_once('AdminUserModel.php');

class Model
{
    protected $dbh;

    function __construct()
    {
        $this->connect();
    }

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
}