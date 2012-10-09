<?php
/*
Plugin Name: Job Board
Plugin URI: http://www.osclass.org/
Description: Job Board
Version: 1.2
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: jobboard_plugin
Plugin update URI: job-board
*/

define('JOBBOARD_PATH', dirname(__FILE__) . '/') ;
require_once(JOBBOARD_PATH . 'ModelJB.php');
require_once(JOBBOARD_PATH . 'helpers.php');

function job_call_after_install() {
    ModelJB::newInstance()->import('jobboard/struct.sql');

    osc_set_preference('upload_path', osc_content_path() . "uploads/", 'jobboard_plugin', 'STRING');
    osc_set_preference('version', 120, 'jobboard_plugin', 'INTEGER');
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
}
osc_add_hook('init', 'jobboard_update_version');

function _jobboard_time_elapsed_string($ptime) {
    $etime = time() - $ptime;

    if ($etime < 1) {
        return '0 '.__('seconds', 'jobboard');
    }

    $a = array( 12 * 30 * 24 * 60 * 60  =>  __('year', 'jobboard'),
                30 * 24 * 60 * 60       =>  __('month', 'jobboard'),
                24 * 60 * 60            =>  __('day', 'jobboard'),
                60 * 60                 =>  __('hour', 'jobboard'),
                60                      =>  __('minute', 'jobboard'),
                1                       =>  __('second', 'jobboard')
                );

    foreach ($a as $secs => $str) {
        $d = $etime / $secs;
        if ($d >= 1) {
            $r = round($d);
            return $r . ' ' . $str . ($r > 1 ? 's' : '');
        }
    }
}
function _jobboard_get_age($birthday){

    if($birthday!='' && $birthday!='0000-00-00') {
        list($year,$month,$day) = explode("-",$birthday);
        $year_diff  = date("Y") - $year;
        $month_diff = date("m") - $month;
        $day_diff   = date("d") - $day;
        if ($day_diff < 0 || $month_diff < 0)
          $year_diff--;
        return $year_diff;
    } else {
        return '-';
    }
}

function _jobboard_get_sex_array() {
    // array sex
    $aSex = array(
        'male'         => __('Male', 'jobboard'),
        'female'       => __('Female', 'jobboard'),
        'prefernotsay' => __('Prefer not say', 'jobboard')
    );
    return $aSex;
}

function jobboard_sex_to_string($sex) {
    // array sex
    $aSex = _jobboard_get_sex_array();
    return $aSex[$sex];
}
osc_add_hook('admin_header', 'jobboard_extra');

function job_call_after_uninstall() {
    ModelJB::newInstance()->uninstall();

    osc_delete_preference('upload_path', 'jobboard_plugin');
    osc_delete_preference('version', 'jobboard_plugin');
}

/* AJAX */
function ajax_rating_request() {
    ModelJB::newInstance()->setRating(Params::getParam("applicantId"), Params::getParam("rating"));
}
osc_add_hook('ajax_admin_jobboard_rating', 'ajax_rating_request');

function ajax_applicant_status() {
    ModelJB::newInstance()->changeStatus(Params::getParam("applicantId"), Params::getParam("status"));
}
osc_add_hook('ajax_admin_applicant_status', 'ajax_applicant_status');

function ajax_applicant_status_notification() {
    $applicantID = Params::getParam('applicantId');
    $status      = Params::getParam('status');

    require_once(JOBBOARD_PATH . 'email.php');
    send_email_notification_applicant($status, $applicantID);
}
osc_add_hook('ajax_admin_applicant_status_notifitacion', 'ajax_applicant_status_notification');

function ajax_note_add() {
    $noteID = ModelJB::newInstance()->insertNote(Params::getParam('applicantID'), Params::getParam('noteText'));
    $aNote = ModelJB::newInstance()->getNoteByID($noteID);
    $aNote['day']   = date('d', strtotime($aNote['dt_date']));
    $aNote['month'] = date('M', strtotime($aNote['dt_date']));
    $aNote['year']  = date('Y', strtotime($aNote['dt_date']));
    echo json_encode($aNote);
}
osc_add_hook('ajax_admin_note_add', 'ajax_note_add');

function ajax_note_edit() {
    ModelJB::newInstance()->updateNote(Params::getParam('noteID'), Params::getParam('noteText'));
    $aNote = ModelJB::newInstance()->getNoteByID(Params::getParam('noteID'));
    $aNote['day']   = date('d', strtotime($aNote['dt_date']));
    $aNote['month'] = date('M', strtotime($aNote['dt_date']));
    $aNote['year']  = date('Y', strtotime($aNote['dt_date']));
    echo json_encode($aNote);
}
osc_add_hook('ajax_admin_note_edit', 'ajax_note_edit');

function ajax_note_delete() {
    ModelJB::newInstance()->deleteNote(Params::getParam('noteID'));
}
osc_add_hook('ajax_admin_note_delete', 'ajax_note_delete');
/* /AJAX */

/* FORM JOB BOARD */
function jobboard_form($catID = null) {
    $detail = array(
        'e_position_type' => '',
        's_salary_text'   => '',
        'locale'          => array()
    );
    foreach(osc_get_locales() as $locale) {
        $detail['locale'][$locale['pk_c_code']] = array(
            's_desired_exp'                 => '',
            's_studies'                     => '',
            's_minimum_requirements'        => '',
            's_desired_requirements'        => '',
            's_contract'                    => ''
        );
    }
    // session variables
    $detail = get_jobboard_session_variables($detail);

    require_once(JOBBOARD_PATH . 'item_edit.php');
    Session::newInstance()->_clearVariables();
}
osc_add_hook('item_form', 'jobboard_form');

function jobboard_form_post($catID = null, $itemID = null)  {
    ModelJB::newInstance()->insertJobsAttr($itemID, Params::getParam('relation'), Params::getParam('positionType'), Params::getParam('salaryText') );

    // prepare locales
    $dataItem = array();
    foreach(Params::getParam('min_reqs') as $k => $v) {
        $dataItem[$k]['min_reqs'] = $v;
    }
    foreach(Params::getParam('desired_reqs') as $k => $v) {
        $dataItem[$k]['desired_reqs'] = $v;
    }
    foreach(Params::getParam('desired_exp') as $k => $v) {
        $dataItem[$k]['desired_exp'] = $v;
    }
    foreach(Params::getParam('studies') as $k => $v) {
        $dataItem[$k]['studies'] = $v;
    }
    foreach(Params::getParam('contract') as $k => $v) {
        $dataItem[$k]['contract'] = $v;
    }

    // insert locales
    foreach ($dataItem as $k => $data) {
        ModelJB::newInstance()->insertJobsAttrDescription($itemID, $k, $data['desired_exp'], $data['studies'], $data['min_reqs'], $data['desired_reqs'], $data['contract']);
    }
}
osc_add_hook('item_form_post', 'jobboard_form_post');

