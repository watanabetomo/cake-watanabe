CREATE TABLE product (
    id SERIAL PRIMARY KEY,
    product_category_id BIGINT(20) UNSIGNED NOT NULL,
    name TEXT,
    img TEXT,
    delivery_info TEXT,
    `order` SMALLINT UNSIGNED,
    create_user BIGINT(20) UNSIGNED NOT NULL,
    update_user BIGINT(20) UNSIGNED NULL DEFAULT NULL,
    created_at TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at TIMESTAMP(6) NULL DEFAULT NULL,
    delete_flg BOOLEAN DEFAULT FALSE
);


CREATE TABLE product_detail (
    id SERIAL PRIMARY KEY,
    product_id BIGINT(20) UNSIGNED NOT NULL,
    size SMALLINT UNSIGNED,
    price INTEGER UNSIGNED
);

CREATE TABLE product_category (
    id SERIAL PRIMARY KEY,
    name TEXT,
    `order` SMALLINT UNSIGNED
);

INSERT INTO product_detail (product_id, size, price) VALUES
(1, 90, 800),
(1, 50, 900),
(1, 70, 1000),
(2, 30, 500),
(2, 90, 800),
(2, 50, 900),
(3, 70, 1000),
(3, 30, 500),
(3, 90, 800),
(4, 50, 900),
(4, 70, 1000),
(4, 30, 500),
(4, 90, 800),
(5, 50, 900),
(5, 70, 1000),
(5, 30, 500),
(5, 20, 9000),
(6, 50, 900),
(6, 70, 1000),
(6, 30, 500),
(6, 20, 9000);

INSERT INTO product_category (name, `order`) VALUES
('Chocolate cake', 2),
('Cheese cake', 1),
('Shortcake&Tarte', 3),
('Season cake', 4);