CREATE TABLE /*TABLE_PREFIX*/t_ads4osc_ads (
    pk_i_id INT NOT NULL AUTO_INCREMENT,
    s_network VARCHAR( 50 ) NULL ,
    s_title VARCHAR( 50 ) NULL ,
    s_account_id VARCHAR( 50 ) NULL ,
    s_partner_id VARCHAR( 50 ) NULL ,
    s_slot_id VARCHAR( 50 ) NULL ,
    i_max_ads_per_page INT NULL ,
    e_ad_type ENUM(  'CUSTOM',  'ALL', 'TEXT', 'LINKS',  'IMAGES',  'VIDEO' ) NULL DEFAULT 'CUSTOM',
    i_ad_width INT NULL ,
    i_ad_height INT NULL ,
    s_ad_format VARCHAR( 50 ) NULL ,
    s_display_pages VARCHAR( 100 ) NULL ,
    s_display_categories VARCHAR( 100 ) NULL ,
    f_weight FLOAT NULL ,
    s_html_before TEXT NULL ,
    s_code TEXT NULL ,
    s_html_after TEXT NULL,
    b_active BOOL NULL DEFAULT 1,
    i_num_views INT NULL DEFAULT 0,
    
        PRIMARY KEY (pk_i_id)
) ENGINE = MYISAM DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';