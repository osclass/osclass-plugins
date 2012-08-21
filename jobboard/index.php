<?php
/*
Plugin Name: Job Board
Plugin URI: http://www.osclass.org/
Description: Job Board
Version: 1.0
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: jobboard_plugin
Plugin update URI: job-board
*/

define('JOBBOARD_PATH', dirname(__FILE__) . '/') ;
require_once(JOBBOARD_PATH . 'ModelJB.php');

function job_call_after_install() {
    ModelJB::newInstance()->import('jobboard/struct.sql');

    osc_set_preference('upload_path', osc_content_path() . "uploads/", 'jobboard_plugin', 'STRING');
    osc_set_preference('version', 100, 'jobboard_plugin', 'INTEGER');
}

function job_call_after_uninstall() {
    ModelJB::newInstance()->uninstall();

    osc_delete_preference('upload_path', 'jobboard_plugin');
    osc_delete_preference('version', 'jobboard_plugin');
}

/* FORM JOB BOARD */
function jobboard_form($catID = null) {
    $detail = array(
        'e_position_type' => '',
        's_salary_text'   => '',
        'locale'          => array()
    );
    foreach(osc_get_locales() as $locale) {
        $detail['locale'][$locale['pk_c_code']] = array(
            's_desired_exp' => '',
            's_studies'     => '',
            'min_reqs'      => '',
            'desired_reqs'  => '',
            'contract'      => ''
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
            $detail['locale'][$locale['pk_c_code']]['s_desired_exp']          = $data[$locale['pk_c_code']]['desired_exp'];
            $detail['locale'][$locale['pk_c_code']]['s_studies']              = $data[$locale['pk_c_code']]['studies'];
            $detail['locale'][$locale['pk_c_code']]['s_minimum_requirements'] = $data[$locale['pk_c_code']]['min_reqs'];
            $detail['locale'][$locale['pk_c_code']]['s_desired_requirements'] = $data[$locale['pk_c_code']]['desired_reqs'];
            $detail['locale'][$locale['pk_c_code']]['s_contract']             = $data[$locale['pk_c_code']]['contract'];
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

/* CONTACT */
function jobboard_save_contact_listing($item) {
    $itemID = osc_item_id();
    $name   = Params::getParam('yourName');
    $email  = Params::getParam('yourEmail');
    $cover  = Params::getParam('message');
    $phone  = Params::getParam('phoneNumber');
    $aCV    = Params::getFiles('attachment');

    // check fields
    if( $name === '' ) {
        osc_add_flash_error_message(__("Name is required", 'jobboard'));
        _save_jobboard_contact_listing();
        header('Location: ' . osc_item_url()); die;
    }
    if( $email === '' ) {
        osc_add_flash_error_message(__("Email is required", 'jobboard'));
        _save_jobboard_contact_listing();
        header('Location: ' . osc_item_url()); die;
    }
    if( $cover === '' ) {
        osc_add_flash_error_message(__("Cover is required", 'jobboard'));
        _save_jobboard_contact_listing();
        header('Location: ' . osc_item_url()); die;
    }
    if( isset($aCV['name']) && $aCV['name'] === '' ) {
        osc_add_flash_error_message(__("CV is required", 'jobboard'));
        _save_jobboard_contact_listing();
        header('Location: ' . osc_item_url()); die;
    }

    // insert to database
    $mJB = ModelJB::newInstance();

    $applicantID = $mJB->insertApplicant($itemID, $name, $email, $cover, $phone);
    // return to listing url
    if( !$applicantID ) {
        osc_add_flash_error_message(__("There were some problem processing your application, please try again", 'jobboard'));
        header('Location: ' . osc_item_url()); die;
    }

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

    if( $error_attachment ) {
        ModelJB::newInstance()->deleteApplicant($applicantID);
        osc_add_flash_error_message(__("There were some problem processing your application, please try again", 'jobboard'));
        header('Location: ' . osc_item_url()); die;
    }

    return true;
}
osc_add_hook('post_item_contact_post', 'jobboard_save_contact_listing');
osc_remove_hook('hook_email_item_inquiry', 'fn_email_item_inquiry');

function _save_jobboard_contact_listing() {
    Session::newInstance()->_setForm('yourEmail',    Params::getParam('yourEmail'));
    Session::newInstance()->_setForm('yourName',     Params::getParam('yourName'));
    Session::newInstance()->_setForm('phoneNumber',  Params::getParam('phoneNumber'));
    Session::newInstance()->_setForm('message_body', Params::getParam('message'));
}
/* /CONTACT */

function job_delete_locale($locale) {
    ModelJB::newInstance()->deleteLocale($locale);
}

function job_delete_item($item_id) {
    ModelJB::newInstance()->deleteItem($item_id);
}

function jobboard_admin_menu() { ?>
<style type="text/css" media="screen">
    .ico-jobboard{
        background-image: url('<?php printf('%soc-content/plugins/%simg/icon.png', osc_base_url(), osc_plugin_folder(__FILE__)); ?>') !important;
    }
    body.compact .ico-jobboard{
        background-image: url('<?php printf('%soc-content/plugins/%simg/iconCompact.png', osc_base_url(), osc_plugin_folder(__FILE__)); ?>') !important;
    }
</style>
<?php
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
        __('Applicants'),
        osc_admin_render_plugin_url("jobboard/people.php"),
        'jobboard_people',
        'moderator'
    );
}
osc_add_hook('admin_header','jobboard_admin_menu');

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
    return $status_array;
}

function job_filter_options($options, $aRow) {
    $options[] = '<a href="' . osc_admin_render_plugin_url("jobboard/people.php&jobId=") . $aRow['pk_i_id'] . '">' . __('View applicants for this job', 'jobboard') . '</a>';
    return $options;
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
        if (preg_match('|(.+?)#(.+)|', $k, $m)) {
            $dataItem[$m[1]][$m[2]] = $v;
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

// delete locale
osc_add_hook('delete_locale', 'job_delete_locale');
// delete item
osc_add_hook('delete_item', 'job_delete_item');

function css_jobs() {
    echo '<link href="' . osc_plugin_url(__FILE__) . 'css/styles.css" rel="stylesheet" type="text/css">' . PHP_EOL;
}
osc_add_hook('header', 'css_jobs');

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
}
osc_add_hook('init_admin', 'default_settings_jobboard');

osc_add_filter('actions_manage_items', 'job_filter_options');

?>
