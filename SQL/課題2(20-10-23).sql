CREATE TABLE product (
    id SERIAL PRIMARY KEY,
    product_category_id BIGINT(20) UNSIGNED NOT NULL,
    name TEXT,
    img TEXT,
    delivery_info TEXT,
    turn SMALLINT UNSIGNED,
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
    price INTEGER UNSIGNED,
    turn SMALLINT UNSIGNED
);

CREATE TABLE product_category (
    id SERIAL PRIMARY KEY,
    name TEXT,
    turn SMALLINT UNSIGNED
);

INSERT INTO product_category (name, turn) VALUES
('Chocolate cake', 2),
('Cheese cake', 1),
('Shortcake&Tarte', 3),
('Season cake', 4);