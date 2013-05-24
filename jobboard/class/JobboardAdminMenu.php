<?php if ( !defined('ABS_PATH') ) exit('ABS_PATH is not loaded. Direct access is not allowed.');

class JobboardAdminMenu
{
    public function __construct() {
        $this->init();
    }

    /**
     * Add new menu entries
     */
    public function init() {
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

        // killer questions menu
        osc_add_admin_submenu_page(
            'jobboard',
            __('Killer Questions', 'jobboard'),
            osc_admin_render_plugin_url("jobboard/manage_killer.php"),
            'jobboard_killer',
            'moderator'
        );

        osc_add_admin_submenu_page(
             'jobboard',
             __('Download resumes', 'jobboard'),
             osc_admin_render_plugin_url("jobboard/resume_download.php"),
             'jobboard_resumedownload',
             'moderator'
        );

        osc_add_admin_submenu_page(
            'jobboard',
            __('Default locations', 'jobboard'),
            osc_admin_render_plugin_url("jobboard/admin/settings.php"),
            'jobboard_locations',
            'moderator'
        );
    }
}