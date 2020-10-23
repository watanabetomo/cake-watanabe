/**productテーブル作成*/

CREATE TABLE product (
    id SERIAL PRIMARY KEY,
    product_category_id INT NOT NULL,
    name TEXT,
    img TEXT,
    delivery_info TEXT,
    order INT,
    created_at TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at TIMESTAMP(6) NULL DEFAULT NULL,
    delete_flg BOOLEAN DEFAULT FALSE
);


CREATE TABLE product_detail (
    id SERIAL PRIMARY KEY,
    product_id INT NOT NULL,
    size INT,
    price INT
);

CREATE TABLE product_category (
    id SERIAL PRIMARY KEY,
    name TEXT,
    order INT
);