
CREATE TABLE user (
    id SERIAL PRIMARY KEY,
    login_id TEXT NOT NULL,
    login_pass TEXT NOT NULL,
    name TEXT NOT NULL,
    name_kana TEXT NOT NULL,
    birth_year CHAR(4),
    birth_month CHAR(2),
    birth_day CHAR(2),
    gender TINYINT NOT NULL,
    mail TEXT Not NULL,
    tel1 VARCHAR(5) NOT NULL,
    tel2 VARCHAR(5) NOT NULL,
    tel3 VARCHAR(5) NOT NULL,
    postal_code1 CHAR(3) NOT NULL,
    postal_code2 CHAR(4) NOT NULL,
    pref TINYINT NOT NULL,
    city VARCHAR(15) NOT NULL,
    address VARCHAR(100) NOT NULL,
    other VARCHAR(100),
    memo TEXT,
    status TINYINT NOT NULL,
    last_login_date TIMESTAMP(6) NULL DEFAULT NULL,
    crated_at TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at TIMESTAMP(6) NULL DEFAULT NULL,
    delete_flg BOOLEAN DEFAULT FALSE
);

CREATE TABLE cart (
    id SERIAL PRIMARY KEY,
    user_id BIGINT(20) UNSIGNED NOT NULL,
    product_detail_id BIGINT(20) UNSIGNED NOT NULL,
    num INT UNSIGNED NOT NULL
);

CREATE TABLE `order` (
    id SERIAL PRIMARY KEY,
    user_id BIGINT(20) UNSIGNED NOT NULL,
    name TEXT NOT NULL,
    name_kana TEXT NOT NULL,
    mail TEXT NOT NULL,
    tel1 VARCHAR(5) NOT NULL,
    tel2 VARCHAR(5) NOT NULL,
    tel3 VARCHAR(5) NOT NULL,
    postal_code1 CHAR(3) NOT NULL,
    postal_code2 CHAR(4) NOT NULL,
    pref TINYINT NOT NULL,
    city VARCHAR(15) NOT NULL,
    address VARCHAR(100) NOT NULL,
    other VARCHAR(100),
    payment_id BIGINT(20) UNSIGNED NOT NULL,
    sub_price INT UNSIGNED NOT NULL,
    shipping_price INT UNSIGNED NOT NULL,
    tax INT UNSIGNED NOT NULL,
    total_price INT UNSIGNED NOT NULL,
    crated_at TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at TIMESTAMP(6) NULL DEFAULT NULL,
    delete_flg BOOLEAN DEFAULT FALSE
);

CREATE TABLE order_detail(
    id SERIAL PRIMARY KEY,
    order_id BIGINT(20) UNSIGNED NOT NULL,
    product_id BIGINT(20) UNSIGNED NOT NULL,
    name TEXT NOT NULL,
    price INT UNSIGNED NOT NULL,
    num INT UNSIGNED NOT NULL
);

CREATE TABLE m_payment(
    id SERIAL PRIMARY KEY,
    name TEXT
);

INSERT INTO m_payment VALUES('各種クレジットカード決済'),('銀行振込'),('代金引換');