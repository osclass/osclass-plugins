<?php if ( !defined('ABS_PATH') ) exit('ABS_PATH is not loaded. Direct access is not allowed.');

/*
 * Print jobboard object with essential information and internatinalization
 * Load custom css and js
 */
class JobboardEnqueue
{
    public function __construct()
    {
        osc_add_hook('init_admin',   array(&$this, 'admin_assets_jobboard') );
        osc_add_hook('admin_header', array(&$this, 'jobboard_init_js'), 0);
        osc_add_hook('header',       array(&$this, 'jobboard_init_js'), 0);
        osc_add_hook('init',         array(&$this, 'jobboard_css_front') );
    }

    function jobboard_css_front()
    {
	osc_enqueue_style('jobboard-css', osc_plugin_url(dirname(__FILE__)) . 'assets/css/styles.css');
    }

    /*
     * Print jobboard object with essential information and internatinalization
     */
    function jobboard_init_js()
    {
?>
    <script type="text/javascript">
        jobboard = {};
	jobboard.langs = <?php echo json_encode($this->langs()); ?>;
	jobboard.ajax = <?php echo json_encode($this->ajax()); ?>;
	jobboard.dashboard = <?php echo json_encode($this->dashboard_settings()); ?>;
        jobboard.max_killer_questions = '<?php echo osc_get_preference('max_killer_questions', 'jobboard_plugin'); ?>';
	jobboard.tinymce_content_css = '<?php echo osc_plugin_url(dirname(__FILE__))  . "assets/css/styles.css" ?>';
    </script>
<?php
    }

    /*
     * Load custom css and js
     */
    function admin_assets_jobboard()
    {
	osc_enqueue_style('jobboard-css', osc_plugin_url(dirname(__FILE__)) . 'assets/css/styles.css');
        switch(urldecode(Params::getParam('file'))) {
            case('jobboard/dashboard.php'):
		osc_enqueue_style('hopscotch-css', osc_plugin_url(dirname(__FILE__))  . "assets/lib/hopscotch/css/hopscotch.css");

                osc_enqueue_script('jquery-rating');
                osc_enqueue_script('jobboard-people');
                osc_enqueue_script('jobboard-dashboard');
		osc_enqueue_script('hopscotch');
            break;
            case('jobboard/people_detail.php'):
                osc_enqueue_script('jquery-rating');
                osc_enqueue_script('jquery-metadata');
                osc_enqueue_script('jobboard-people-detail');
                osc_enqueue_script('tiny_mce');
                osc_enqueue_script('jobboard-init-tinymce');
		osc_enqueue_style('jquery-rating', osc_plugin_url(dirname(__FILE__)) . 'assets/lib/rating/jquery.rating.css');
            break;
            case('jobboard/people.php'):
                osc_enqueue_script('jquery-rating');
                osc_enqueue_script('jquery-metadata');
                osc_enqueue_script('jobboard-people');
		osc_enqueue_style('jquery-rating', osc_plugin_url(dirname(__FILE__)) . 'assets/lib/rating/jquery.rating.css');
            break;
            case('jobboard/killer_form_frm.php'):
                osc_enqueue_script('jquery-validate');
                osc_enqueue_script('jobboard-killer-form');
            case('jobboard/manage_killer.php'):
                osc_enqueue_script('jobboard-manage-killer-form');
            break;
        }
        if(Params::getParam('page')=='items') {
            osc_enqueue_script('jquery-metadata');
            osc_enqueue_script('jobboard-killer-form');
            if(Params::getParam('action')=='post') {
                osc_enqueue_script('jobboard-item-add');
            }
        }
        if(Params::getParam('page')=='admins') {
            if(Params::getParam('action')=='add' || Params::getParam('action')=='edit') {
                osc_enqueue_script('jobboard-admin-page');
            }
        }
    }

