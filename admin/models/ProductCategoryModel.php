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
        return $this->dbh->query('SELECT id, name FROM product_category ORDER BY turn IS NULL ASC')->fetchAll();
    }

    /**
     * カテゴリー名からidを取得
     *
     * @param String $name
     * @return void
     */
    public function getIdByName($name)
    {
        $this->connect();
        $stmt = $this->dbh->prepare('SELECT id FROM product_category WHERE name = ?');
        $stmt->execute([$name]);
        return $stmt->fetch();
    }
}