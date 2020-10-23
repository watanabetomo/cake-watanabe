<?php
class ProductCategoryModel extends Model
{
    /**
     * nameをすべて取得する
     *
     * @return array 全件のnameを保持する配列
     */
    public function fetchAllName()
    {
        $this->connect();
        return $this->dbh->query('SELECT id, name FROM product_category ORDER BY `order` IS NULL ASC')->fetchAll();
    }

    public function getIdByName($name)
    {
        $this->connect();
        $stmt = $this->dbh->prepare('SELECT id FROM product_category WHERE name = ?');
        $stmt->execute([$name]);
        return $stmt->fetch();
    }
}