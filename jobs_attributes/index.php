<?php
/*
Plugin Name: Jobs attributes
Plugin URI: http://www.osclass.org/
Description: This plugin extends a category of items to store jobs attributes such as salary, requirements, timetable, and so on.
Version: 2.1.1
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: jobs_plugin
Plugin update URI: http://www.osclass.org/files/plugins/jobs_attributes/update.php
*/

// Adds some plugin-specific search conditions
function job_search_conditions($params = '') {
    // we need conditions and search tables (only if we're using our custom tables)
    if($params!='') {
        $has_conditions = false;
        $has_salary = false;
        foreach($params as $key => $value) {
            // We may want to  have param-specific searches
            switch($key) {
                case 'relation':
                    Search::newInstance()->addConditions(sprintf("%st_item_job_attr.e_relation = '%s'", DB_TABLE_PREFIX, $value));
                    $has_conditions = true;
                    break;
                case 'companyName':
                    if($value!='') {
                        Search::newInstance()->addConditions(sprintf("%st_item_job_attr.s_company_name = '%%%s%%'", DB_TABLE_PREFIX, $value));
                        $has_conditions = true;
                    }
                    break;
                case 'positionType':
                    if($value!='UNDEF') {
                        Search::newInstance()->addConditions(sprintf("%st_item_job_attr.e_position_type = '%s'", DB_TABLE_PREFIX, $value));
                        $has_conditions = true;
                    }
                    break;
                case 'salaryRange':
                    $salaryRange = explode(" - ", $value);
                    $salaryMin = ($salaryRange[0]!='')?$salaryRange[0]:job_plugin_salary_min();
                    $salaryMax = (isset($salaryRange[1]) && $salaryRange[1]!='')?$salaryRange[1]:job_plugin_salary_max();
                    Search::newInstance()->addConditions(sprintf("%st_item_job_attr.i_salary_min >= %d", DB_TABLE_PREFIX, $salaryMin));
                    Search::newInstance()->addConditions(sprintf("%st_item_job_attr.i_salary_max <= %d", DB_TABLE_PREFIX, $salaryMax));
                    Search::newInstance()->addConditions(sprintf("%st_item_job_attr.e_salary_period = '%s'", DB_TABLE_PREFIX, $params['salaryPeriod']));
                    $has_conditions = true;
                    break;
                /*case 'salaryMin':
                    if($value != 0) {
                        Search::newInstance()->addConditions(sprintf("%st_item_job_attr.i_salary_min >= %d", DB_TABLE_PREFIX, $value));
                        $has_conditions = true;
                        if(!$has_salary) {
                            Search::newInstance()->addConditions(sprintf("%st_item_job_attr.e_salary_period = '%s'", DB_TABLE_PREFIX, $params['salaryPeriod']));
                            $has_salary = true;
                        }
                    }
                    break;
                case 'salaryMax':
                    if($value > 0) {
                        Search::newInstance()->addConditions(sprintf("%st_item_job_attr.i_salary_max <= %d", DB_TABLE_PREFIX, $value));
                        $has_conditions = true;
                        if(!$has_salary) {
                            Search::newInstance()->addConditions(sprintf("%st_item_job_attr.e_salary_period = '%s'", DB_TABLE_PREFIX, $params['salaryPeriod']));
                            $has_salary = true;
                        }
                    }*/
//                    break;
                default:
                    break;
            }
        }

        // Only if we have some values at the params we add our table and link with the ID of the item.
        if($has_conditions) {
            Search::newInstance()->addConditions(sprintf("%st_item_job_attr.fk_i_item_id = %st_item.pk_i_id", DB_TABLE_PREFIX, DB_TABLE_PREFIX));
            Search::newInstance()->addConditions(sprintf("%st_item_job_description_attr.fk_i_item_id = %st_item.pk_i_id", DB_TABLE_PREFIX, DB_TABLE_PREFIX));
            Search::newInstance()->addTable(sprintf("%st_item_job_attr", DB_TABLE_PREFIX));
            Search::newInstance()->addTable(sprintf("%st_item_job_description_attr", DB_TABLE_PREFIX));
        }
    }
}

