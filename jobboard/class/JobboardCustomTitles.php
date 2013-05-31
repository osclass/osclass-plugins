<?php if ( !defined('ABS_PATH') ) exit('ABS_PATH is not loaded. Direct access is not allowed.');
/**
 * Custom titles at - plugin pages -
 * Meta titles
 * Custom Header -
 */
class JobboardCustomTitles
{
    public function __construct()
    {
        // custom tile for plugin custom pages
        osc_add_filter('custom_plugin_title', array(&$this, 'jobboard_plugin_title'));

        // custom meta title at - ocadmin
        osc_add_filter('admin_title', array( &$this, 'jobboard_titles'), 9);

        // custom header
        if(Params::getParam('page') == 'items') {
            osc_add_hook('admin_header',   array(&$this, '_remove_title_header'));
            if(Params::getParam('action') == '') {
                osc_add_hook('admin_page_header', array(&$this, 'jobboard_customPageHeader_vacancies'));
            } else {
                osc_add_hook('admin_page_header', array(&$this, 'jobboard_customPageHeader_vacancies_post'));
            }
        }

        // custom header
        if(Params::getParam('page') == 'plugins' && Params::getParam('file') == 'jobboard/admin/settings.php') {
            osc_add_hook('admin_header',      array(&$this, '_remove_title_header'));
            osc_add_hook('admin_page_header', array(&$this, 'jobboard_customPageHeader_settings'));
        }

    }

    /**
     * Custom header for vacancies page - manage listings
     */
    function jobboard_customPageHeader_vacancies() { ?>
        <h1><?php _e('Vacancies', 'jobboard'); ?>
            <a href="#" class="btn ico ico-32 ico-help float-right"></a>
            <a href="<?php echo osc_admin_base_url(true) . '?page=items&action=post' ; ?>" class="btn btn-green ico ico-32 ico-add-white float-right"><?php _e('Add vacancy', 'jobboard'); ?></a>
        </h1>
    <?php
    }

    /**
     * Custom header for admin settings
     */
    function jobboard_customPageHeader_settings() { ?>
        <h1><?php _e('Default locations', 'jobboard'); ?>
            <a href="#" class="btn ico ico-32 ico-help float-right"></a>
        </h1>
    <?php
    }

    /**
     * Custom header for add/edit
     */
    function jobboard_customPageHeader_vacancies_post() { ?>
        <h1><?php _e('Vacancies', 'jobboard'); ?>
            <a href="#" class="btn ico ico-32 ico-help float-right"></a>
        </h1>
    <?php
    }

    function _remove_title_header() {
        osc_remove_hook('admin_page_header','customPageHeader');
    }

    /**
     * Modify title page showed at oc-admin pages
     *
     * @param type $string
     * @return string
     */
    function jobboard_plugin_title($string)
    {
        if(Params::getParam('page') == 'plugins') {
            if(Params::getParam('file') == 'jobboard/people_detail.php') {
                $string = __('Applicant', 'jobboard') . '<a href="#" class="btn ico ico-32 ico-help float-right"></a>';
            }
            if(Params::getParam('file') == 'jobboard/dashboard.php') {
                $string = __('Dashboard', 'jobboard');
            }
            if(Params::getParam('file') == 'jobboard/resume_download.php') {
                $string = __('Download resumes', 'jobboard');
            }
            if(Params::getParam('file') == 'jobboard/people.php') {
                $string = __('Applicants', 'jobboard') . '<a href="#" class="btn ico ico-32 ico-help float-right"></a>';
            }
        }
        return $string;
    }

    /**
     * Meta title
     *
     * @param type $title
     * @return type
     */
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
                } else if($file=='jobboard/resume_download.php') {
                    $title = preg_replace('|^(.*)&raquo;|', __('Download resumes','jobboard').' &raquo;', $title);
                }
                break;
            default:
                break;
        }
        return $title;
    }

}