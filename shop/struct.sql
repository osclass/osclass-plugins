CREATE TABLE /*TABLE_PREFIX*/t_shop_item (
    fk_i_item_id INT UNSIGNED NOT NULL,
    i_amount INT UNSIGNED NOT NULL DEFAULT 1,
    b_digital BOOLEAN NOT NULL DEFAULT FALSE,
    b_accept_paypal BOOLEAN NOT NULL DEFAULT FALSE,
    b_accept_bank_transfer BOOLEAN NOT NULL DEFAULT FALSE,

        FOREIGN KEY (fk_i_item_id) REFERENCES /*TABLE_PREFIX*/t_item (pk_i_id)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';

CREATE TABLE /*TABLE_PREFIX*/t_shop_user (
    fk_i_user_id INT UNSIGNED NOT NULL,
    f_score FLOAT NULL DEFAULT 0,
    i_total_sales INT UNSIGNED NOT NULL DEFAULT 0,
    i_total_buys INT UNSIGNED NOT NULL DEFAULT 0,

        FOREIGN KEY (fk_i_user_id) REFERENCES /*TABLE_PREFIX*/t_user (pk_i_id)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';

CREATE TABLE /*TABLE_PREFIX*/t_shop_transactions (
    pk_i_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    fk_i_item_id INT UNSIGNED NOT NULL,
    fk_i_user_id INT UNSIGNED NOT NULL,
    fk_i_buyer_id INT UNSIGNED NOT NULL,
    i_amount INT UNSIGNED NULL,
    f_item_price FLOAT NULL DEFAULT 0,
    s_currency VARCHAR(3) NULL,
    e_status ENUM('SOLD','PAID', 'SHIPPED', 'VOTE_BUYER', 'VOTE_SELLER', 'ENDED'),

        PRIMARY KEY(pk_i_id)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';

CREATE TABLE /*TABLE_PREFIX*/t_shop_log (
    fk_i_transaction_id INT UNSIGNED NOT NULL,
    e_status ENUM('SOLD','PAID', 'SHIPPED', 'VOTE_BUYER', 'VOTE_SELLER', 'ENDED'),
    fk_i_user_id INT UNSIGNED NOT NULL,
    dt_date DATETIME NOT NULL,

        FOREIGN KEY (fk_i_transaction_id) REFERENCES /*TABLE_PREFIX*/t_shop_transactions (pk_i_id)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';

CREATE TABLE  /*TABLE_PREFIX*/t_shop_paypal_log (
    pk_i_id INT NOT NULL AUTO_INCREMENT ,
    s_concept VARCHAR( 200 ) NOT NULL ,
    dt_date DATETIME NOT NULL ,
    s_code VARCHAR( 17 ) NOT NULL ,
    f_amount FLOAT NOT NULL ,
    s_currency_code VARCHAR( 3 ) NULL ,
    s_email VARCHAR( 200 ) NULL ,
    fk_i_transaction_id INT NULL ,

    PRIMARY KEY(pk_i_id)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';