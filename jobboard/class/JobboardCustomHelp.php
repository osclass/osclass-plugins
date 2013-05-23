<?php

class JobboardCustomTitles
{
    public function __construct()
    {
        osc_add_hook('init_admin',   array(&$this, 'help_jobboard_init'));
        osc_remove_hook('help_box', 'addHelp');
    }

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
                        osc_add_hook('help_box', array(&$this, 'help_add_vacancy'), 9);
                    break;
                    case(''):
                        osc_add_hook('help_box', array(&$this, 'help_list_vacancies'), 9);
                    break;
                }
            break;
            case('pages'):
                switch($action) {
                    case('add'):
                    case('edit'):
                        osc_add_hook('help_box', array(&$this, 'help_add_jobboard_page'), 9);
                    break;
                    case(''):
                        osc_add_hook('help_box', array(&$this, 'help_jobboard_pages'), 9);
                    break;
                }
            break;
            case('plugins'):
                switch(urldecode(Params::getParam('file'))) {
                    case('jobboard/people.php'):
                        osc_add_hook('help_box', array(&$this, 'help_list_applicants'), 9);
                    break;
                    case('jobboard/people_detail.php'):
                        osc_add_hook('help_box', array(&$this, 'help_detail_applicant'), 9);
                    break;
                }
            break;
            case('appearance'):
                switch(urldecode(Params::getParam('file'))) {
                    case('oc-content/themes/corporateboard/admin/settings.php'):
                        osc_add_hook('help_box', array(&$this, 'help_settings_jobboard'), 9);
                    break;
                    case('oc-content/themes/corporateboard/admin/colors.php'):
                        osc_add_hook('help_box', array(&$this, 'help_appearance_jobboard'), 9);
                    break;
                }
            break;
        }
    }
}

