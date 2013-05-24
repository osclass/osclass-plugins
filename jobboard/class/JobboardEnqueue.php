<?php if ( !defined('ABS_PATH') ) exit('ABS_PATH is not loaded. Direct access is not allowed.');

class JobboardEnqueue
{
    public function __construct() {
        osc_add_hook('init_admin', array(&$this, 'admin_assets_jobboard') );
    }

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
                osc_enqueue_script('tiny_mce');
                osc_enqueue_script('jobboard-init-tinymce');
                osc_enqueue_style('jquery-rating', osc_plugin_url(__FILE__) . 'js/rating/jquery.rating.css');
                osc_enqueue_style('jobboard-people-detail', osc_plugin_url(__FILE__) . 'css/people_detail.css');
            break;
            case('jobboard/people.php'):
                osc_enqueue_script('jquery-rating');
                osc_enqueue_script('jquery-metadata');
                osc_enqueue_script('jobboard-people');
                osc_enqueue_style('jquery-rating', osc_plugin_url(__FILE__) . 'js/rating/jquery.rating.css');
            break;
            case('jobboard/killer_form_frm.php'):
                osc_enqueue_script('jquery-validate');
                osc_enqueue_script('jobboard-killer-form');
            case('jobboard/manage_killer.php'):
                osc_enqueue_script('jobboard-manage-killer-form');
            break;
        }
        if(Params::getParam('page')=='items') {
            osc_enqueue_style('jobboard-flash-message', osc_plugin_url(__FILE__) . 'css/jobboard-flash-message.css', 6);
            osc_enqueue_script('jquery-metadata');
            osc_enqueue_script('jobboard-killer-form');
        }
    }
}