function jobboard_item_edit($catID = null, $itemID = null) {
    $detail       = ModelJB::newInstance()->getJobsAttrByItemId($itemID);
    $descriptions = ModelJB::newInstance()->getJobsAttrDescriptionsByItemId($itemID);
    $detail['locale'] = array();
    foreach ($descriptions as $desc) {
        $detail['locale'][$desc['fk_c_locale_code']] = $desc;
    }

    // session variables
    $detail = get_jobboard_session_variables($detail);

    require_once(JOBBOARD_PATH . 'item_edit.php');
    Session::newInstance()->_clearVariables();
}
osc_add_hook('item_edit', 'jobboard_item_edit');

function jobboard_item_edit_post($catID = null, $itemID = null) {
    ModelJB::newInstance()->replaceJobsAttr($itemID, Params::getParam('relation'), Params::getParam('positionType'), Params::getParam('salaryText'));

    // prepare locales
    $dataItem = array();
    foreach(Params::getParam('min_reqs') as $k => $v) {
        $dataItem[$k]['min_reqs'] = $v;
    }
    foreach(Params::getParam('desired_reqs') as $k => $v) {
        $dataItem[$k]['desired_reqs'] = $v;
    }
    foreach(Params::getParam('desired_exp') as $k => $v) {
        $dataItem[$k]['desired_exp'] = $v;
    }
    foreach(Params::getParam('studies') as $k => $v) {
        $dataItem[$k]['studies'] = $v;
    }
    foreach(Params::getParam('contract') as $k => $v) {
        $dataItem[$k]['contract'] = $v;
    }

    // insert locales
    foreach ($dataItem as $k => $data) {
        ModelJB::newInstance()->replaceJobsAttrDescriptions($itemID, $k, $data['desired_exp'], $data['studies'], $data['min_reqs'], $data['desired_reqs'], $data['contract']);
    }
}
osc_add_hook('item_edit_post', 'jobboard_item_edit_post');

function get_jobboard_session_variables($detail) {
    if( Session::newInstance()->_getForm('pj_positionType') != '' ) {
        $detail['e_position_type'] = Session::newInstance()->_getForm('pj_positionType');
    }
    if( Session::newInstance()->_getForm('pj_salaryText') != '' ) {
        $detail['s_salary_text'] = Session::newInstance()->_getForm('pj_salaryText');
    }
    if( Session::newInstance()->_getForm('pj_data') != '' ) {
        foreach(osc_get_locales() as $locale) {
            $data = Session::newInstance()->_getForm('pj_data');
            $detail['locale'][$locale['pk_c_code']]['s_desired_exp']          = @$data[$locale['pk_c_code']]['desired_exp'];
            $detail['locale'][$locale['pk_c_code']]['s_studies']              = @$data[$locale['pk_c_code']]['studies'];
            $detail['locale'][$locale['pk_c_code']]['s_minimum_requirements'] = @$data[$locale['pk_c_code']]['min_reqs'];
            $detail['locale'][$locale['pk_c_code']]['s_desired_requirements'] = @$data[$locale['pk_c_code']]['desired_reqs'];
            $detail['locale'][$locale['pk_c_code']]['s_contract']             = @$data[$locale['pk_c_code']]['contract'];
        }
    }

    return $detail;
}
/* /FORM JOB BOARD */

function get_jobboard_position_types() {
    $position_types = array(
        'UNDEF' => __('Undefined', 'jobboard'),
        'PART'  => __('Part time', 'jobboard'),
        'FULL'  => __('Full time', 'jobboard')
    );
    return $position_types;
}

function job_item_detail() {
    $detail = ModelJB::newInstance()->getJobsAttrByItemId(osc_item_id());
    $descriptions = ModelJB::newInstance()->getJobsAttrDescriptionsByItemId(osc_item_id());
    $detail['locale'] = array();
    foreach ($descriptions as $desc) {
        $detail['locale'][$desc['fk_c_locale_code']] = $desc;
    }
    require_once(JOBBOARD_PATH . 'item_detail.php');
}

function job_linkedin() {
    require_once(JOBBOARD_PATH . 'linkedinApply.php');
}

/* CONTACT */
function jobboard_add_extra_fields() {
    // add age field [m/d/y]
    // add sex field [Male/Femele/Undef]
    require_once(JOBBOARD_PATH . 'extra_contact_form.php');
}
osc_add_hook('item_contact_form', 'jobboard_add_extra_fields');
osc_add_hook('contact_form', 'jobboard_add_extra_fields');
function jobboard_save_contact_listing() {
    jobboard_common_contact(osc_item_id(), osc_item_url());
    require_once(JOBBOARD_PATH . 'email.php');
    send_email_to_applicant('listing');
    send_notifaction_applicant_to_admin('listing');
}

osc_add_hook('post_item_contact_post', 'jobboard_save_contact_listing', 1);
osc_remove_hook('hook_email_item_inquiry', 'fn_email_item_inquiry');

function jobboard_save_contact($params) {
    jobboard_common_contact(null, osc_contact_url(), @$params['attachment']);
    require_once(JOBBOARD_PATH . 'email.php');
    send_email_to_applicant('spontaneous');
    send_notifaction_applicant_to_admin('spontaneous');
    osc_add_flash_ok_message(__('Thanks for sending us your CV', 'jobboard'));
    header('Location: ' . osc_contact_url()); exit;
}
osc_add_hook('pre_contact_post', 'jobboard_save_contact');

