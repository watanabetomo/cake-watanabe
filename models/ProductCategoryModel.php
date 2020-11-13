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
        $stmt = $this->dbh->query('SELECT id, name FROM product_category ORDER BY turn ASC');
        return $stmt->fetchAll();
    }

    /**
     * カテゴリー名からidを取得
     *
     * @param String $name
     * @return void
     */
    public function getIdByName($name)
    {
        $stmt = $this->dbh->prepare('SELECT id FROM product_category WHERE name = ?');
        $stmt->execute([$name]);
        return $stmt->fetch();
    }

    public function getName($id)
    {
        $stmt = $this->dbh->prepare('SELECT name FROM product_category WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}