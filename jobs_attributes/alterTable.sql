ALTER TABLE /*TABLE_PREFIX*/t_item_job_attr ADD COLUMN i_salary_min_hour INT(6) UNSIGNED NULL DEFAULT NULL  AFTER e_salary_period ,
ADD COLUMN i_salary_max_hour INT(6) UNSIGNED NULL DEFAULT NULL  AFTER i_salary_min_hour ;