function jobboard_common_contact($itemID, $url, $uploadCV = '') {
    $error_attachment = false;

    $name   = Params::getParam('yourName');
    $email  = Params::getParam('yourEmail');

    $birth  = Params::getParam('birthday');
    $sex    = Params::getParam('sex');

    $cover  = Params::getParam('message');
    $phone  = Params::getParam('phoneNumber');
    $aCV    = Params::getFiles('attachment');
    // GET EXTRA PARMAS
    // check fields
    if( $name === '' ) {
        osc_add_flash_error_message(__("Name is required", 'jobboard'));
        _save_jobboard_contact_listing();
        header('Location: ' . $url); die;
    }
    if( $email === '' ) {
        osc_add_flash_error_message(__("Email is required", 'jobboard'));
        _save_jobboard_contact_listing();
        header('Location: ' . $url); die;
    }
    if( $birth === '' ) {
        osc_add_flash_error_message(__("Birthday is required", 'jobboard'));
        _save_jobboard_contact_listing();
        header('Location: ' . $url); die;
    } else {
        // check date format & convert date to mysql date format
        // we recive mm/dd/yyyy id valid ?
        $aDate = explode('/', $birth);
        $birth = date("Y-m-d", mktime(0,0,0,$aDate[0],$aDate[1],$aDate[2]) );
        if($birth === false) {
            osc_add_flash_error_message(__("Invalid birthday date", 'jobboard'));
            _save_jobboard_contact_listing();
            header('Location: ' . $url); die;
        }
    }

    if( $sex === '' ) {
        osc_add_flash_error_message(__("Sex is required", 'jobboard'));
        _save_jobboard_contact_listing();
        header('Location: ' . $url); die;
    }
    if( $cover === '' ) {
        osc_add_flash_error_message(__("Cover is required", 'jobboard'));
        _save_jobboard_contact_listing();
        header('Location: ' . $url); die;
    }
    if( isset($aCV['name']) && $aCV['name'] === '' ) {
        osc_add_flash_error_message(__("CV is required", 'jobboard'));
        _save_jobboard_contact_listing();
        header('Location: ' . $url); die;
    }

    // check: apply only once for each job offer
    $numberApplys = ModelJB::newInstance()->countApply($itemID, $email);

    if( $numberApplys > 0 ) {
        osc_add_flash_error_message(__("You can only apply once a job offer", 'jobboard'));
        _save_jobboard_contact_listing();
        header('Location: ' . $url); die;
    }

    require osc_lib_path() . 'osclass/mimes.php';
    // get allowedExt
    $aMimesAllowed = array();
    $aExt = array('pdf', 'rtf', 'doc', 'docx', 'odt');
    foreach($aExt as $ext){
        if(isset($mimes[$ext])) {
            $mime = $mimes[$ext];
            if( is_array($mime) ){
                foreach($mime as $aux){
                    if( !in_array($aux, $aMimesAllowed) ) {
                        array_push($aMimesAllowed, $aux);
                    }
                }
            } else {
                if( !in_array($mime, $aMimesAllowed) ) {
                    array_push($aMimesAllowed, $mime);
                }
            }
        }
    }

    if( $aCV['error'] == UPLOAD_ERR_OK ) {
        if( !in_array($aCV['type'], $aMimesAllowed) ) {
            osc_add_flash_error_message(__("The file you tried to upload does not have a valid extension", 'jobboard'));
            _save_jobboard_contact_listing();
            header('Location: ' . $url); die;
        }
    }

    // insert to database
    $mJB = ModelJB::newInstance();

    $applicantID = $mJB->insertApplicant($itemID, $name, $email, $cover, $phone, $birth, $sex);
    View::newInstance()->_exportVariableToView('applicantID', $applicantID);
    // return to listing url
    if( !$applicantID ) {
        osc_add_flash_error_message(__("There were some problem processing your application, please try again", 'jobboard'));
        header('Location: ' . $url); die;
    }

    if($uploadCV=='') {
        if(isset($aCV['name']) && $aCV['error'] == UPLOAD_ERR_OK) {
            $tmp_name = $aCV['tmp_name'];
            $fileName = date('YmdHis') . '_' . $aCV['name'];
            if( move_uploaded_file($tmp_name, osc_get_preference('upload_path', 'jobboard_plugin') . $fileName) ) {
                $mJB->insertFile($applicantID, $fileName);
            } else {
                $error_attachment = true;
            }
        } else {
            $error_attachment = true;
        }
    } else {
        $fileName = date('YmdHis') . '_' . $aCV['name'];
        if( copy($uploadCV, osc_get_preference('upload_path', 'jobboard_plugin') . $fileName) ) {
            @unlink($uploadCV);
            $mJB->insertFile($applicantID, $fileName);
        } else {
            $error_attachment = true;
        }
    }

    if( $error_attachment ) {
        ModelJB::newInstance()->deleteApplicant($applicantID);
        osc_add_flash_error_message(__("There were some problem processing your application, please try again", 'jobboard'));
        header('Location: ' . $url); die;
    }

    return true;
}

function _save_jobboard_contact_listing() {
    Session::newInstance()->_setForm('yourEmail',    Params::getParam('yourEmail'));
    Session::newInstance()->_setForm('yourName',     Params::getParam('yourName'));
    Session::newInstance()->_setForm('phoneNumber',  Params::getParam('phoneNumber'));
    Session::newInstance()->_setForm('message_body', Params::getParam('message'));
    // v 1.2
    Session::newInstance()->_setForm('birthday',     Params::getParam('birthday'));
    Session::newInstance()->_setForm('sex',          Params::getParam('sex'));
}
/* /CONTACT */

function job_delete_locale($locale) {
    ModelJB::newInstance()->deleteLocale($locale);
}

function job_delete_item($item_id) {
    ModelJB::newInstance()->deleteItem($item_id);
}

function jobboard_admin_menu() {
    if(Params::getParam('page')=='items' && Params::getParam('action')=='') { ?>
        <script type="text/javascript">
            $(document).ready(function(){
                $("#bulk_actions option").each(function(){
                    switch(this.value) {
                        case 'activate_all':
                        case 'deactivate_all':
                        case 'premium_all':
                        case 'depremium_all':
                        case 'spam_all':
                        case 'despam_all':
                            $(this).remove();
                            break;
                    }
                });
            });
        </script>
    <?php }
}
osc_add_hook('admin_header','jobboard_admin_menu');

