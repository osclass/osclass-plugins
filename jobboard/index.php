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

    osc_set_preference('upload_path', osc_content_path()."uploads/", 'jobboard_plugin', 'STRING');
    osc_set_preference('allow_cv_upload', '1', 'jobboard_plugin', 'INTEGER');
    osc_set_preference('version', 100, 'jobboard_plugin', 'INTEGER');
}

function job_call_after_uninstall() {
    ModelJB::newInstance()->uninstall();

    osc_delete_preference('upload_path', 'jobboard_plugin');
    osc_delete_preference('allow_cv_upload', 'jobboard_plugin');
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
    $request = Params::getParamsAsArray();
    foreach ($request as $k => $v) {
        if (preg_match('|(.+?)#(.+)|', $k, $m)) {
            $dataItem[$m[1]][$m[2]] = $v;
        }
    }

    // insert locales
    foreach ($dataItem as $k => $_data) {
        ModelJB::newInstance()->insertJobsAttrDescription($itemID, $k, $_data['desired_exp'], $_data['studies'], $_data['min_reqs'], $_data['desired_reqs'], $_data['contract']);
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
    $request  = Params::getParamsAsArray();
    foreach ($request as $k => $v) {
        if (preg_match('|(.+?)#(.+)|', $k, $m)) {
            $dataItem[$m[1]][$m[2]] = $v;
        }
    }

    // insert locales
    foreach ($dataItem as $k => $_data) {
        ModelJB::newInstance()->replaceJobsAttrDescriptions($itemID, $k, $_data['desired_exp'], $_data['studies'], $_data['min_reqs'], $_data['desired_reqs'], $_data['contract']);
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
        __('Plugin options', 'jobboard'),
        osc_admin_render_plugin_url('jobboard/conf.php').'?section=types',
        'jobboard_options',
        'moderator'
    );
}
osc_add_hook('admin_header','jobboard_admin_menu');

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
    //reset preferences
    osc_reset_preferences();
}
osc_add_hook('init_admin', 'default_settings_jobboard');

/* File: jobboard/index.php */