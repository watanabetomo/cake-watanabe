<?php
class ProductModel extends Model
{

    /**
     * product_listとproduct_categoryを結合し、データを取得する
     *
     * @param $get GETパラメータ
     *
     * @return array product_listとproduct_categoryを結合したのデータ
     */
    public function getProduct($get)
    {
        $sql =
        'SELECT '
            . 'product.id, '
            . 'product.name, '
            . 'product.img, '
            . 'product.created_at, '
            . 'product.updated_at '
        . 'FROM '
            . 'product '
        . 'JOIN product_category '
            . 'ON product.product_category_id = product_category.id '
        . 'WHERE '
            . 'delete_flg = false '
        . ((isset($get['keyword']) and $get['keyword'] != '') ? ' AND product.name LIKE ?' : '')
        . (isset($get['order']) ? ' ORDER BY product.' . $get['column'] .  ' IS NULL ASC, product.' . $get['column'] . ' ' . $get['order'] : '');
        $stmt = $this->dbh->prepare($sql);
        if (isset($get['keyword'])) {
            $stmt->execute(['%' . $get['keyword'] . '%']);
        } else {
            $stmt->execute();
        }
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
     * @param array $details
     * @return void
     */
    public function update($id, $name, $category_id, $delivery_info, $turn, $update_user, $details)
    {
        try {
            $this->dbh->exec('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED');
            $this->dbh->beginTransaction();
            $sql =
                'UPDATE '
                    . 'product '
                . 'SET '
                    . 'name = ?, '
                    . 'product_category_id = ?, '
                    . 'delivery_info = ?, '
                    . 'turn = ?, '
                    . 'update_user = ?, '
                    . 'updated_at = current_timestamp() '
                . 'WHERE'
                    . 'id = ?';
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute([
                $name == '' ? null : $name,
                $category_id,
                $delivery_info == '' ? null : $delivery_info,
                $turn == '' ? null : $turn,
                $update_user,
                $id
            ]);
            $productDetailModel = new ProductDetailModel();
            for ($i = 0; $i < 5; $i++) {
                $productDetailModel->update(
                    $id,
                    $details[$i]['size'] == '' ? null : $details[$i]['size'],
                    $details[$i]['price'] == '' ? null : $details[$i]['price'],
                    $i + 1,
                    $this->dbh
                );
            }
            $this->dbh->commit();
        } catch (PDOException $e) {
            $this->dbh->rollBack();
            throw new PDOException($e);
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
        $sql = 'UPDATE product SET delete_flg = true WHERE id = ?';
        $stmt = $this->dbh->prepare($sql);
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
        $sql =
            'SELECT '
                . 'id, '
                . 'name, '
                . 'img '
            . 'FROM '
                . 'product '
            . 'WHERE '
                . 'product_category_id = ? '
                . 'AND delete_flg = false '
            . 'ORDER BY '
                . 'turn ASC';
        $stmt = $this->dbh->prepare($sql);
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
     * @param array $details
     * @return void
     */
    public function register($name, $category_id, $delivery_info, $turn, $create_user, $details)
    {
        try {
            $this->dbh->exec('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED');
            $this->dbh->beginTransaction();
            $sql =
                'INSERT INTO '
                    . 'product (name, product_category_id, delivery_info, turn, create_user) '
                . 'VALUES '
                    . '(?, ?, ?, ?, ?)';
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute([
                $name == '' ? null : $name,
                $category_id,
                $delivery_info == '' ? null : $delivery_info,
                $turn == '' ? null : $turn,
                $create_user
            ]);
            $id = $this->getMaxId();
            $productDetailModel = new ProductDetailModel();
            for ($i = 0; $i < 5; $i++) {
                $productDetailModel->register(
                    $id,
                    $details[$i]['size'] == '' ? null : $details[$i]['size'],
                    $details[$i]['price'] == '' ? null : $details[$i]['price'],
                    $i + 1,
                    $this->dbh
                );
            }
            $this->dbh->commit();
        } catch (PDOException $e) {
            $this->dbh->rollBack();
            throw new PDOException($e);
        }
    }

    /**
     * 画像アップロード及びDBの画像情報更新
     *
     * @param int $id
     * @param array $array
     * @return void
     */
    public function uploadImg($id, $array)
    {
        try{
            $this->dbh->exec('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED');
            $this->dbh->beginTransaction();
            $sql =
                'UPDATE '
                    . 'product SET img = ?, '
                    . 'updated_at = CURRENT_TIMESTAMP() '
                . 'WHERE '
                    . 'id = ?';
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute([$array['name'], $id]);
            if ($array['error'] == UPLOAD_ERR_OK) {
                exec('sudo chmod 0777 ../' . IMG_PATH);
                if (!move_uploaded_file(
                    $array['tmp_name'],
                    '../' . IMG_PATH . mb_convert_encoding($array['name'], 'cp932', 'utf8')
                )) {
                    exec('sudo chmod 0755 ../' . IMG_PATH);
                    throw new Exception;
                }
                exec('sudo chmod 0755 ../' . IMG_PATH);
            } else {
                throw new Exception;
            }
            $this->dbh->commit();
        } catch (Exception $e) {
            $this->dbh->rollBack();
            throw new Exception($e);
        }
    }

    /**
     * idの最大値を取得
     *
     * @return id
     */
    public function getMaxId()
    {
        return $this->dbh->lastInsertId();
    }

    /**
     * idをもとに一件分の商品情報を取得
     *
     * @param int $id
     * @return array 商品情報
     */
    public function fetchSingleDetail($id)
    {
        $sql =
            'SELECT '
                . 'name, '
                . 'img '
            . 'FROM '
                . 'product '
            . 'WHERE '
                . 'id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * imgを取得
     *
     * @param int $id
     * @return array imgカラム
     */
    public function getImg($id)
    {
        $sql =
            'SELECT '
                . 'img '
            . 'FROM '
                . 'product '
            . 'WHERE '
                . 'id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_COLUMN);
    }

    /**
     * 商品情報の取得
     *
     * @param int $id
     * @return void
     */
    public function fetchSingleProduct($id)
    {
        $sql =
            'SELECT '
                . '* '
            . 'FROM '
                . 'product '
            . 'WHERE '
                . 'id = ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        $productDetailModel = new ProductDetailModel();
        $product['details'] = $productDetailModel->getDetails($id);
        return $product;
    }
}