function jobboard_duplicate_job() {
    if(Params::getParam('page')=='items' && Params::getParam('action')=='post') {
        $id = Params::getParam('duplicatefrom') ;
        if($id!='') {
            $item = Item::newInstance()->findByPrimaryKey($id);

            View::newInstance()->_exportVariableToView("item", $item);
            View::newInstance()->_exportVariableToView("new_item", TRUE);
            View::newInstance()->_exportVariableToView("actions", array());

            $detail       = ModelJB::newInstance()->getJobsAttrByItemId($id);
            $descriptions = ModelJB::newInstance()->getJobsAttrDescriptionsByItemId($id);

            Session::newInstance()->_setForm('pj_positionType',  @$detail['e_position_type'] );
            Session::newInstance()->_setForm('pj_salaryText', @$detail['s_salary_text'] );

            $dataItem = array();
            foreach ($descriptions as $v) {
                $dataItem[$v['fk_c_locale_code']] = array();
                $dataItem[$v['fk_c_locale_code']]['contract'] = $v['s_contract'];
                $dataItem[$v['fk_c_locale_code']]['studies'] = $v['s_studies'];
                $dataItem[$v['fk_c_locale_code']]['desired_exp'] = $v['s_desired_exp'];
                $dataItem[$v['fk_c_locale_code']]['min_reqs'] = $v['s_minimum_requirements'];
                $dataItem[$v['fk_c_locale_code']]['desired_reqs'] = $v['s_desired_requirements'];
            }
            Session::newInstance()->_setForm('pj_data', $dataItem );

            Session::newInstance()->_keepForm('pj_positionType');
            Session::newInstance()->_keepForm('pj_salaryText');
            Session::newInstance()->_keepForm('pj_data');

            osc_current_admin_theme_path('items/frm.php') ;
            Session::newInstance()->_clearVariables();
            osc_run_hook('after_admin_html');
            exit;
        }
    };
}
osc_add_hook('before_admin_html', 'jobboard_duplicate_job');

function jobboard_more_options($options, $aRow) {
    return array();
}
osc_add_filter('more_actions_manage_items', 'jobboard_more_options');

function jobboard_manage_actions($options, $aRow) {
    if($aRow['b_enabled']) {
        $options[] = '<a href="' . osc_admin_base_url(true) . '?page=items&amp;action=status&amp;id=' . $aRow['pk_i_id'] . '&amp;value=DISABLE">' . __('Block', 'jobboard') .'</a>' ;
    } else {
        $options[] = '<a href="' . osc_admin_base_url(true) . '?page=items&amp;action=status&amp;id=' . $aRow['pk_i_id'] . '&amp;value=ENABLE">' . __('Unblock', 'jobboard') .'</a>' ;
    }
    $options[] = '<a href="' . osc_admin_base_url(true) . '?page=items&amp;action=post&amp;duplicatefrom=' . $aRow['pk_i_id'] . '">' . __('Duplicate', 'jobboard') . '</a>' ;
    return $options;
}
osc_add_filter('actions_manage_items', 'jobboard_manage_actions');

//Custom title
osc_add_filter('custom_plugin_title','jobboard_people_title');
function jobboard_people_title($string){
    if(Params::getParam('page') == 'plugins' && Params::getParam('file') == 'jobboard/people_detail.php'){
        $string = __('Applicant', 'jobboard') . '<a href="#" class="btn ico ico-32 ico-help float-right"></a>';
    }
    return $string;
}

osc_add_admin_menu_page(
    __('Jobboard', 'jobboard'),
    '#',
    'jobboard',
    'moderator'
);

osc_add_admin_submenu_page(
    'jobboard',
    __('Dashboard', 'jobboard'),
    osc_admin_render_plugin_url("jobboard/dashboard.php"),
    'jobboard_dash',
    'moderator'
);

osc_add_admin_submenu_page(
    'jobboard',
    __('Applicants', 'jobboard'),
    osc_admin_render_plugin_url("jobboard/people.php"),
    'jobboard_people',
    'moderator'
);

function jobboard_rating($applicantId, $rating = 0) {
    $str = '<span class="rating" id="rating_'.$applicantId.'" rating="'.$rating.'">';
    for($k=1;$k<=5;$k++) {
        $str .= '<a href="#" class="star" star="'.$k.'" id="rating_'.$applicantId.'_'.$k.'" ><img src="'.osc_base_url().'oc-content/plugins/'.osc_plugin_folder(__FILE__).'img/'.($k<=$rating?'fullstar.png':'emptystar.png').'"/></a>';
    }
    $str .= '</span>';
    return $str;
}

function jobboard_status() {
    $status_array = array();
    $status_array[0] = __("Active", "jobboard");
    $status_array[1] = __("Interview", "jobboard");
    $status_array[2] = __("Rejected", "jobboard");
    $status_array[3] = __("Hired", "jobboard");
    return $status_array;
}

//Title, Location, Created, Modified, Number of Candidates, Views
function job_items_table_header($table) {
    $table->addColumn("mod_date", __("Modified", "jobboard"));
    $table->addColumn("applicants", __("# of applicants", "jobboard"));
    $table->addColumn("views", __("Views", "jobboard"));
    $table->removeColumn("user");
    $table->removeColumn("category");
}

function job_items_row($row, $aRow) {
    list($applicants, $total) = ModelJB::newInstance()->searchCount(array('item' => $aRow['pk_i_id']));

    $row['mod_date'] = @$aRow['dt_mod_date'];
    $row['applicants'] = '<a href="' . osc_admin_render_plugin_url("jobboard/people.php&jobId=") . $aRow['pk_i_id'] . '">' . sprintf(__('%d applicants', 'jobboard'), $applicants) . '</a>';
    $views = 0;
    if( $aRow['i_num_views'] > 0 ) {
        $views = $aRow['i_num_views'];
    }
    $row['views'] = @$aRow['i_num_views'];
    return $row;
}

