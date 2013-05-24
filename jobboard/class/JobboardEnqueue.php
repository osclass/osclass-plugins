<?php if ( !defined('ABS_PATH') ) exit('ABS_PATH is not loaded. Direct access is not allowed.');

/*
 * Print jobboard object with essential information and internatinalization
 * Load custom css and js 
 */
class JobboardEnqueue
{
    public function __construct() {
        osc_add_hook('init_admin',   array(&$this, 'admin_assets_jobboard') );
        osc_add_hook('admin_header', array(&$this, 'jobboard_init_js'), 1);
        osc_add_hook('header',       array(&$this, 'jobboard_init_js'), 1);
    }

    /*
     * Print jobboard object with essential information and internatinalization
     */
    function jobboard_init_js() {
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
        // killer questions related
        $langs['question']              = __('Question', 'jobboard');
        $langs['answer']                = __('Answer', 'jobboard');
        $langs['punctuation']           = __('Punctuation', 'jobboard');
        $langs['reject']                = __('Reject', 'jobboard');
        $langs['insertAnswersLink']     = __('Add answers', 'jobboard');
        $langs['removeAnswersLink']     = __('Remove answers', 'jobboard');
        $langs['title_msg_required']    = __('Title cannot be empty', 'jobboard');
        $langs['openquestion']          = __('Openquestion by default', 'jobboard');
        $langs['removeQuestionLink']    = __('Remove question', 'jobboard');
    ?>
    <script type="text/javascript">
        jobboard = {};
        jobboard.langs = <?php echo json_encode($langs); ?>;
        jobboard.max_killer_questions = '<?php echo osc_get_preference('max_killer_questions', 'jobboard_plugin'); ?>';
        jobboard.ajax_rating = '<?php echo osc_admin_ajax_hook_url('jobboard_rating'); ?>';
        jobboard.ajax_applicant_status_notification = '<?php echo osc_admin_ajax_hook_url('applicant_status_notification'); ?>';
        jobboard.ajax_applicant_status_message = '<?php echo osc_admin_ajax_hook_url('applicant_status_message'); ?>';
        jobboard.ajax_applicant_status = '<?php echo osc_admin_ajax_hook_url('applicant_status'); ?>';
        jobboard.ajax_note_add = '<?php echo osc_admin_ajax_hook_url('note_add'); ?>';
        jobboard.ajax_note_edit = '<?php echo osc_admin_ajax_hook_url('note_edit'); ?>';
        jobboard.ajax_note_delete = '<?php echo osc_admin_ajax_hook_url('note_delete'); ?>';
        jobboard.ajax_dismiss_tip = '<?php echo osc_admin_ajax_hook_url('dismiss_tip'); ?>';
        jobboard.ajax_question_delete = '<?php echo osc_admin_ajax_hook_url('question_delete'); ?>';
        jobboard.ajax_answer_punctuation = '<?php echo osc_admin_ajax_hook_url('jobboard_answer_punctuation'); ?>';
        jobbaord.tinymce.content_css = '<?php echo osc_plugin_url(__FILE__) . "css/tinymce.css" ?>';
    </script>
    <?php
    }

    /*
     * Load custom css and js
     */
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