function job_call_after_install() {
    // Insert here the code you want to execute after the plugin's install
    // for example you might want to create a table or modify some values
	
    // In this case we'll create a table to store the Example attributes
    $conn = getConnection();
    $conn->autocommit(false);
    try {
        $path = osc_plugin_resource('jobs_attributes/struct.sql');
        $sql = file_get_contents($path);
        $conn->osc_dbImportSQL($sql);
        $conn->commit();
        osc_set_preference('cv_email', '', 'jobs_plugin', 'STRING');
        osc_set_preference('allow_cv_upload', '0', 'jobs_plugin', 'BOOLEAN');
        osc_set_preference('allow_cv_unreg', '1', 'jobs_plugin', 'BOOLEAN');
        osc_set_preference('send_me_cv', '0', 'jobs_plugin', 'BOOLEAN');
        osc_set_preference('salary_min', '0', 'jobs_plugin', 'INTEGER');
        osc_set_preference('salary_max', '80000', 'jobs_plugin', 'INTEGER');
        osc_set_preference('salary_step', '100', 'jobs_plugin', 'INTEGER');
    } catch (Exception $e) {
        $conn->rollback();
        echo $e->getMessage();
    }
    $conn->autocommit(true);
}

function job_call_after_uninstall() {
    // Insert here the code you want to execute after the plugin's uninstall
    // for example you might want to drop/remove a table or modify some values

    // In this case we'll remove the table we created to store Example attributes
    $conn = getConnection();
    $conn->autocommit(false);
    try {
        $conn->osc_dbExec("DELETE FROM %st_plugin_category WHERE s_plugin_name = 'jobs_plugin'", DB_TABLE_PREFIX);
        $conn->osc_dbExec('DROP TABLE %st_item_job_description_attr', DB_TABLE_PREFIX);
        $conn->osc_dbExec('DROP TABLE %st_item_job_attr', DB_TABLE_PREFIX);
        $conn->commit();
        osc_delete_preference('cv_email', 'jobs_plugin');
        osc_delete_preference('allow_cv_upload', 'jobs_plugin');
        osc_delete_preference('allow_cv_unreg', 'jobs_plugin');
        osc_delete_preference('send_me_cv', 'jobs_plugin');
    } catch (Exception $e) {
        $conn->rollback();
        echo $e->getMessage();
    }
    $conn->autocommit(true);
}

function job_form($catId = null) {
    // We received the categoryID
    if($catId!="") {
        // We check if the category is the same as our plugin
        if(osc_is_this_category('jobs_plugin', $catId)) {
            require_once 'item_edit.php';
        }
    }
    Session::newInstance()->_clearVariables();
}

function job_search_form($catId = null) {
	// We received the categoryID
	if($catId!=null) {
		// We check if the category is the same as our plugin
        foreach($catId as $id) {
    		if(osc_is_this_category('jobs_plugin', $id)) {
	    		include_once 'search_form.php';
	    		break;
	    	}
        }
	}
}

function job_form_post($catId = null, $item_id = null)  {
    // We received the categoryID and the Item ID
    $conn = getConnection();
    if($catId!="") {
        // We check if the category is the same as our plugin
        if(osc_is_this_category('jobs_plugin', $catId) && $item_id!=null) {
            // Insert the data in our plugin's table
            $salaryRange = explode(" - ", Params::getParam('salaryRange'));
            $salaryMin = ($salaryRange[0]!='')?$salaryRange[0]:job_plugin_salary_min();
            $salaryMax = (isset($salaryRange[1]) && $salaryRange[1]!='')?$salaryRange[1]:job_plugin_salary_max();
            $conn->osc_dbExec("INSERT INTO %st_item_job_attr (fk_i_item_id, e_relation, s_company_name, e_position_type, i_salary_min, i_salary_max, e_salary_period) VALUES (%d, '%s', '%s', '%s', %d, %d, '%s')", DB_TABLE_PREFIX, $item_id, Params::getParam('relation'), Params::getParam('companyName'), Params::getParam('positionType'), $salaryMin, $salaryMax, Params::getParam('salaryPeriod') );
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
                $conn->osc_dbExec("INSERT INTO %st_item_job_description_attr (fk_i_item_id, fk_c_locale_code, s_desired_exp, s_studies, s_minimum_requirements, s_desired_requirements, s_contract, s_company_description) VALUES (%d, '%s', '%s', '%s', '%s', '%s', '%s', '%s')", DB_TABLE_PREFIX, $item_id, $k, $_data['desired_exp'], $_data['studies'], $_data['min_reqs'], $_data['desired_reqs'], $_data['contract'], $_data['company_desc'] );
            }
        }
    }
}

