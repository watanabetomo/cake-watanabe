新規テーブル（在庫情報）：stock
id 在庫情報id（主キー）
product_detail_id 商品詳細id
actual_num 実在庫数
unsettled_num 注文未確定在庫数
name 商品名
size サイズ
price 価格

orderテーブル修正（カラム追加）
status 購入・確定・キャンセルの各状態を保持(1:購入 2:確定 3:キャンセル)

CREATE TABLE stock (
    id SERIAL PRIMARY KEY,
    product_detail_id BIGINT(20) UNSIGNED NOT NULL,
    actual_num SMALLINT UNSIGNED NOT NULL,
    unsettled_num SMALLINT UNSIGNED NULL DEFAULT NULL,
    name TEXT NOT NULL,
    size SMALLINT UNSIGNED NOT NULL,
    price INT UNSIGNED NOT NULL,
);

ALTER TABLE `order`
ADD status SMALLINT UNSIGNED NOT NULL;

INSERT INTO stock (product_detail_id, actual_num, name, size, price)
VALUES (1, 1000, 'チョコレートケーキ', 50, 1000);
