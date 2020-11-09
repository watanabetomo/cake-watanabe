<?php
class ProductModel extends Model
{

    /**
     * product_listとproduct_categoryを結合し、全件を取得する
     *
     * @return array product_listとproduct_categoryを結合した全件分のデータ
     */
    public function fetchAllData()
    {
        $this->connect();
        $stmt = $this->dbh->query('SELECT product.id, product.name, product.img, product.created_at, product.updated_at FROM product JOIN product_category ON product.product_category_id = product_category.id WHERE delete_flg = false');
        return $stmt->fetchAll();
    }

    /**
     * idをもとに、productとそれに紐づくproduct_detailのデータすべてを取得する。
     *
     * @param int $id
     * @return array idが一致したレコード一件分
     */
    public function fetchById($id)
    {
        $this->connect();
        $stmt = $this->dbh->prepare('SELECT product.id, product.name, product_category.name AS category_name, product.img, product.delivery_info, product.turn, product_detail.size, product_detail.price, product_detail.turn as detail_turn FROM product JOIN product_category ON product.product_category_id = product_category.id JOIN product_detail ON product.id = product_detail.product_id WHERE product.id = ? ORDER BY turn');
        $stmt->execute([$id]);
        return $stmt->fetchAll();
    }

    /**
     * 商品情報及び商品詳細の更新
     *
     * @param int $id
     * @param String $name
     * @param int $category_id
     * @param String $delivery_info
     * @param int $turn
     * @param int $update_user
     * @param array $size
     * @param array $price
     * @return void
     */
    public function update($id, $name, $category_id, $delivery_info, $turn, $update_user, $size, $price)
    {
        try {
            $productDetailModel = new ProductDetailModel();
            $this->connect();
            $this->dbh->exec('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED');
            $stmt = $this->dbh->prepare('UPDATE product SET name = ?, product_category_id = ?, delivery_info = ?, turn = ?, update_user = ?, updated_at = current_timestamp() WHERE id = ?');
            $this->dbh->beginTransaction();
            $stmt->execute([$name, $category_id, $delivery_info, $turn, $update_user, $id]);
            for ($i=0; $i<5; $i++) {
                $productDetailModel->update($id, $size[$i], $price[$i], $i + 1);
            }
            $this->dbh->commit();
        } catch (PDOException $e) {
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
    public function delete($id)
    {
        $this->connect();
        $stmt = $this->dbh->prepare('UPDATE product SET delete_flg = true WHERE id = ?');
        $stmt->execute([$id]);
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
     * 商品情報及び商品詳細の登録
     *
     * @param String $name
     * @param int $category_id
     * @param String $delivery_info
     * @param int $turn
     * @param int $create_user
     * @param array $size
     * @param array $price
     * @return void
     */
    public function register($name, $category_id, $delivery_info, $turn, $create_user, $size, $price)
    {
        try {
            $productDetailModel = new ProductDetailModel();
            $this->connect();
            $this->dbh->exec('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED');
            $stmt = $this->dbh->prepare('INSERT INTO product(name, product_category_id, delivery_info, turn, create_user) VALUES (?, ?, ?, ?, ?)');
            $this->dbh->beginTransaction();
            $stmt->execute([$name, $category_id, $delivery_info, $turn, $create_user]);
            for ($i=0; $i<5; $i++) {
                $productDetailModel->register($this->getMaxId()[0], $size[$i], $price[$i], $i + 1);
            }
            $this->dbh->commit();
        } catch (PDOException $e) {
            throw new PDOException($e);
            $this->dbh->rollback();
        }
    }

    /**
     * 画像アップロード及びDBの画像情報更新
     *
     * @param int $id
     * @param String $img
     * @param String $tempName
     * @param String $error
     * @return void
     */
    public function imgUpload($id, $img, $tempName, $error)
    {
        try{
            $this->connect();
            $this->dbh->exec('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED');
            $stmt = $this->dbh->prepare('UPDATE product SET img = ?, updated_at = CURRENT_TIMESTAMP() WHERE id = ?');
            $this->dbh->beginTransaction();
            $stmt->execute([$img, $id]);
            if ($error == UPLOAD_ERR_OK) {
                exec('sudo chmod 0777 ../' . IMG_PATH);
                if (!move_uploaded_file($tempName, '../' . IMG_PATH . mb_convert_encoding($img, 'cp932', 'utf8'))) {
                    throw new Exception;
                }
                exec('sudo chmod 0755 ../' . IMG_PATH);
            } elseif ($error == UPLOAD_ERR_NO_FILE) {
                throw new Exception;
            } else {
                throw new Exception;
            }
            $this->dbh->commit();
        } catch (Exception $e) {
            throw new Exception($e);
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

    /**
     * idをもとに一件分の商品情報を取得
     *
     * @param int $id
     * @return array 商品情報
     */
    public function fetchSingleDetail($id)
    {
        $this->connect();
        $stmt = $this->dbh->prepare('SELECT name, img FROM product WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * 検索及びソート結果を返す
     *
     * @param String $column
     * @param String $direction
     * @param String $key
     * @return void
     */
    public function displayResult($column, $direction, $key)
    {
        if ($key != '') {
            return $this->search($key);
        } elseif ($column == 'id') {
            if ($direction == '▼') {
                return $this->sortIdDesc();
            }
            return $this->sortIdAsc();
        } elseif ($column == 'name') {
            if ($direction == '▼') {
                return $this->sortNameDesc();
            }
            return $this->sortNameAsc();
        } elseif ($column == 'updated_at') {
            if ($direction == '▼') {
                return $this->sortUpdatedDesc();
            }
            return $this->sortUpdatedAsc();
        }
    }
}
