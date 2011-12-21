CREATE TABLE /*TABLE_PREFIX*/t_facebook_connect (
    fk_i_user_id INT(10) UNSIGNED NOT NULL,
    i_facebook_uid VARCHAR(30) NULL,

        PRIMARY KEY (fk_i_user_id),
        FOREIGN KEY (fk_i_user_id) REFERENCES /*TABLE_PREFIX*/t_user (pk_i_id)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';