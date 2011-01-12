CREATE TABLE /*TABLE_PREFIX*/t_social_connect (
    fk_i_user_id INT UNSIGNED NOT NULL,
    i_facebook_uid INT UNSIGNED,
    i_twitter_uid INT UNSIGNED,
	
        PRIMARY KEY (fk_i_user_id),
        FOREIGN KEY (fk_i_user_id) REFERENCES /*TABLE_PREFIX*/t_user (pk_i_id)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';
