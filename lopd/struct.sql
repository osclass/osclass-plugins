CREATE TABLE /*TABLE_PREFIX*/t_lopd (
    fk_i_user_id INT UNSIGNED NOT NULL,
    dt_date DATETIME,
    s_ip VARCHAR(100),
    b_could_delete BOOLEAN DEFAULT TRUE,
	
        PRIMARY KEY (fk_i_user_id),
        FOREIGN KEY (fk_i_user_id) REFERENCES /*TABLE_PREFIX*/t_user (pk_i_id)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';