function _applicants_shortcuts() {
    $shortcuts = array();
    $shortcuts['unread'] = array();
    $totalApplicantsShortcut = ModelJB::newInstance()->countApplicantsUnread();
    $shortcuts['unread']['total'] = $totalApplicantsShortcut;
    $shortcuts['unread']['url'] = osc_admin_render_plugin_url('jobboard/people.php') . '&viewUnread=1';
    $shortcuts['unread']['active'] = false;
    if( Params::getParam('viewUnread') ) {
        $shortcuts['unread']['active'] = true;
    }
    $shortcuts['unread']['text'] = sprintf(__('Unread (%1$s)'), $totalApplicantsShortcut);
    $shortcuts['active'] = array();
    $totalApplicantsShortcut = ModelJB::newInstance()->countApplicantsByStatus('0');
    $shortcuts['active']['total'] = $totalApplicantsShortcut;
    $shortcuts['active']['url'] = osc_admin_render_plugin_url('jobboard/people.php') . '&statusId=0';
    $shortcuts['active']['active'] = false;
    if( Params::getParam('statusId') == '0' && !Params::getParam('viewUnread') ) {
        $shortcuts['active']['active'] = true;
    }
    $shortcuts['active']['text'] = sprintf(__('Active (%1$s)'), $totalApplicantsShortcut);
    $shortcuts['interview'] = array();
    $totalApplicantsShortcut = ModelJB::newInstance()->countApplicantsByStatus('1');
    $shortcuts['interview']['total'] = $totalApplicantsShortcut;
    $shortcuts['interview']['url'] = osc_admin_render_plugin_url('jobboard/people.php') . '&statusId=1';
    $shortcuts['interview']['active'] = false;
    if( Params::getParam('statusId') == '1' ) {
        $shortcuts['interview']['active'] = true;
    }
    $shortcuts['interview']['text'] = sprintf(__('Interview (%1$s)'), $totalApplicantsShortcut);
    $shortcuts['rejected'] = array();
    $totalApplicantsShortcut = ModelJB::newInstance()->countApplicantsByStatus('2');
    $shortcuts['rejected']['total'] = $totalApplicantsShortcut;
    $shortcuts['rejected']['url'] = osc_admin_render_plugin_url('jobboard/people.php') . '&statusId=2';
    $shortcuts['rejected']['active'] = false;
    if( Params::getParam('statusId') == '2' ) {
        $shortcuts['rejected']['active'] = true;
    }
    $shortcuts['rejected']['text'] = sprintf(__('Rejected (%1$s)'), $totalApplicantsShortcut);
    $shortcuts['hired'] = array();
    $totalApplicantsShortcut = ModelJB::newInstance()->countApplicantsByStatus('3');
    $shortcuts['hired']['total'] = $totalApplicantsShortcut;
    $shortcuts['hired']['url'] = osc_admin_render_plugin_url('jobboard/people.php') . '&statusId=3';
    $shortcuts['hired']['active'] = false;
    if( Params::getParam('statusId') == '3' ) {
        $shortcuts['hired']['active'] = true;
    }
    $shortcuts['hired']['text'] = sprintf(__('Hired (%1$s)'), $totalApplicantsShortcut);
    return $shortcuts;
}

/**
* Redirect to function via JS
*
* @param string $url
*/
function job_js_redirect_to($url) { ?>
    <script type="text/javascript">
        window.location = "<?php echo $url; ?>"
    </script>
<?php }

function job_pre_item_post() {
    Session::newInstance()->_setForm('pj_positionType',  Params::getParam('positionType') );
    Session::newInstance()->_setForm('pj_salaryText', Params::getParam('salaryText') );
    // prepare locales
    $dataItem = array();
    $request = Params::getParamsAsArray();
    foreach ($request as $k => $v) {
        if(is_array($v)) {
            foreach($v as $locale => $value) {
                $dataItem[$locale][$k] = $value;
            }
        }
    }
    Session::newInstance()->_setForm('pj_data', $dataItem );

    // keep values on session
    Session::newInstance()->_keepForm('pj_positionType');
    Session::newInstance()->_keepForm('pj_salaryText');
    Session::newInstance()->_keepForm('pj_data');
}

function job_save_inputs_into_session() {
    Session::newInstance()->_keepForm('pj_positionType');
    Session::newInstance()->_keepForm('pj_salaryText');
    Session::newInstance()->_keepForm('pj_data');
}
osc_add_hook('pre_item_post', 'job_pre_item_post') ;
osc_add_hook('save_input_session', 'job_save_inputs_into_session' );

// this is needed in order to be able to activate the plugin
osc_register_plugin(osc_plugin_path(__FILE__), 'job_call_after_install');
// this is a hack to show a Uninstall link at plugins table (you could also use some other hook to show a custom option panel)
osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", 'job_call_after_uninstall');

// show an item special attributes
osc_add_hook('item_detail', 'job_item_detail');
osc_add_hook('item_detail', 'job_linkedin');

// delete locale
osc_add_hook('delete_locale', 'job_delete_locale');
// delete item
osc_add_hook('delete_item', 'job_delete_item');


/* Help sections */
function help_list_vacancies() {
    echo '<p>' . __("Manage all the vacancies published on your website. You can edit, delete, block or duplicate the vacancies already published or filter them by: e-mail, category, region, city etc.", 'jobboard') . '</p>';
}
function help_add_vacancy() {
    echo '<p>' . __("Add new vacancy to your job board: enter a title, select a category, country, region and provide a short description.", 'jobboard') . '</p>';
}
function help_list_applicants() {
    echo '<p>' . __("Here you can manage all the applicants that are interested in your vacancies. You can filter them to see only those who have applied for one job offer, view the list by ratings or search applicants by name or email. By clicking on a name of the applicant you can view more information about her/his profile.", 'jobboard') . '</p>';
}
function help_detail_applicant() {
    echo '<p>' . __("Here you can view a profile of the applicant, view or download his/her CV, add notes, rate profile and change applicant’s status (active, interviewed, hired or rejected).", 'jobboard') . '</p>';
}
function help_jobboard_pages() {
    echo '<p>' . __('Here you can create, edit, view or delete static pages on which information can be stored, such as "Corporate" or "Legal" pages.', 'jobboard') . '</p>';
}
function help_add_jobboard_page() {
    echo '<p>' . __("Modify the emails your site's users receive when they join your site, when someone shows interest in their ad, to recover their password... <strong>Be careful</strong>: don't modify any of the words that appear within brackets.") . '</p>';
}
function help_appearance_jobboard() {
    echo '<p>' . __("Personalise your job board, upload your logo, change a background colour, customize fonts, etc.", 'jobboard') . '</p>';
}
function help_settings_jobboard() {
    echo '<p>' . __("Manage your settings, modify e-mails, titles, admin users, passwords or allow spontaneous applications etc. You can also add a tracking code for Google Analytics here.", 'jobboard') . '</p>';
}

