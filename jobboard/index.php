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

require_once('ModelJB.php');

function job_call_after_install() {
    // Insert here the code you want to execute after the plugin's install
    // for example you might want to create a table or modify some values
	
    // In this case we'll create a table to store the Example attributes
    ModelJB::newInstance()->import('jobboard/struct.sql');

    osc_set_preference('upload_path', osc_content_path()."uploads/", 'jobboard_plugin', 'STRING');
    osc_set_preference('allow_cv_upload', '1', 'jobboard_plugin', 'INTEGER');
    osc_set_preference('version', 100, 'jobboard_plugin', 'INTEGER');
}

function job_call_after_uninstall() {
    // Insert here the code you want to execute after the plugin's uninstall
    // for example you might want to drop/remove a table or modify some values

    // In this case we'll remove the table we created to store Example attributes
    ModelJB::newInstance()->uninstall();
    
    osc_delete_preference('upload_path', 'jobboard_plugin');
    osc_delete_preference('allow_cv_upload', 'jobboard_plugin');
    osc_delete_preference('version', 'jobboard_plugin');
}

function job_form($catId = null) {
    // We received the categoryID
    if($catId!="") {
        // We check if the category is the same as our plugin
        if(osc_is_this_category('jobboard_plugin', $catId)) {
            require_once 'item_edit.php';
        }
    }
    Session::newInstance()->_clearVariables();
}

function job_form_post($catId = null, $item_id = null)  {
    // We received the categoryID and the Item ID
    if($catId!="") {
        // We check if the category is the same as our plugin
        if(osc_is_this_category('jobboard_plugin', $catId) && $item_id!=null) {
            // Insert the data in our plugin's table
            ModelJB::newInstance()->insertJobsAttr($item_id, Params::getParam('relation'), Params::getParam('positionType'), Params::getParam('salaryText') );

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
                ModelJB::newInstance()->insertJobsAttrDescription($item_id, $k, $_data['desired_exp'], $_data['studies'], $_data['min_reqs'], $_data['desired_reqs'], $_data['contract']);
            }
        }
    }
}

// Self-explanatory
function job_item_detail() {
    if(osc_is_this_category('jobboard_plugin', osc_item_category_id())) {
        $detail = ModelJB::newInstance()->getJobsAttrByItemId(osc_item_id());
        $descriptions = ModelJB::newInstance()->getJobsAttrDescriptionsByItemId(osc_item_id());
        $detail['locale'] = array();
        foreach ($descriptions as $desc) {
            $detail['locale'][$desc['fk_c_locale_code']] = $desc;
        }
        require_once 'item_detail.php';
    }
}

// Self-explanatory
function job_item_edit($catId = null, $item_id = null) {
    if(osc_is_this_category('jobboard_plugin', $catId)) {
        $conn = getConnection();
        $detail = ModelJB::newInstance()->getJobsAttrByItemId($item_id);
        $descriptions = ModelJB::newInstance()->getJobsAttrDescriptionsByItemId($item_id);
        $detail['locale'] = array();
        foreach ($descriptions as $desc) {
            $detail['locale'][$desc['fk_c_locale_code']] = $desc;
        }
        require_once 'item_edit.php';
    }
    Session::newInstance()->_clearVariables();
}

function job_item_edit_post($catId = null, $item_id = null) {
    // We received the categoryID and the Item ID
    if($catId!=null) {
        // We check if the category is the same as our plugin
        if(osc_is_this_category('jobboard_plugin', $catId)) {
            ModelJB::newInstance()->replaceJobsAttr( $item_id, Params::getParam('relation'), Params::getParam('positionType'), Params::getParam('salaryText'));
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
                ModelJB::newInstance()->replaceJobsAttrDescriptions( $item_id, $k, $_data['desired_exp'], $_data['studies'], $_data['min_reqs'], $_data['desired_reqs'], $_data['contract'] );
            }
        }
    }
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

function job_admin_configuration() {
    // Standard configuration page for plugin which extend item's attributes
    osc_plugin_configure_view(osc_plugin_path(__FILE__) );
}


function job_pre_item_post()
{

    Session::newInstance()->_setForm('pj_salaryText', Params::getParam('salaryText') );
    Session::newInstance()->_setForm('pj_relation',  Params::getParam('relation') );
    Session::newInstance()->_setForm('pj_positionType',  Params::getParam('positionType') );
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
    Session::newInstance()->_keepForm('pj_salaryText');
    Session::newInstance()->_keepForm('pj_relation');
    Session::newInstance()->_keepForm('pj_positionType');
    Session::newInstance()->_keepForm('pj_data');
}

function job_save_inputs_into_session()
{
    Session::newInstance()->_keepForm('pj_salaryText');
    Session::newInstance()->_keepForm('pj_relation');
    Session::newInstance()->_keepForm('pj_positionType');
    Session::newInstance()->_keepForm('pj_data');
}



// this is needed in order to be able to activate the plugin
osc_register_plugin(osc_plugin_path(__FILE__), 'job_call_after_install');
// this is a hack to show a Uninstall link at plugins table (you could also use some other hook to show a custom option panel)
osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", 'job_call_after_uninstall');


// when publishing an item we show an extra form with more attributes
osc_add_hook('item_form', 'job_form');
// to add that new information to our custom table
osc_add_hook('item_form_post', 'job_form_post');

// show an item special attributes
osc_add_hook('item_detail', 'job_item_detail');

// edit an item special attributes
osc_add_hook('item_edit', 'job_item_edit');
// edit an item special attributes POST
osc_add_hook('item_edit_post', 'job_item_edit_post');

// delete locale
osc_add_hook('delete_locale', 'job_delete_locale');
// delete item
osc_add_hook('delete_item', 'job_delete_item');

// previous to insert item
osc_add_hook('pre_item_post', 'job_pre_item_post') ;
osc_add_hook('pre_item_edit', 'job_pre_item_post') ;
// save input values into session
osc_add_hook('save_input_session', 'job_save_inputs_into_session' );

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