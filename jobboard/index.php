<?php
/*
Plugin Name: Job board
Plugin URI: http://www.osclass.org/
Description: This plugin convert the site in a job board
Version: 0.9
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: jobboard
Plugin update URI: 
*/

define( 'JOBBOARD_VERSION', 0.9 ) ;
define( 'JOBBOARD_PATH', dirname( __FILE__ ) . '/' ) ;

require_once JOBBOARD_PATH . 'class/Jobboard.php' ;
require_once JOBBOARD_PATH . 'helpers.php' ;

function job_call_after_install() {
    // import database structure: t_item_jobboard, t_item_jobboard_description
    Jobboard::newInstance()->import( 'jobboard/struct.sql' ) ;

    osc_set_preference( 'company', '', 'jobboard', 'STRING' ) ;
}

function job_call_after_uninstall() {
    // delete plugin tables: t_item_jobboard, t_item_jobboard_description
    Jobboard::newInstance()->uninstall() ;

    osc_delete_preference( 'company', 'jobboard' ) ;
}

// TODO: search conditions
function job_search_conditions($params = '') {
    // we need conditions and search tables (only if we're using our custom tables)
    if($params!='') {
        $has_conditions = false;
        $has_salary = false;
        foreach($params as $key => $value) {
            // We may want to  have param-specific searches
            switch($key) {
                case 'relation':
                    if($value != "") {
                        Search::newInstance()->addConditions(sprintf("%st_item_job_attr.e_relation = '%s'", DB_TABLE_PREFIX, $value));
                        $has_conditions = true;
                    }
                    break;
                case 'companyName':
                    if($value != '') {
                        Search::newInstance()->addConditions(sprintf("%st_item_job_attr.s_company_name LIKE '%%%s%%'", DB_TABLE_PREFIX, $value));
                        $has_conditions = true;
                    }
                    break;
                case 'positionType':

                    if($value!='UNDEF' && $value != '') {
                        Search::newInstance()->addConditions(sprintf("%st_item_job_attr.e_position_type = '%s'", DB_TABLE_PREFIX, $value));
                        $has_conditions = true;
                    }
                    break;
                case 'salaryRange':
                    if($params['salaryPeriod']!='') {
                        $salaryRange = explode(" - ", $value);
                        $salaryMin = ($salaryRange[0]!='')?$salaryRange[0]:job_plugin_salary_min();
                        $salaryMax = (isset($salaryRange[1]) && $salaryRange[1]!='')?$salaryRange[1]:job_plugin_salary_max();

                        $salaryHour = job_to_salary_hour( $params['salaryPeriod'], $salaryMin, $salaryMax) ;

                        Search::newInstance()->addConditions(sprintf("%st_item_job_attr.i_salary_min_hour >= %d", DB_TABLE_PREFIX, $salaryHour['min']));
                        Search::newInstance()->addConditions(sprintf("%st_item_job_attr.i_salary_max_hour <= %d", DB_TABLE_PREFIX, $salaryHour['max']));
                        $has_conditions = true;
                    }
                    break;
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

function job_form($catId = null) {
    $detail = array() ;

    require JOBBOARD_PATH . 'view/item_form.php';
    Session::newInstance()->_clearVariables();
}

// TODO: search form
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

function job_form_post($catID, $itemID)  {
    // We received the categoryID and the Item ID
    Jobboard::newInstance()->insertJobsAttr(
            $itemID, 
            Params::getParam( 'positionType' ), 
            Params::getParam( 'salary' )
    ) ;

    $locales = osc_get_locales() ;

    $contract     = Params::getParam( 'contract' ) ;
    $studies      = Params::getParam( 'studies' ) ;
    $experience   = Params::getParam( 'experience' ) ;
    $requirements = Params::getParam( 'requirements' ) ;
    
    // insert locales
    foreach( $locales as $locale ) {
        $code = $locale['pk_c_code'] ;
        
        $s_contract = '' ;
        if( array_key_exists( $code, $contract ) ) {
            $s_contract = $contract[$code] ;
        }
        $s_studies = '' ;
        if( array_key_exists( $code, $studies ) ) {
            $s_studies = $studies[$code] ;
        }
        $s_experience = '' ;
        if( array_key_exists( $code, $experience ) ) {
            $s_experience = $experience[$code] ;
        }
        $s_requirements = '' ;
        if( array_key_exists( $code, $requirements ) ) {
            $s_requirements = $requirements[$code] ;
        }
        
        Jobboard::newInstance()->insertJobsAttrDescription(
                $itemID,
                $code,
                $s_contract,
                $s_studies,
                $s_experience,
                $s_requirements
        ) ;
    }
}

// TODO: item detail
function job_item_detail() {
    $detail = Jobboard::newInstance()->getJobsAttrByItemId( osc_item_id() ) ;
    
    if( count( $detail ) == 0 ) {
        return false ;
    }
    
    $desc = Jobboard::newInstance()->getJobsAttrDescriptionsByItemId( osc_item_id() ) ;

    $detail['locale'] = array() ;
    foreach( $desc as $d ) {
        $detail['locale'][$d['fk_c_locale_code']] = $d ;
    }

    require JOBBOARD_PATH . 'view/item_form.php';
}

function job_item_edit($catID = null, $itemID = null) {
    $detail = Jobboard::newInstance()->getJobsAttrByItemId( $itemID ) ;

    if( count( $detail ) == 0 ) {
        return false ;
    }

    $desc = Jobboard::newInstance()->getJobsAttrDescriptionsByItemId( $itemID ) ;

    $detail['locale'] = array() ;
    foreach( $desc as $d ) {
        $detail['locale'][$d['fk_c_locale_code']] = $d ;
    }

    require JOBBOARD_PATH . 'view/item_form.php';
    Session::newInstance()->_clearVariables();
}

// TODO: edit form post
function job_item_edit_post($catID = null, $itemID = null) {
    // We received the categoryID and the Item ID
    Jobboard::newInstance()->replaceJobsAttr(
            $itemID, 
            Params::getParam( 'positionType' ), 
            Params::getParam( 'salary' )
    ) ;
}

function job_delete_locale($locale) {
    Jobboard::newInstance()->deleteLocale( $locale ) ;
}

function job_delete_item($item_id) {
    Jobboard::newInstance()->deleteItem( $item_id ) ;
}

function jobs_admin_menu() {
    echo '<h3><a href="#">' . __( 'Job board', 'jobboard' ) . '</a></h3>
    <ul> 
        <li><a href="' . osc_admin_render_plugin_url('jobboard/conf.php') . '">&raquo; ' . __( 'Configure', 'jobboard' ) . '</a></li>
    </ul>' ;
}

function job_pre_item_post() {
    Session::newInstance()->_setForm( 'pj_salary', Params::getParam('salary') ) ;
    Session::newInstance()->_setForm( 'pj_positionType',  Params::getParam('positionType') ) ;
    Session::newInstance()->_setForm( 'pj_contract',  Params::getParam('contract') ) ;
    Session::newInstance()->_setForm( 'pj_studies',  Params::getParam('studies') ) ;
    Session::newInstance()->_setForm( 'pj_experience',  Params::getParam('experience') ) ; 
    Session::newInstance()->_setForm( 'pj_requirements',  Params::getParam('requirements') ) ;

    // keep values on session
    Session::newInstance()->_keepForm( 'pj_salary' ) ;
    Session::newInstance()->_keepForm( 'pj_positionType' ) ;
    Session::newInstance()->_keepForm( 'pj_contract' ) ;
    Session::newInstance()->_keepForm( 'pj_studies' ) ;
    Session::newInstance()->_keepForm( 'pj_experience' ) ;
    Session::newInstance()->_keepForm( 'pj_requirements' ) ;
}

function job_save_inputs_into_session() {
    Session::newInstance()->_keepForm( 'pj_salary' ) ;
    Session::newInstance()->_keepForm( 'pj_positionType' ) ;
    Session::newInstance()->_keepForm( 'pj_contract' ) ;
    Session::newInstance()->_keepForm( 'pj_studies' ) ;
    Session::newInstance()->_keepForm( 'pj_experience' ) ;
    Session::newInstance()->_keepForm( 'pj_requirements' ) ;
}

// This is needed in order to be able to activate the plugin
osc_register_plugin( osc_plugin_path( __FILE__ ), 'job_call_after_install' ) ;
// This is a hack to show a Configure link at plugins table (you could also use some other hook to show a custom option panel)
osc_add_hook( osc_plugin_path( __FILE__ ) . '_uninstall', 'job_call_after_uninstall' ) ;

// When publishing an item we show an extra form with more attributes
osc_add_hook( 'item_form', 'job_form' ) ;
// To add that new information to our custom table
osc_add_hook( 'item_form_post', 'job_form_post' ) ;

// When searching, display an extra form with our plugin's fields
osc_add_hook( 'search_form', 'job_search_form' ) ;
// When searching, add some conditions
osc_add_hook( 'search_conditions', 'job_search_conditions' ) ;

// Show an item special attributes
osc_add_hook( 'item_detail', 'job_item_detail' ) ;

// Edit an item special attributes
osc_add_hook( 'item_edit', 'job_item_edit' ) ;
// Edit an item special attributes POST
osc_add_hook( 'item_edit_post', 'job_item_edit_post' ) ;

//Delete locale
osc_add_hook( 'delete_locale', 'job_delete_locale' ) ;
//Delete item
osc_add_hook( 'delete_item', 'job_delete_item' ) ;

// Admin menu
osc_add_hook( 'admin_menu', 'jobs_admin_menu' ) ;

// previous to insert item
osc_add_hook( 'pre_item_post', 'job_pre_item_post' ) ;
osc_add_hook( 'pre_item_edit', 'job_pre_item_post' ) ;
// save input values into session
osc_add_hook( 'save_input_session', 'job_save_inputs_into_session' ) ;

?>