function help_jobboard_init() {
    $page   = Params::getParam('page');
    $action = Params::getParam('action');
    switch($page) {
        case('items'):
            switch($action) {
                case('item_edit'):
                case('post'):
                    osc_add_hook('help_box', 'help_add_vacancy', 9);
                break;
                case(''):
                    osc_add_hook('help_box', 'help_list_vacancies', 9);
                break;
            }
        break;
        case('pages'):
            switch($action) {
                case('add'):
                case('edit'):
                    osc_add_hook('help_box', 'help_add_jobboard_page', 9);
                break;
                case(''):
                    osc_add_hook('help_box', 'help_jobboard_pages', 9);
                break;
            }
        break;
        case('plugins'):
            switch(urldecode(Params::getParam('file'))) {
                case('jobboard/people.php'):
                    osc_add_hook('help_box', 'help_list_applicants', 9);
                break;
                case('jobboard/people_detail.php'):
                    osc_add_hook('help_box', 'help_detail_applicant', 9);
                break;
            }
        break;
        case('appearance'):
            switch(urldecode(Params::getParam('file'))) {
                case('oc-content/themes/corporateboard/admin/settings.php'):
                    osc_add_hook('help_box', 'help_settings_jobboard', 9);
                break;
                case('oc-content/themes/corporateboard/admin/colors.php'):
                    osc_add_hook('help_box', 'help_appearance_jobboard', 9);
                break;
            }
        break;
    }
}
osc_add_hook('init_admin', 'help_jobboard_init');
function remove_help_core() {
    osc_remove_hook('help_box','addHelp');
}
osc_add_hook('admin_header', 'remove_help_core');

function applicant_admin_menu_current($class) {
    if( urldecode(Params::getParam('file')) === 'jobboard/people_detail.php' ) {
        return 'current';
    }

    return $class;
}
osc_add_filter('current_admin_menu_corporateboard', 'applicant_admin_menu_current');
/* /Help sections */

// register js and css scripts
osc_register_script('jquery-rating', osc_plugin_url(__FILE__) . 'js/rating/jquery.rating.js', 'jquery');
osc_register_script('jquery-metadata', osc_plugin_url(__FILE__) . 'js/rating/jquery.MetaData.js', 'jquery');
osc_register_script('jobboard-people', osc_plugin_url(__FILE__) . 'js/people.js', 'jquery');
osc_register_script('jobboard-people-detail', osc_plugin_url(__FILE__) . 'js/people_detail.js', 'jquery');
osc_register_script('jobboard-item-contact', osc_plugin_url(__FILE__) . 'js/item_contact.js', array('jquery', 'jquery-validate'));
osc_register_script('jobboard-dashboard', osc_plugin_url(__FILE__) . 'js/dashboard.js', array('jquery'));

function admin_assets_jobboard() {
    osc_enqueue_style('jobboard-css', osc_plugin_url(__FILE__) . 'css/styles.css');
    switch(urldecode(Params::getParam('file'))) {
        case('jobboard/dashboard.php'):
            osc_enqueue_style('jquery-rating', osc_plugin_url(__FILE__) . 'css/dashboard.css');
            osc_enqueue_script('jquery-rating');
            osc_enqueue_script('jobboard-people');
            osc_enqueue_script('jobboard-dashboard');
        break;
        case('jobboard/people_detail.php'):
            osc_enqueue_script('jquery-rating');
            osc_enqueue_script('jquery-metadata');
            osc_enqueue_script('jobboard-people-detail');
            osc_enqueue_style('jquery-rating', osc_plugin_url(__FILE__) . 'js/rating/jquery.rating.css');
            osc_enqueue_style('jobboard-people-detail', osc_plugin_url(__FILE__) . 'css/people_detail.css');
        break;
        case('jobboard/people.php'):
            osc_enqueue_script('jquery-rating');
            osc_enqueue_script('jquery-metadata');
            osc_enqueue_script('jobboard-people');
            osc_enqueue_style('jquery-rating', osc_plugin_url(__FILE__) . 'js/rating/jquery.rating.css');
        break;
    }
}
osc_add_hook('init_admin', 'admin_assets_jobboard');

function front_item_contact_validation() {
    if( osc_is_ad_page() ) { ?>
<script src="<?php echo osc_plugin_url(__FILE__) . 'js/item_contact.js'; ?>" type="text/javascript"></script>
<?php }
}
osc_add_hook('footer', 'front_item_contact_validation');

function jobboard_post_actions() {
    switch(urldecode(Params::getParam('file'))) {
        case('jobboard/people.php'):
            switch(Params::getParam('jb_action')) {
                case('unread'):
                    ModelJB::newInstance()->changeUnread(Params::getParam('applicantID'));
                    header('Location: ' . osc_admin_render_plugin_url("jobboard/people.php")); exit;
                break;
            }

            // set default iDisplayLength
            if( Params::getParam('iDisplayLength') != '' ) {
                Cookie::newInstance()->push('applicants_iDisplayLength', Params::getParam('iDisplayLength'));
                Cookie::newInstance()->set();
            } else {
                // set a default value if it's set in the cookie
                if( Cookie::newInstance()->get_value('applicants_iDisplayLength') != '' ) {
                    Params::setParam('iDisplayLength', Cookie::newInstance()->get_value('applicants_iDisplayLength'));
                } else {
                    Params::setParam('iDisplayLength', 10);
                }
            }

            osc_enqueue_script('jquery-rating');
            osc_enqueue_script('jquery-metadata');
            osc_enqueue_style('jquery-rating', osc_plugin_url(__FILE__) . 'js/rating/jquery.rating.css');
        break;
    }
}
osc_add_hook('init_admin', 'jobboard_post_actions');

