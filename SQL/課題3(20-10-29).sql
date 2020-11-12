
CREATE TABLE user (
    id SERIAL PRIMARY KEY,
    login_id TEXT NOT NULL,
    login_pass TEXT NOT NULL,
    name TEXT NOT NULL,
    name_kana TEXT NOT NULL,
    birth_year CHAR(4),
    birth_month CHAR(2),
    birth_day CHAR(2),
    gender SMALLINT UNSIGNED NOT NULL,
    mail TEXT NOT NULL,
    tel1 VARCHAR(5) NOT NULL,
    tel2 VARCHAR(5) NOT NULL,
    tel3 VARCHAR(5) NOT NULL,
    postal_code1 CHAR(3) NOT NULL,
    postal_code2 CHAR(4) NOT NULL,
    pref SMALLINT UNSIGNED NOT NULL,
    city VARCHAR(15) NOT NULL,
    address VARCHAR(100) NOT NULL,
    other VARCHAR(100),
    memo TEXT,
    status SMALLINT UNSIGNED NOT NULL,
    last_login_date TIMESTAMP(6) NULL DEFAULT NULL,
    created_at TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at TIMESTAMP(6) NULL DEFAULT NULL,
    delete_flg BOOLEAN DEFAULT FALSE
);

CREATE TABLE cart (
    id SERIAL PRIMARY KEY,
    user_id BIGINT(20) UNSIGNED NOT NULL,
    product_detail_id BIGINT(20) UNSIGNED NOT NULL,
    num SMALLINT UNSIGNED NOT NULL
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
    pref SMALLINT UNSIGNED NOT NULL,
    city VARCHAR(15) NOT NULL,
    address VARCHAR(100) NOT NULL,
    other VARCHAR(100),
    payment_id BIGINT(20) UNSIGNED NOT NULL,
    sub_price INT UNSIGNED NOT NULL,
    shipping_price INT UNSIGNED NOT NULL,
    tax SMALLINT UNSIGNED NOT NULL,
    total_price INT UNSIGNED NOT NULL,
    created_at TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at TIMESTAMP(6) NULL DEFAULT NULL,
    delete_flg BOOLEAN DEFAULT FALSE
);

CREATE TABLE order_detail(
    id SERIAL PRIMARY KEY,
    order_id BIGINT(20) UNSIGNED NOT NULL,
    product_detail_id BIGINT(20) UNSIGNED NOT NULL,
    name TEXT NOT NULL,
    size SMALLINT UNSIGNED NOT NULL,
    price INT UNSIGNED NOT NULL,
    num SMALLINT UNSIGNED NOT NULL
);

CREATE TABLE m_payment(
    id SERIAL PRIMARY KEY,
    name TEXT
);

INSERT INTO m_payment(name) VALUES('各種クレジットカード決済'),('銀行振込'),('代金引換');


INSERT INTO user (login_id, login_pass, name, name_kana, birth_year, birth_month, birth_day, gender, mail, tel1, tel2, tel3, postal_code1, postal_code2, pref, city, address, other, memo, status)
VALUES ('watanabe', 'tomoya1226', '渡部智哉', 'ワタナベトモヤ', '1995', '12', '26', 1, 't.watanabe@ebacorp.jp', '090', '7813', '5525', '277', '0088', 7, '柏市', 'ひばりが丘5-14', NULL, NULL, 1);
