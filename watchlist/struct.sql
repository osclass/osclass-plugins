CREATE TABLE /*TABLE_PREFIX*/t_item_watchlist (
    id int(10) unsigned NOT NULL AUTO_INCREMENT,
    fk_i_item_id INT(10) UNSIGNED,
    fk_i_user_id INT(10) UNSIGNED,

        PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';