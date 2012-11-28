/* killer questions related */

CREATE TABLE /*TABLE_PREFIX*/t_question (
  pk_i_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  e_type ENUM('CLOSED', 'OPENED') NOT NULL,
  s_text TINYTEXT NOT NULL,

    PRIMARY KEY (pk_i_id)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';

CREATE TABLE /*TABLE_PREFIX*/t_answer (
  pk_i_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  fk_i_question_id INT UNSIGNED NULL,
  s_text TINYTEXT NOT NULL,
  s_punctuation VARCHAR(45) NULL DEFAULT NULL,
  b_reject TINYINT NOT NULL DEFAULT 0,

  PRIMARY KEY (pk_i_id),
  FOREIGN KEY (fk_i_question_id) REFERENCES /*TABLE_PREFIX*/t_question (pk_i_id)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';

CREATE TABLE /*TABLE_PREFIX*/t_killer_form (
  pk_i_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  s_title VARCHAR(255) NOT NULL DEFAULT '',
  dt_pub_date DATETIME NOT NULL,
  dt_mod_date DATETIME NULL DEFAULT NULL,

    PRIMARY KEY (pk_i_id),
    INDEX idx_pub_date (dt_pub_date),
    INDEX idx_mod_date (dt_mod_date)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';

CREATE TABLE /*TABLE_PREFIX*/t_killer_form_questions (
  fk_i_killer_form_id INT UNSIGNED NULL,
  fk_i_question_id INT UNSIGNED NULL,
  i_order INT NOT NULL,

    PRIMARY KEY (fk_i_killer_form_id, fk_i_question_id),
    FOREIGN KEY (fk_i_killer_form_id) REFERENCES /*TABLE_PREFIX*/t_killer_form (pk_i_id),
    FOREIGN KEY (fk_i_question_id) REFERENCES /*TABLE_PREFIX*/t_question (pk_i_id)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';

CREATE TABLE /*TABLE_PREFIX*/t_killer_form_results (
  fk_i_applicant_id INT UNSIGNED NULL,
  fk_i_killer_form_id INT UNSIGNED NULL,
  fk_i_question_id INT UNSIGNED NULL,
  fk_i_answer_id INT UNSIGNED NULL,
  s_answer_opened TINYTEXT DEFAULT NULL,
  s_punctuation VARCHAR(45) NULL DEFAULT NULL,

    PRIMARY KEY (fk_i_applicant_id, fk_i_killer_form_id, fk_i_question_id),
    FOREIGN KEY (fk_i_killer_form_id) REFERENCES /*TABLE_PREFIX*/t_killer_form (pk_i_id),
    FOREIGN KEY (fk_i_question_id) REFERENCES /*TABLE_PREFIX*/t_question (pk_i_id)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';
