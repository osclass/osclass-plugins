<?php if ( !defined('ABS_PATH') ) exit('ABS_PATH is not loaded. Direct access is not allowed.');

class JobboardInstallUpdate
{
    public function __construct() {

    }

    function job_call_after_install() {
        ModelJB::newInstance()->import('jobboard/struct.sql');

        osc_set_preference('max_killer_questions', 10, 'jobboard_plugin', 'INTEGER');
        osc_set_preference('max_answers', 5, 'jobboard_plugin', 'INTEGER');
        osc_set_preference('upload_path', osc_content_path() . "uploads/", 'jobboard_plugin', 'STRING');
        osc_set_preference('version', 143, 'jobboard_plugin', 'INTEGER');
        osc_set_preference('url_pdf_convert', 'http://peas.osclass.services/topdf.php', 'jobboard_plugin', 'STRING');
    }

    function job_call_after_uninstall() {
        ModelKQ::newInstance()->uninstall();
        ModelJB::newInstance()->uninstall();

        osc_delete_preference('upload_path', 'jobboard_plugin');
        osc_delete_preference('version', 'jobboard_plugin');
        // remove killer questions preferences
        osc_delete_preference('max_killer_questions', 'jobboard_plugin');
        osc_delete_preference('max_answers', 'jobboard_plugin');

        // remove preferences added by JobboardNotices class
        $job_notice = new JobboardNotices();
        $job_notice->uninstall();
        // remove strem activity
        $job_stream = new Stream();
        $job_stream->uninstall();

        // remove page
        Page::newInstance()->deleteByInternalName('email_resumes_jobboard');
    }

    function jobboard_update_version() {
        $version = osc_get_preference('version', 'jobboard_plugin');

        if( $version < 110 ) {
            osc_set_preference('version', 110, 'jobboard_plugin', 'INTEGER');
            $conn      = DBConnectionClass::newInstance();
            $data      = $conn->getOsclassDb();
            $dbCommand = new DBCommandClass($data);

            $dbCommand->query(sprintf('ALTER TABLE %s ADD s_source VARCHAR(15) NOT NULL DEFAULT \'\' AFTER i_rating', ModelJB::newInstance()->getTable_JobsApplicants()));
            $dbCommand->query(sprintf('ALTER TABLE %s ADD s_ip VARCHAR(15) NOT NULL DEFAULT \'\' AFTER s_source', ModelJB::newInstance()->getTable_JobsApplicants()));

            osc_reset_preferences();
        }

        if( $version < 120 ) {
            osc_set_preference('version', 120, 'jobboard_plugin', 'INTEGER');
            $conn      = DBConnectionClass::newInstance();
            $data      = $conn->getOsclassDb();
            $dbCommand = new DBCommandClass($data);

            $dbCommand->query(sprintf('ALTER TABLE %s ADD COLUMN s_sex VARCHAR(15) NOT NULL DEFAULT \'prefernotsay\'  AFTER s_ip', ModelJB::newInstance()->getTable_JobsApplicants()));
            $dbCommand->query(sprintf('ALTER TABLE %s ADD COLUMN dt_birthday DATE NOT NULL DEFAULT \'0000-00-00\' AFTER s_sex', ModelJB::newInstance()->getTable_JobsApplicants()));

            osc_reset_preferences();
        }

        if( $version < 141) {
            osc_set_preference('version', 141, 'jobboard_plugin', 'INTEGER');
//            $description = array();
//            $description[osc_language()]['s_title'] = __('{WEB_TITLE} - Download all the resumes of your applicants', 'jobboard');
//            $description[osc_language()]['s_text'] = __('<p>Hi {CONTACT_NAME}!</p><p>We just finished packaging all the resumes of your applicants on {WEB_TITLE}.</p><p>Click on the links below to download the packages:</p><p>{RESUME_LIST}</p><p>Thanks</p>', 'jobboard');
//            Page::newInstance()->insert(
//                array('s_internal_name' => 'email_resumes_jobboard', 'b_indelible' => '1', 's_meta' => ''),
//                $description
//                );
            $conn      = DBConnectionClass::newInstance();
            $data      = $conn->getOsclassDb();
            $dbCommand = new DBCommandClass($data);

            $dbCommand->query(sprintf('ALTER TABLE %s ADD COLUMN i_num_positions INT UNSIGNED NOT NULL DEFAULT 1', ModelJB::newInstance()->getTable_JobsAttr()));
        }

        // add alters for killer questions
        if( $version < 142) {
            ModelKQ::newInstance()->import('jobboard/struct_killer.sql');

            osc_set_preference('version', 142, 'jobboard_plugin', 'INTEGER');
            osc_set_preference('max_killer_questions', 10, 'jobboard_plugin', 'INTEGER');
            osc_set_preference('max_answers', 5, 'jobboard_plugin', 'INTEGER');
            $conn      = DBConnectionClass::newInstance();
            $data      = $conn->getOsclassDb();
            $dbCommand = new DBCommandClass($data);

            $dbCommand->query(sprintf('ALTER TABLE %s ADD COLUMN fk_i_killer_form_id INT UNSIGNED DEFAULT NULL AFTER s_salary_text', ModelJB::newInstance()->getTable_JobsAttr()));
            $dbCommand->query(sprintf('ALTER TABLE %s ADD COLUMN fk_i_killer_form_result_id INT UNSIGNED DEFAULT NULL AFTER dt_birthday', ModelJB::newInstance()->getTable_JobsApplicants()));
            $dbCommand->query(sprintf('ALTER TABLE %s ADD COLUMN d_score DECIMAL(4,2) NULL DEFAULT NULL AFTER dt_birthday', ModelJB::newInstance()->getTable_JobsApplicants()));
            $dbCommand->query(sprintf('ALTER TABLE %s ADD COLUMN b_corrected TINYINT NOT NULL DEFAULT 0  AFTER d_score', ModelJB::newInstance()->getTable_JobsApplicants()));

            osc_reset_preferences();
        }

        // add s_name_original aplicant original file/cv
        if( $version < 143 ) {
            osc_set_preference('version', 143, 'jobboard_plugin', 'INTEGER');
            osc_set_preference('url_pdf_convert', 'http://peas.osclass.services/topdf.php', 'jobboard_plugin', 'STRING');

            $conn      = DBConnectionClass::newInstance();
            $data      = $conn->getOsclassDb();
            $dbCommand = new DBCommandClass($data);

            $dbCommand->query(sprintf('ALTER TABLE %s ADD COLUMN s_name_original VARCHAR(255) NOT NULL DEFAULT \'\'  AFTER s_name', ModelJB::newInstance()->getTable_JobsFiles()));

            osc_reset_preferences();
        }
    }
}