<?php
/*
Plugin Name: Job Board
Plugin URI: http://www.osclass.org/
Description: Job Board
Version: 1.4.3
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: jobboard_plugin
Plugin update URI: job-board
*/

define('JOBBOARD_PATH', dirname(__FILE__) . '/') ;
require_once(JOBBOARD_PATH . 'model/ModelJB.php');
require_once(JOBBOARD_PATH . 'model/ModelKQ.php');
require_once(JOBBOARD_PATH . 'model/ModelLogJB.php');
require_once(JOBBOARD_PATH . 'helpers.php');
require_once(JOBBOARD_PATH . 'class/Stream.class.php');
require_once(JOBBOARD_PATH . 'class/JobboardInstallUpdate.php');
require_once(JOBBOARD_PATH . 'class/JobboardContact.php');
require_once(JOBBOARD_PATH . 'class/JobboardListingActions.php');
require_once(JOBBOARD_PATH . 'class/JobboardEnqueue.php');

// init classes
$jb_contact         = new JobboardContact();
$jb_listing_actions = new JobboardListingActions();
$jb_enqueue         = new JobboardEnqueue();

if( OC_ADMIN ) {
    require_once(JOBBOARD_PATH . 'class/JobboardNotices.class.php');
    require_once(JOBBOARD_PATH . 'class/ShareJobOffer.class.php');
    require_once(JOBBOARD_PATH . 'class/JobboardManageListings.php');
    require_once(JOBBOARD_PATH . 'class/JobboardManageApplicants.php');
    require_once(JOBBOARD_PATH . 'class/JobboardAdminMenu.php');
    require_once(JOBBOARD_PATH . 'class/JobboardCustomTitles.php');
    require_once(JOBBOARD_PATH . 'class/JobboardCustomHelp.php');
    require_once(JOBBOARD_PATH . 'class/JobboardFooter.class.php');

    // init oc-admin classes
    $jb_manage_listings = new JobboardManageListings();
    $jb_admin_menu      = new JobboardAdminMenu();
    $jb_custom_title    = new JobboardCustomTitles();
    $jb_help            = new JobboardCustomHelp();
}

/*
 * Install - Uninstall - Update plugin  ----------------------------------------
 */
osc_register_plugin(osc_plugin_path(__FILE__), array( new JobboardInstallUpdate(), 'job_call_after_install') );
osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", array( new JobboardInstallUpdate(), 'job_call_after_uninstall') );
osc_add_hook('init', array(new JobboardInstallUpdate(), 'jobboard_update_version') );
/* -- install - uninstall - update hooks -- */

/*
 * Register js and css scripts  ------------------------------------------------
 */
osc_register_script('jquery-rating', osc_plugin_url(__FILE__) . 'assets/lib/rating/jquery.rating.js', 'jquery');
osc_register_script('jquery-metadata', osc_plugin_url(__FILE__) . 'assets/lib/rating/jquery.MetaData.js', 'jquery');
osc_register_script('jobboard-people', osc_plugin_url(__FILE__) . 'assets/js/people.js', 'jquery');
osc_register_script('jobboard-killer-form', osc_plugin_url(__FILE__) . 'assets/js/killerForm.js', 'jquery');
osc_register_script('jobboard-manage-killer-form', osc_plugin_url(__FILE__) . 'assets/js/manageKillerForm.js', 'jquery');
osc_register_script('jobboard-people-detail', osc_plugin_url(__FILE__) . 'assets/js/people_detail.js', 'jquery');
osc_register_script('jobboard-dashboard', osc_plugin_url(__FILE__) . 'assets/js/dashboard.js', array('jquery', 'hopscotch'));
osc_register_script('jobboard-apply-linkedin', osc_plugin_url(__FILE__) . 'assets/js/bridgeApplyLinkedin.js', array('jquery'));
osc_register_script('jobboard-show-flashmessage', osc_plugin_url(__FILE__) . 'assets/js/jobboardShowFlashmessage.js', array('jquery'));
osc_register_script('jobboard-init-tinymce', osc_plugin_url(__FILE__) . 'assets/js/init_tinymce.js', array('jquery', 'tiny_mce'));
osc_register_script('jobboard-item-add', osc_plugin_url(__FILE__) . 'assets/js/item_add.js', array('jquery'));
osc_register_script('jobboard-admin-page', osc_plugin_url(__FILE__) . 'assets/js/jb_admin.js', 'jquery');
osc_register_script('hopscotch', osc_plugin_url(__FILE__) . 'assets/lib/hopscotch/js/hopscotch-0.1.min.js', array('jquery'));

/* -- Register js and css scripts -- */

/*
 * ajax binding
 */
require_once(JOBBOARD_PATH . 'class/JobboardAjax.php');

/*
 * NO SE EJECUTA !
 */
function jobboard_admin_admin_item_select() {
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
osc_add_hook('admin_header','jobboard_admin_item_select');
// ------------------------------

/**
 * Unread applicants,
 * displatLength on manage applicants
 *
 *
 * @todo to be improved
 */
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
        break;
    }
}
osc_add_hook('init_admin', 'jobboard_post_actions');
// -----------------------------------------------------------------------------

/**
 * Apply with linkedin - document.domain
 *
 * ONLY UNDER SUBDOMAIN.osclass.com
 */
// get subdomain - linkedin related - osclass.com/apply/
$url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$parsedUrl = parse_url($url);
$host = explode('.', $parsedUrl['host']);
$a2 = array_pop($host);
$a1 = array_pop($host);
$subdomain = $a1.".".$a2;

if( $subdomain == 'osclass.com') {
    osc_add_hook('init', 'jobboard_set_domain_linkedin');
}
function jobboard_set_domain_linkedin() {
    osc_enqueue_script('jobboard-apply-linkedin');
}
// -----------------------------------------------------------------------------
/*
 * Corporateboard hook theme
 */
function applicant_admin_menu_current($class) {
    if( urldecode(Params::getParam('file')) === 'jobboard/people_detail.php' ) {
        return 'current';
    }
    return $class;
}
osc_add_filter('current_admin_menu_corporateboard', 'applicant_admin_menu_current');
// End of file