function jobboard_init_js() {
    $langs = array();
    $langs['delete_string']     = __('Delete', 'jobboard');
    $langs['edit_string']       = __('Edit', 'jobboard');
    $langs['text_hide_filter']  = __('Hide search', 'jobboard');
    $langs['text_show_filter']  = __('Show search', 'jobboard');
    $langs['empty_note_text']   = __('No notes have been added to this applicant', 'jobboard');
    $langs['sex_required']      = __('Sex: this field is required', 'jobboard');
    $langs['birthday_required'] = __('Birthday: this field is required', 'jobboard');
    $langs['invalid_birthday_date'] = __('Invalid birthday date', 'jobboard');
?>
<script type="text/javascript">
    jobboard = {};
    jobboard.langs = <?php echo json_encode($langs); ?>;
    jobboard.ajax_rating = '<?php echo osc_admin_ajax_hook_url('jobboard_rating'); ?>';
    jobboard.ajax_applicant_status_notification = '<?php echo osc_admin_ajax_hook_url('applicant_status_notifitacion'); ?>';
    jobboard.ajax_applicant_status = '<?php echo osc_admin_ajax_hook_url('applicant_status'); ?>';
    jobboard.ajax_note_add = '<?php echo osc_admin_ajax_hook_url('note_add'); ?>';
    jobboard.ajax_note_edit = '<?php echo osc_admin_ajax_hook_url('note_edit'); ?>';
    jobboard.ajax_note_delete = '<?php echo osc_admin_ajax_hook_url('note_delete'); ?>';
    jobboard.ajax_dismiss_tip = '<?php echo osc_admin_ajax_hook_url('dismiss_tip'); ?>';
</script>
<?php }
osc_add_hook('admin_header', 'jobboard_init_js', 1);
osc_add_hook('header', 'jobboard_init_js', 1);

function default_settings_jobboard() {
    // always active osc_item_attachment
    if( !osc_item_attachment() ) {
        osc_set_preference('item_attachment', true);
    }
    if( osc_price_enabled_at_items() ) {
        osc_set_preference('enableField#f_price@items', false);
    }
    if( osc_images_enabled_at_items() ) {
        osc_set_preference('enableField#images@items', false);
    }
    if( osc_max_images_per_item() > 0 ) {
        osc_set_preference('numImages@items', 0);
    }
    //reset preferences
    osc_reset_preferences();

    if(Params::getParam('page')=='items' && Params::getParam('action')=='post') {
        Session::newInstance()->_setForm('contactName', osc_page_title());
        Session::newInstance()->_setForm('contactEmail', osc_contact_email());
    }
}
osc_add_hook('init_admin', 'default_settings_jobboard');

function jobboard_titles($title) {
    $page = Params::getParam('page');
    $action = Params::getParam('action');
    switch($page) {
        case 'items':
            if($action=='') {
                $title = preg_replace('|^(.*)&raquo;|', __('Manage vacancies','jobboard').' &raquo;', $title);
            } else if($action=='post') {
                $title = preg_replace('|^(.*)&raquo;|', __('Add vacancy','jobboard').' &raquo;', $title);
            } else if($action=='item_edit') {
                $title = preg_replace('|^(.*)&raquo;|', __('Edit vacancy','jobboard').' &raquo;', $title);
            }
            break;
        case 'plugins':
            $file = Params::getParam('file');
            if($file=='jobboard/dashboard.php') {
                $title = preg_replace('|^(.*)&raquo;|', __('Dashboard','jobboard').' &raquo;', $title);
            } else if($file=='jobboard/people.php') {
                $title = preg_replace('|^(.*)&raquo;|', __('Applicants','jobboard').' &raquo;', $title);
            } else if($file=='jobboard/people_detail.php') {
                $peopleId = Params::getParam('people');
                $people = ModelJB::newInstance()->getApplicant($peopleId);
                $title = preg_replace('|^(.*)&raquo;|', sprintf(__('%s &raquo; Applicants', 'jobboard'), $people['s_name']).' &raquo;', $title);
            }
            break;
        default:
            break;
    }
    return $title;
}
osc_add_filter('admin_title', 'jobboard_titles', 9);

/* H1 titles */
function jobboard_customPageHeader_vacancies() { ?>
    <h1><?php _e('Vacancies', 'jobboard'); ?>
        <a href="#" class="btn ico ico-32 ico-help float-right"></a>
        <a href="<?php echo osc_admin_base_url(true) . '?page=items&action=post' ; ?>" class="btn btn-green ico ico-32 ico-add-white float-right"><?php _e('Add vacancy', 'jobboard'); ?></a>
    </h1>
<?php
}
function jobboard_customPageHeader_vacancies_post() { ?>
    <h1><?php _e('Vacancies', 'jobboard'); ?>
        <a href="#" class="btn ico ico-32 ico-help float-right"></a>
    </h1>
<?php
}
function corporateboard_remove_title_header(){
    osc_remove_hook('admin_page_header','customPageHeader');

}
if(Params::getParam('page') == 'items'){
    osc_add_hook('admin_header','corporateboard_remove_title_header');
    if(Params::getParam('action') == ''){
        osc_add_hook('admin_page_header','jobboard_customPageHeader_vacancies');
    } else {
        osc_add_hook('admin_page_header','jobboard_customPageHeader_vacancies_post');
    }
}

function jobboard_replace_listing($string) {
    return preg_replace(array('|Listing|', '|listing|'), array(__('Vacancy', 'jobboard'), __('vacancy', 'jobboard')), $string);
}
osc_add_filter('gettext', 'jobboard_replace_listing');
function jobboard_replace_listing_plural($string) {
    return preg_replace(array('|Listings|', '|listings|'), array(__('Vacancies', 'jobboard'), __('vacancies', 'jobboard')), $string);
}
osc_add_filter('gettext', 'jobboard_replace_listing_plural', 1);

// Custom title
osc_add_filter('custom_plugin_title','jobboard_dashboard_title');
function jobboard_dashboard_title($string){
    if(Params::getParam('page') == 'plugins' && Params::getParam('file') == 'jobboard/dashboard.php'){
        $string = __('Dashboard', 'jobboard');
    }
    if(Params::getParam('page') == 'plugins' && Params::getParam('file') == 'jobboard/people.php'){
        $string = __('Applicants', 'jobboard') . '<a href="#" class="btn ico ico-32 ico-help float-right"></a>';
    }
    return $string;
}
/* /H1 titles */

osc_add_hook('admin_items_table','job_items_table_header');
osc_add_filter("items_processing_row", "job_items_row");

/**
 * Apply with linkedin - document.domain
 */
function jobboard_set_domain()
{
    ?>
    <script type="text/javascript">
	   document.domain = 'osclass.com';
    </script>
    <?php
}
// get subdomain - linkedin related - osclass.com/apply/
$url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$parsedUrl = parse_url($url);
$host = explode('.', $parsedUrl['host']);
$a2 = array_pop($host);
$a1 = array_pop($host);
$subdomain = $a1.".".$a2;