// Self-explanatory
function job_item_detail() {
    if(osc_is_this_category('jobs_plugin', osc_item_category_id())) {
        $conn = getConnection();
        $detail = $conn->osc_dbFetchResult("SELECT * FROM %st_item_job_attr WHERE fk_i_item_id = %d", DB_TABLE_PREFIX, osc_item_id());

        $descriptions = $conn->osc_dbFetchResults('SELECT * FROM %st_item_job_description_attr WHERE fk_i_item_id = %d', DB_TABLE_PREFIX, osc_item_id());
        $detail['locale'] = array();
        foreach ($descriptions as $desc) {
            $detail['locale'][$desc['fk_c_locale_code']] = $desc;
        }
        require_once 'item_detail.php';
    }
}

// Self-explanatory
function job_item_edit($catId = null, $item_id = null) {
    if(osc_is_this_category('jobs_plugin', $catId)) {
        $conn = getConnection();
        $detail = $conn->osc_dbFetchResult("SELECT * FROM %st_item_job_attr WHERE fk_i_item_id = %d", DB_TABLE_PREFIX, $item_id);

        $descriptions = $conn->osc_dbFetchResults('SELECT * FROM %st_item_job_description_attr WHERE fk_i_item_id = %d', DB_TABLE_PREFIX, $item_id);
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
        if(osc_is_this_category('jobs_plugin', $catId)) {
            $salaryRange = explode(" - ", Params::getParam('salaryRange'));
            $salaryMin = ($salaryRange[0]!='')?$salaryRange[0]:job_plugin_salary_min();
            $salaryMax = (isset($salaryRange[1]) && $salaryRange[1]!='')?$salaryRange[1]:job_plugin_salary_max();
            $conn = getConnection() ;
            $conn->osc_dbExec("REPLACE INTO %st_item_job_attr (fk_i_item_id, e_relation, s_company_name, e_position_type, i_salary_min, i_salary_max, e_salary_period) VALUES (%d, '%s', '%s', '%s', %d, %d, '%s')", DB_TABLE_PREFIX, $item_id, Params::getParam('relation'), Params::getParam('companyName'), Params::getParam('positionType'), $salaryMin, $salaryMax, Params::getParam('salaryPeriod') );
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
                $conn->osc_dbExec("REPLACE INTO %st_item_job_description_attr (fk_i_item_id, fk_c_locale_code, s_desired_exp, s_studies, s_minimum_requirements, s_desired_requirements, s_contract, s_company_description) VALUES (%d, '%s', '%s', '%s', '%s', '%s', '%s', '%s')", DB_TABLE_PREFIX, $item_id, $k, $_data['desired_exp'], $_data['studies'], $_data['min_reqs'], $_data['desired_reqs'], $_data['contract'], $_data['company_desc'] );
            }
        }
    }
}

function job_delete_locale($locale) {
    $conn = getConnection();
    $conn->osc_dbExec("DELETE FROM %st_item_job_description_attr WHERE fk_c_locale_code = '" . $locale . "'", DB_TABLE_PREFIX);
}

function job_delete_item($item) {
    $conn = getConnection();
    $conn->osc_dbExec("DELETE FROM %st_item_job_attr WHERE fk_i_item_id = '" . $item . "'", DB_TABLE_PREFIX);
    $conn->osc_dbExec("DELETE FROM %st_item_job_description_attr WHERE fk_i_item_id = '" . $item . "'", DB_TABLE_PREFIX);
}

function jobs_admin_menu() {
    echo '<h3><a href="#">Jobs plugin</a></h3>
    <ul> 
        <li><a href="'.osc_admin_configure_plugin_url("jobs_attributes/index.php").'">&raquo; ' . __('Configure plugin', 'jobs_attributes') . '</a></li>
        <li><a href="'.osc_admin_render_plugin_url("jobs_attributes/conf.php").'?section=types">&raquo; ' . __('Plugin Options', 'jobs_attributes') . '</a></li>
    </ul>';
}