    private function langs()
    {
	$langs = array();
	$langs['delete_string']         = __('Delete', 'jobboard');
	$langs['edit_string']           = __('Edit', 'jobboard');
	$langs['text_hide_filter']      = __('Hide search', 'jobboard');
	$langs['text_show_filter']      = __('Show search', 'jobboard');
	$langs['empty_note_text']       = __('No notes have been added to this applicant', 'jobboard');
	$langs['sex_required']          = __('Sex: this field is required', 'jobboard');
	$langs['birthday_required']     = __('Birthday: this field is required', 'jobboard');
	$langs['invalid_birthday_date'] = __('Invalid birthday date', 'jobboard');
	$langs['complete_form_please']  = __('Complete this form', 'jobboard');
	$langs['admins_help_inline']    = __('Administrators have total control over all aspects of your jobboard, while moderators are only allowed to view job offers and manage applicants', 'jobboard');
	// killer questions related
	$langs['question']              = __('Question', 'jobboard');
	$langs['answer']                = __('Answer', 'jobboard');
	$langs['punctuation']           = __('Punctuation', 'jobboard');
	$langs['reject']                = __('Reject', 'jobboard');
	$langs['insertAnswersLink']     = __('Add answers', 'jobboard');
	$langs['removeAnswersLink']     = __('Remove answers', 'jobboard');
	$langs['cleanAnswerLink']       = __('Remove this answer', 'jobboard');
	$langs['title_msg_required']    = __('Title cannot be empty', 'jobboard');
	$langs['openquestion']          = __('Open question by default', 'jobboard');
	$langs['removeQuestionLink']    = __('Remove question', 'jobboard');
	// hopscotch
	$langs['hopscotch']['i18n'] = array(
	    'nextBtn'      => __('Next', 'jobboard'),
	    'prevBtn'      => __('Back', 'jobboard'),
	    'doneBtn'      => __('Done', 'jobboard'),
	    'skipBtn'      => __('Skip', 'Skip'),
	    'closeTooltip' => __('Close', 'Skip')
	);
	$langs['hopscotch']['dashboard'] = array(
	    'step1' => array(
		'title'   => __('Welcome to Osclass.com', 'jobboard'),
		'content' => __('Here you can publish your first job offer.', 'jobboard')
	    ),
	    'step2' => array(
		'title'   => __('Manage jobs', 'jobboard'),
		'content' => __('You can manage your different job offers from here.', 'jobboard')
	    ),
	    'step3' => array(
		'title'   => __('Applicants', 'jobboard'),
		'content' => __('You can see the candidates\' profiles here - you can filter them by a job offer they\'ve applied for.', 'jobboard')
	    ),
	    'step4' => array(
		'title'   => __('Settings', 'jobboard'),
		'content' => __('You can set your Google Analytics ID, company name and other settings from here.', 'jobboard')
	    ),
	    'step5' => array(
		'title'   => __('Appearance', 'jobboard'),
		'content' => __('You can customize the colours of your website to better reflect your company\'s image.', 'jobboard')
	    ),
	    'step6' => array(
		'title'   => __('That\'s all for now!', 'jobboard'),
		'content' => __('You can repeat the tour anytime by clicking the link below.', 'jobboard')
	    )
	);

	return $langs;
    }

    private function dashboard_settings()
    {
	$dashboard = array();
	$dashboard['tour'] = array(
	    'visible'    => (osc_get_preference('dashboard_tour_visible', 'jobboard_plugin') === '0') ? false : true,
	    'times_seen' => (osc_get_preference('dashboard_tour_times_seen', 'jobboard_plugin') === '') ? 0 : (int) osc_get_preference('dashboard_tour_times_seen', 'jobboard_plugin')
	);

	return $dashboard;
    }

    private function ajax()
    {
	$ajax = array(
	    'rating' => osc_admin_ajax_hook_url('jobboard_rating'),
	    'applicant_status_notification' => osc_admin_ajax_hook_url('applicant_status_notification'),
	    'applicant_status_message' => osc_admin_ajax_hook_url('applicant_status_message'),
	    'applicant_status' => osc_admin_ajax_hook_url('applicant_status'),
	    'note_add' => osc_admin_ajax_hook_url('note_add'),
	    'note_edit' => osc_admin_ajax_hook_url('note_edit'),
	    'note_delete' => osc_admin_ajax_hook_url('note_delete'),
	    'dismiss_tip' => osc_admin_ajax_hook_url('dismiss_tip'),
	    'question_delete' => osc_admin_ajax_hook_url('question_delete'),
	    'answer_punctuation' => osc_admin_ajax_hook_url('jobboard_answer_punctuation'),
	    'dashboard_tour' => osc_admin_ajax_hook_url('dashboard_tour')
	);

	return $ajax;
    }
}