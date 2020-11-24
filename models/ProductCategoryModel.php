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
        $sql =
            'SELECT '
                . 'id, '
                . 'name '
            . 'FROM '
                . 'product_category '
            . 'ORDER BY '
                . 'turn ASC'
        ;
        $stmt = $this->dbh->query($sql)->fetchAll();
        return $stmt;
    }

    /**
     * カテゴリー名からidを取得
     *
     * @param String $name
     * @return void
     */
    public function getIdByName($name)
    {
        $sql =
            'SELECT '
                . 'id '
            . 'FROM '
                . 'product_category '
            . 'WHERE '
                . 'name = ?'
        ;
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$name]);
        return $stmt->fetch();
    }

    public function getName($id)
    {
        $sql =
            'SELECT '
                . 'name '
            . 'FROM '
                . 'product_category '
            . 'WHERE '
                . 'id = ?'
        ;
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}