if( $subdomain == 'osclass.com') {
    osc_add_hook('header', 'jobboard_set_domain');
}

// -------------------------------------------------------------------------
//                      NOTICE  &  TIPS
// -------------------------------------------------------------------------
/*
 * Show notice at oc-admin
 */
function jobboard_notices(){
    $arrayNotice = osc_apply_filter( 'showNotice', array() );
    $numNotice   = count($arrayNotice);
    if($numNotice > 0 ){
        $randIndex = array_rand($arrayNotice);
        echo '<div class="flashmessage flashmessage-inline" style="min-height:22px;">'.
                $arrayNotice[$randIndex].'<a class="btn ico btn-mini ico-close">x</a></div>';
    }
}
osc_add_hook('jobboard_header_dashboard', 'jobboard_notices',10);

/*
 * Show notice tips at oc-admin, this notice can be dismissed!
 */
function jobboard_notices_tips() {
    $arrayNotice = osc_apply_filter( 'showNoticeTips', array() );
    // remove dismiss tips
    $aDismiss = json_decode( osc_get_preference('notice_tips_dismissed', 'jobboard'), true);
    // remove dismissed tips
    if(!is_null($aDismiss) || is_array($aDismiss)) {
        foreach($aDismiss as $key => $v) {
            unset( $arrayNotice[$key] );
        }
    }

    $numNotice   = count($arrayNotice);
    if($numNotice > 0) {
        $randIndex = array_rand($arrayNotice);
        echo '<div class="flashmessage flashmessage-inline" style="min-height:22px;">'.
                $arrayNotice[$randIndex].sprintf(__('. <a id="dismiss-tip" data-notice-id="%s" href="#">Dismiss</a> to not show again.','jobboard'),
                $randIndex ).'<a class="btn ico btn-mini ico-close">x</a></div>';
    }
}
osc_add_hook('jobboard_header_dashboard', 'jobboard_notices_tips',10) ;

/*
 * Filter - Empty jobs ok
 */
function notice_empty_jobs($arrayNotice) {
    // empty jobs! add new job please
    $notice_empty_jobs = osc_get_preference('notice_empty_jobs', 'jobboard');
    if($notice_empty_jobs=='') {
        osc_set_preference('notice_empty_jobs', '1','jobboard');
        osc_reset_preferences();
        $notice_empty_jobs = osc_get_preference('notice_empty_jobs', 'jobboard');
    }

    if($notice_empty_jobs=='1'){
        $jobs = ModelJB::newInstance()->search(0,1);
        if(count($jobs)>0) {
            osc_set_preference('notice_empty_jobs', '0', 'jobboard');
        }
        // ADD MESSAGE @TODO
        $arrayNotice['notice_empty_jobs'] = __('1 empty jobboard', 'jobboard');
    }
    return $arrayNotice;
}
osc_add_filter('showNoticeTips', 'notice_empty_jobs');

/*
 * Filter - Edit corporate page ok
 */
function notice_edit_corporate_page( $arrayNotice ) {
    // update your corporativa page -> if t_page.dt_mod_date is null
    $notice_update_corporatepage = osc_get_preference('notice_edit_corporatepage', 'jobboard');
    if($notice_update_corporatepage=='') {
        osc_set_preference('notice_edit_corporatepage', '1', 'jobboard');
        osc_reset_preferences();
        $notice_update_corporatepage = osc_get_preference('notice_empty_jobs', 'jobboard');
    }
    if($notice_update_corporatepage=='1') {
        $corporatePage = Page::newInstance()->findByInternalName('corporate');
        if(is_null($corporatePage['dt_mod_date']) ) {
            // ADD MESSAGE @TODO
            $arrayNotice['notice_edit_corporate_page'] = __('2 edit your corporate page', 'jobboard');
        }
    }
    return $arrayNotice;
}
osc_add_filter('showNoticeTips', 'notice_edit_corporate_page');

/*
 * Filter - Tracking empty ok
 */
function notice_ga_tracking($arrayNotice) {
    if(osc_get_preference('googleanalytics_trackingid','jobboard')=='') {
        $arrayNotice['notice_ga_tracking'] = osc_apply_filter( 'corporate_notice_empty_analytics_tag', __('3 google analytics tag empty', 'jobboard'));
    }
    return $arrayNotice;
}
osc_add_filter('showNoticeTips', 'notice_ga_tracking');

/*
 * Filter - Theme colors edited ok
 */
function notice_edit_colors_theme($arrayNotice) {
    // can change theme colors! +link
    if(function_exists('corporateboard_array_theme_options')) {
        $array = corporateboard_array_theme_options();
        $pageColorEdited = false;
        $aDefaults  = $array['defaults'];
        $aKeys      = $array['keys'];
        foreach($aKeys as $key => $value) {
            if($aDefaults[$key] != osc_get_preference($value, 'jobboard') ) {
                $pageColorEdited = true;
                break;
            }
        }
        if(!$pageColorEdited) {
            $arrayNotice['notice_edit_colors_theme'] = __('4 Edit your theme colors url', 'jobboard');
        }
    }
    return $arrayNotice;
}
osc_add_filter('showNoticeTips', 'notice_edit_colors_theme');

/*
 * Filter - Unread applicants ok
 * show allways
 */
function notice_unread_applicants( $arrayNotice ) {
    // unread messages notice
    $numUnread = count( ModelJB::newInstance()->search(0,1000, array('unread' => true) ) );
    if($numUnread>0) {
        $arrayNotice['notice_unread_applicants'] = __(sprintf('There are <b>%s</b> unread applicants', $numUnread), 'jobboard');
    }
    return $arrayNotice;
}
osc_add_filter('showNotice', 'notice_unread_applicants');

/*
 * AJAX - add notice_tip_id to array of dismissed notice
 */
function ajax_dismiss_tip() {
    $notice_id = Params::getParam('noticeID');
    $array = json_decode( osc_get_preference('notice_tips_dismissed', 'jobboard'), true);

    if(!is_array($array)) {
        $array = array();
    }

    $array_[$notice_id] = 1;
    $merge = array_merge($array, $array_);

    if( osc_set_preference('notice_tips_dismissed', json_encode($merge),'jobboard') ) {
        echo '1';
    } else {
        echo '0';
    }
}
osc_add_hook('ajax_admin_dismiss_tip', 'ajax_dismiss_tip');

?>