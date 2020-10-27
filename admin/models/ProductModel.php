<?php
class ProductModel extends Model{

    /**
     * product_listとproduct_categoryを結合し、全件を取得する
     *
     * @return array product_listとproduct_categoryを結合した全件分のデータ
     */
    public function fetchAllData(){
        $this->connect();
        $stmt = $this->dbh->query('SELECT product.id, product.name, product.img, product.created_at, product.updated_at FROM product JOIN product_category ON product.product_category_id = product_category.id WHERE delete_flg = false');
        return $stmt->fetchAll();
    }

    /**
     * idをもとにレコードを取得する
     *
     * @param int $id
     * @return array idが一致したレコード一件分
     */
    public function fetchById($id){
        $this->connect();
        $stmt = $this->dbh->prepare('SELECT product.id, product.name, product_category.name AS category_name, product.img, product.delivery_info, product.`order` FROM product JOIN product_category ON product.product_category_id = product_category.id WHERE product.id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * データの更新
     *
     * @param int $id
     * @param String $name
     * @param int $category_id
     * @param String $delivery_info
     * @param int $order
     * @param String $update_user
     * @return void
     */
    public function update($id, $name, $category_id, $delivery_info, $order, $update_user){
        try{
            $this->connect();
            $this->dbh->exec('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED');
            $stmt = $this->dbh->prepare('UPDATE product SET name = ?, product_category_id = ?, delivery_info = ?, `order` = ?, update_user = ?, updated_at = current_timestamp() WHERE id = ?');
            $this->dbh->beginTransaction();
            $stmt->execute([$name, $category_id, $delivery_info, $order, $update_user, $id]);
            $this->dbh->commit();
        }catch(PDOException $e){
            throw new PDOException('データの更新に失敗しました');
            $this->dbh->rollback();
        }
    }

    /**
     * データの削除（削除フラグを立てる）
     *
     * @param int $id
     * @return void
     */
    public function delete($id){
        try{
            $this->connect();
            $this->dbh->exec('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED');
            $stmt = $this->dbh->prepare('UPDATE product SET delete_flg = true WHERE id = ?');
            $this->dbh->beginTransaction();
            $stmt->execute([$id]);
            $this->dbh->commit();
        }catch(PDOException $e){
            throw new PDOException('削除に失敗しました');
            $this->dbh->rollback();
        }
    }

    /**
     * カテゴリーIDでデータ取得
     *
     * @param int $id
     * @return array product_category_idで取得したレコード一件分
     */
    public function fetchByCategoryId($id)
    {
        $this->connect();
        $stmt = $this->dbh->prepare('SELECT id, name, img FROM product WHERE product_category_id = ? AND delete_flg = false ORDER BY `order` IS NULL ASC');
        $stmt->execute([$id]);
        return $stmt->fetchAll();
    }

    /**
     * データの登録
     *
     * @param String $name
     * @param int $category_id
     * @param String $delivery_info
     * @param int $order
     * @param int $create_user
     * @return void
     */
    public function register($name, $category_id, $delivery_info, $order, $create_user)
    {
        try{
            $this->connect();
            $this->dbh->exec('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED');
            $stmt = $this->dbh->prepare('INSERT INTO product(name, product_category_id, delivery_info, ,`order` create_user) VALUES (?, ?, ?, ?, ?)');
            $this->dbh->beginTransaction();
            $stmt->execute([$name, $category_id, $delivery_info, $order, $create_user]);
            $this->dbh->commit();
        }catch(PDOException $e){
            throw new PDOException('登録に失敗しました');
            $this->dbh->rollback();
        }
    }

    /**
     * 画像ファイル名を登録
     *
     * @param int $id
     * @param String $img
     * @return void
     */
    public function imgUpload($id, $img){
        try{
            $this->connect();
            $this->dbh->exec('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED');
            $this->dbh->beginTransaction();
            $stmt = $this->dbh->prepare('UPDATE product SET img = ? WHERE id = ?');
            $stmt->execute([$img, $id]);
            $this->dbh->commit();
        }catch(PDOException $e){
            throw new PDOException('画像の登録に失敗しました');
            $this->dbh->rollback();
        }
    }
}