function job_admin_configuration() {
    // Standard configuration page for plugin which extend item's attributes
    osc_plugin_configure_view(osc_plugin_path(__FILE__) );
}

function job_plugin_salary_min() {
    return osc_get_preference('salary_min', 'jobs_plugin');
}

function job_plugin_salary_max() {
    return osc_get_preference('salary_max', 'jobs_plugin');
}

function job_plugin_salary_step() {
    return osc_get_preference('salary_step', 'jobs_plugin');
}

function job_pre_item_post()
{
    $salaryRange = explode(" - ", Params::getParam('salaryRange'));
    $salaryMin   = ($salaryRange[0]!='')?$salaryRange[0]:job_plugin_salary_min() ;
    $salaryMax   = (isset($salaryRange[1]) && $salaryRange[1]!='')?$salaryRange[1]:job_plugin_salary_max() ;
    // extract digits from salary Min & Max
    $salaryMin   = preg_match('/(\d*)/', $salaryMin, $matches);
    $salaryMin   = $matches[0];
    $salaryMax   = preg_match('/(\d*)/', $salaryMax, $matches);
    $salaryMax   = $matches[0];
    Session::newInstance()->_setForm('pj_salaryMin', $salaryMin );
    Session::newInstance()->_setForm('pj_salaryMax', $salaryMax );
    Session::newInstance()->_setForm('pj_relation',  Params::getParam('relation') );
    Session::newInstance()->_setForm('pj_companyName',  Params::getParam('companyName') );
    Session::newInstance()->_setForm('pj_positionType',  Params::getParam('positionType') );
    Session::newInstance()->_setForm('pj_salaryPeriod',  Params::getParam('salaryPeriod') );
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
    Session::newInstance()->_keepForm('pj_salaryMin');
    Session::newInstance()->_keepForm('pj_salaryMax');
    Session::newInstance()->_keepForm('pj_relation');
    Session::newInstance()->_keepForm('pj_companyName');
    Session::newInstance()->_keepForm('pj_positionType');
    Session::newInstance()->_keepForm('pj_salaryPeriod');
    Session::newInstance()->_keepForm('pj_data');
}

function job_save_inputs_into_session()
{
    Session::newInstance()->_keepForm('pj_salaryMin');
    Session::newInstance()->_keepForm('pj_salaryMax');
    Session::newInstance()->_keepForm('pj_relation');
    Session::newInstance()->_keepForm('pj_companyName');
    Session::newInstance()->_keepForm('pj_positionType');
    Session::newInstance()->_keepForm('pj_salaryPeriod');
    Session::newInstance()->_keepForm('pj_data');
}

// This is needed in order to be able to activate the plugin
osc_register_plugin(osc_plugin_path(__FILE__), 'job_call_after_install');
// This is a hack to show a Configure link at plugins table (you could also use some other hook to show a custom option panel)
osc_add_hook(osc_plugin_path(__FILE__)."_configure", 'job_admin_configuration');
// This is a hack to show a Uninstall link at plugins table (you could also use some other hook to show a custom option panel)
osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", 'job_call_after_uninstall');

// When publishing an item we show an extra form with more attributes
osc_add_hook('item_form', 'job_form');
// To add that new information to our custom table
osc_add_hook('item_form_post', 'job_form_post');

// When searching, display an extra form with our plugin's fields
osc_add_hook('search_form', 'job_search_form');
// When searching, add some conditions
osc_add_hook('search_conditions', 'job_search_conditions');

// Show an item special attributes
osc_add_hook('item_detail', 'job_item_detail');

// Edit an item special attributes
osc_add_hook('item_edit', 'job_item_edit');
// Edit an item special attributes POST
osc_add_hook('item_edit_post', 'job_item_edit_post');

//Delete locale
osc_add_hook('delete_locale', 'job_delete_locale');
//Delete item
osc_add_hook('delete_item', 'job_delete_item');

// Admin menu
osc_add_hook('admin_menu', 'jobs_admin_menu');

// previous to insert item
osc_add_hook('pre_item_post', 'job_pre_item_post') ;
osc_add_hook('pre_item_edit', 'job_pre_item_post') ;
// save input values into session
osc_add_hook('save_input_session', 'job_save_inputs_into_session' );
?>
