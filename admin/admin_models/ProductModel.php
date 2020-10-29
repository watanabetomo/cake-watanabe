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
        $stmt = $this->dbh->prepare('SELECT product.id, product.name, product_category.name AS category_name, product.img, product.delivery_info, product.turn, product_detail.size, product_detail.price, product_detail.turn as detail_turn FROM product JOIN product_category ON product.product_category_id = product_category.id JOIN product_detail ON product.id = product_detail.product_id WHERE product.id = ? ORDER BY turn');
        $stmt->execute([$id]);
        return $stmt->fetchAll();
    }

    /**
     * データの更新
     *
     * @param int $id
     * @param String $name
     * @param int $category_id
     * @param String $delivery_info
     * @param int $turn
     * @param String $update_user
     * @return void
     */
    public function update($id, $name, $category_id, $delivery_info, $turn, $update_user){
        try{
            $this->connect();
            $this->dbh->exec('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED');
            $stmt = $this->dbh->prepare('UPDATE product SET name = ?, product_category_id = ?, delivery_info = ?, turn = ?, update_user = ?, updated_at = current_timestamp() WHERE id = ?');
            $this->dbh->beginTransaction();
            $stmt->execute([$name, $category_id, $delivery_info, $turn, $update_user, $id]);
            $this->dbh->commit();
        }catch(PDOException $e){
            throw new PDOException($e);
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
            throw new PDOException($e);
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
        $stmt = $this->dbh->prepare('SELECT id, name, img FROM product WHERE product_category_id = ? AND delete_flg = false ORDER BY turn IS NULL ASC');
        $stmt->execute([$id]);
        return $stmt->fetchAll();
    }

    /**
     * データの登録
     *
     * @param String $name
     * @param int $category_id
     * @param String $delivery_info
     * @param int $turn
     * @param int $create_user
     * @return void
     */
    public function register($name, $category_id, $delivery_info, $turn, $create_user)
    {
        try{
            $this->connect();
            $this->dbh->exec('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED');
            $stmt = $this->dbh->prepare('INSERT INTO product(name, product_category_id, delivery_info, turn, create_user) VALUES (?, ?, ?, ?, ?)');
            $this->dbh->beginTransaction();
            $stmt->execute([$name, $category_id, $delivery_info, $turn, $create_user]);
            $this->dbh->commit();
        }catch(PDOException $e){
            throw new PDOException($e);
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
            $stmt = $this->dbh->prepare('UPDATE product SET img = ? WHERE id = ?');
            $this->dbh->beginTransaction();
            $stmt->execute([$img, $id]);
            $this->dbh->commit();
        }catch(PDOException $e){
            throw new PDOException($e);
            $this->dbh->rollback();
        }
    }

    /**
     * idの最大値を取得
     *
     * @return MAX(id)
     */
    public function getMaxId()
    {
        $this->connect();
        return $this->dbh->query('SELECT MAX(id) FROM product')->fetch();
    }

    /**
     * 検索ワードをnameに含むデータを取得
     *
     * @param String $keyword
     * @return array 検索結果
     */
    public function search($keyword)
    {
        $this->connect();
        $stmt = $this->dbh->prepare('SELECT product.id, product.name, product.img, product.created_at, product.updated_at FROM product JOIN product_category ON product.product_category_id = product_category.id WHERE product.name LIKE ? AND delete_flg = false');
        $stmt->execute(['%' . $keyword . '%']);
        return $stmt->fetchAll();
    }

    /**
     * idの昇順でproductテーブルの中身を取得を取得
     *
     * @return array idの昇順で並んだproductテーブルのレコード
     */
    public function sortIdAsc()
    {
        $this->connect();
        $stmt = $this->dbh->query('SELECT product.id, product.name, product.img, product.created_at, product.updated_at FROM product JOIN product_category ON product.product_category_id = product_category.id WHERE delete_flg = false ORDER BY product.id ASC');
        return $stmt->fetchAll();
    }

    /**
     * idの降順でproductテーブルの中身を取得を取得
     *
     * @return array idの降順で並んだproductテーブルのレコード
     */
    public function sortIdDesc()
    {
        $this->connect();
        $stmt = $this->dbh->query('SELECT product.id, product.name, product.img, product.created_at, product.updated_at FROM product JOIN product_category ON product.product_category_id = product_category.id WHERE delete_flg = false ORDER BY product.id DESC');
        return $stmt->fetchAll();
    }

    /**
     * nameの昇順でproductテーブルの中身を取得を取得
     *
     * @return array nameの昇順で並んだproductテーブルのレコード
     */
    public function sortNameAsc()
    {
        $this->connect();
        $stmt = $this->dbh->query('SELECT product.id, product.name, product.img, product.created_at, product.updated_at, CASE WHEN product.name = "" THEN 1 ELSE 0 END AS dummy FROM product JOIN product_category ON product.product_category_id = product_category.id WHERE delete_flg = false ORDER BY dummy ASC, product.name DESC');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * nameの降順でproductテーブルの中身を取得を取得
     *
     * @return array nameの降順で並んだproductテーブルのレコード
     */
    public function sortNameDesc()
    {
        $this->connect();
        $stmt = $this->dbh->query('SELECT product.id, product.name, product.img, product.created_at, product.updated_at, CASE WHEN product.name = "" THEN 1 ELSE 0 END AS dummy FROM product JOIN product_category ON product.product_category_id = product_category.id WHERE delete_flg = false ORDER BY dummy ASC, product.name ASC');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     *updated_atの昇順でproductテーブルの中身を取得を取得
     *
     * @return arrayupdated_atの昇順で並んだproductテーブルのレコード
     */
    public function sortUpdatedAsc()
    {
        $this->connect();
        $stmt = $this->dbh->query('SELECT product.id, product.name, product.img, product.created_at, product.updated_at FROM product JOIN product_category ON product.product_category_id = product_category.id WHERE delete_flg = false ORDER BY product.updated_at IS NULL ASC, product.updated_at DESC');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * updated_atの降順でproductテーブルの中身を取得を取得
     *
     * @return array updated_atの降順で並んだproductテーブルのレコード
     */
    public function sortUpdatedDesc()
    {
        $this->connect();
        $stmt = $this->dbh->query('SELECT product.id, product.name, product.img, product.created_at, product.updated_at FROM product JOIN product_category ON product.product_category_id = product_category.id WHERE delete_flg = false ORDER BY product.updated_at IS NULL ASC, product.updated_at ASC');
        $stmt->execute();
        return $stmt->fetchAll();
    }
}