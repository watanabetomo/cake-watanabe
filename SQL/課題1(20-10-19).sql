/**DB作成（DB名：t_watanabe）、DB選択*/

CREATE DATABASE IF NOT EXISTS `t_watanabe` DEFAULT CHARACTER SET utf8;
USE `t_watanabe`;

/**
～テーブル作成指示～
テーブル名：admin_user（管理者ユーザ）
id（主キー）　　　　　　　　　　　数字　　　　　　　　　　 自動連番
login_id（ログインID）　　　　　　テキスト　　　　　　　　 未入力を認めない
login_pass（ログインパスワード）　テキスト　　　　　　　　 未入力を認めない
name（ユーザ名）　　　　　　　　　テキスト　　　　　　　　 未入力を認めない
created_at（作成日時）　　　　　　日時（6桁のミリ秒まで）　初期値は現在時刻
updated_at（更新日時）　　　　　　日時（6桁のミリ秒まで）　初期値はnull
delete_flg（削除フラグ）　　　　　真偽　　　　　　　　　　 初期値は偽（有効なユーザ）
*/

/**テーブル作成（テーブル名：admin_user）*/

CREATE TABLE admin_user (
  id SERIAL PRIMARY KEY,
  login_id text NOT NULL,
  login_pass text NOT NULL,
  name text NOT NULL,
  created_at timestamp(6) NOT NULL DEFAULT current_timestamp(6),
  updated_at timestamp(6) NULL DEFAULT NULL,
  delete_flg boolean DEFAULT false
);


/**データ挿入*/

INSERT INTO `admin_user` (`login_id`, `login_pass`, `name`) VALUES
('ebaeba', '$2y$10$LoC8l6t.WSjF2ToEMZpXR.NC5i2s/tEA0xprNnX32GVX4WWtYaXP2', '江畑'),
('watanabe', '$2y$10$HvQoU9KOKLcLjWMIwNCrWu8AXFAB50mmsCiOKsdPv7JHuZoftewEC', '渡部智哉'),
('aaaa', '$2y$10$cenovMqSGExqIWfQyDndEuT4mm6W1xu3tpDvR6gApMC0siPShuuF.', 'cccc'),
('dddd', '$2y$10$Ko5BmoN.0UrNMlbnchKWIOYQMS/i1rElNpF289T7w6NbI/iPFVeze', 'ffff');

INSERT INTO `admin_user` (`login_id`, `login_pass`, `name`) VALUES
('ebaeba', '$2y$10$LoC8l6t.WSjF2ToEMZpXR.NC5i2s/tEA0xprNnX32GVX4WWtYaXP2', N'江畑'),
('watanabe', '$2y$10$HvQoU9KOKLcLjWMIwNCrWu8AXFAB50mmsCiOKsdPv7JHuZoftewEC', N'渡部智哉');