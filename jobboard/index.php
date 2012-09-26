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

define('JOBBOARD_PATH', dirname(__FILE__) . '/') ;
require_once(JOBBOARD_PATH . 'ModelJB.php');
require_once(JOBBOARD_PATH . 'helpers.php');

function job_call_after_install() {
    ModelJB::newInstance()->import('jobboard/struct.sql');

    osc_set_preference('upload_path', osc_content_path() . "uploads/", 'jobboard_plugin', 'STRING');
    osc_set_preference('version', 100, 'jobboard_plugin', 'INTEGER');
}

function job_call_after_uninstall() {
    ModelJB::newInstance()->uninstall();

    osc_delete_preference('upload_path', 'jobboard_plugin');
    osc_delete_preference('version', 'jobboard_plugin');
}

function ajax_rating_request() {
    ModelJB::newInstance()->setRating(Params::getParam("applicantId"), Params::getParam("rating"));
}
osc_add_hook('ajax_admin_jobboard_rating', 'ajax_rating_request');

function ajax_applicant_status() {
    ModelJB::newInstance()->changeStatus(Params::getParam("applicantId"), Params::getParam("status"));
}
osc_add_hook('ajax_admin_applicant_status', 'ajax_applicant_status');

function ajax_applicant_status_notification() {
    $applicantID = Params::getParam('applicantId');
    $status      = Params::getParam('status');

    require_once(JOBBOARD_PATH . 'email.php');
    send_email_notification_applicant($status, $applicantID);
}
osc_add_hook('ajax_admin_applicant_status_notifitacion', 'ajax_applicant_status_notification');

/* FORM JOB BOARD */
function jobboard_form($catID = null) {
    $detail = array(
        'e_position_type' => '',
        's_salary_text'   => '',
        'locale'          => array()
    );
    foreach(osc_get_locales() as $locale) {
        $detail['locale'][$locale['pk_c_code']] = array(
            's_desired_exp'                 => '',
            's_studies'                     => '',
            's_minimum_requirements'        => '',
            's_desired_requirements'        => '',
            's_contract'                    => ''
        );
    }
    // session variables
    $detail = get_jobboard_session_variables($detail);

    require_once(JOBBOARD_PATH . 'item_edit.php');
    Session::newInstance()->_clearVariables();
}
osc_add_hook('item_form', 'jobboard_form');

function jobboard_form_post($catID = null, $itemID = null)  {
    ModelJB::newInstance()->insertJobsAttr($itemID, Params::getParam('relation'), Params::getParam('positionType'), Params::getParam('salaryText') );

    // prepare locales
    $dataItem = array();
    foreach(Params::getParam('min_reqs') as $k => $v) {
        $dataItem[$k]['min_reqs'] = $v;
    }
    foreach(Params::getParam('desired_reqs') as $k => $v) {
        $dataItem[$k]['desired_reqs'] = $v;
    }
    foreach(Params::getParam('desired_exp') as $k => $v) {
        $dataItem[$k]['desired_exp'] = $v;
    }
    foreach(Params::getParam('studies') as $k => $v) {
        $dataItem[$k]['studies'] = $v;
    }
    foreach(Params::getParam('contract') as $k => $v) {
        $dataItem[$k]['contract'] = $v;
    }

    // insert locales
    foreach ($dataItem as $k => $data) {
        ModelJB::newInstance()->insertJobsAttrDescription($itemID, $k, $data['desired_exp'], $data['studies'], $data['min_reqs'], $data['desired_reqs'], $data['contract']);
    }
}
osc_add_hook('item_form_post', 'jobboard_form_post');

function jobboard_item_edit($catID = null, $itemID = null) {
    $detail       = ModelJB::newInstance()->getJobsAttrByItemId($itemID);
    $descriptions = ModelJB::newInstance()->getJobsAttrDescriptionsByItemId($itemID);
    $detail['locale'] = array();
    foreach ($descriptions as $desc) {
        $detail['locale'][$desc['fk_c_locale_code']] = $desc;
    }

    // session variables
    $detail = get_jobboard_session_variables($detail);

    require_once(JOBBOARD_PATH . 'item_edit.php');
    Session::newInstance()->_clearVariables();
}
osc_add_hook('item_edit', 'jobboard_item_edit');

function jobboard_item_edit_post($catID = null, $itemID = null) {
    ModelJB::newInstance()->replaceJobsAttr($itemID, Params::getParam('relation'), Params::getParam('positionType'), Params::getParam('salaryText'));

    // prepare locales
    $dataItem = array();
    foreach(Params::getParam('min_reqs') as $k => $v) {
        $dataItem[$k]['min_reqs'] = $v;
    }
    foreach(Params::getParam('desired_reqs') as $k => $v) {
        $dataItem[$k]['desired_reqs'] = $v;
    }
    foreach(Params::getParam('desired_exp') as $k => $v) {
        $dataItem[$k]['desired_exp'] = $v;
    }
    foreach(Params::getParam('studies') as $k => $v) {
        $dataItem[$k]['studies'] = $v;
    }
    foreach(Params::getParam('contract') as $k => $v) {
        $dataItem[$k]['contract'] = $v;
    }

    // insert locales
    foreach ($dataItem as $k => $data) {
        ModelJB::newInstance()->replaceJobsAttrDescriptions($itemID, $k, $data['desired_exp'], $data['studies'], $data['min_reqs'], $data['desired_reqs'], $data['contract']);
    }
}
osc_add_hook('item_edit_post', 'jobboard_item_edit_post');

function get_jobboard_session_variables($detail) {
    if( Session::newInstance()->_getForm('pj_positionType') != '' ) {
        $detail['e_position_type'] = Session::newInstance()->_getForm('pj_positionType');
    }
    if( Session::newInstance()->_getForm('pj_salaryText') != '' ) {
        $detail['s_salary_text'] = Session::newInstance()->_getForm('pj_salaryText');
    }
    if( Session::newInstance()->_getForm('pj_data') != '' ) {
        foreach(osc_get_locales() as $locale) {
            $data = Session::newInstance()->_getForm('pj_data');
            $detail['locale'][$locale['pk_c_code']]['s_desired_exp']          = @$data[$locale['pk_c_code']]['desired_exp'];
            $detail['locale'][$locale['pk_c_code']]['s_studies']              = @$data[$locale['pk_c_code']]['studies'];
            $detail['locale'][$locale['pk_c_code']]['s_minimum_requirements'] = @$data[$locale['pk_c_code']]['min_reqs'];
            $detail['locale'][$locale['pk_c_code']]['s_desired_requirements'] = @$data[$locale['pk_c_code']]['desired_reqs'];
            $detail['locale'][$locale['pk_c_code']]['s_contract']             = @$data[$locale['pk_c_code']]['contract'];
        }
    }

    return $detail;
}
/* /FORM JOB BOARD */

function get_jobboard_position_types() {
    $position_types = array(
        'UNDEF' => __('Undefined', 'jobboard'),
        'PART'  => __('Part time', 'jobboard'),
        'FULL'  => __('Full time', 'jobboard')
    );
    return $position_types;
}

function job_item_detail() {
    $detail = ModelJB::newInstance()->getJobsAttrByItemId(osc_item_id());
    $descriptions = ModelJB::newInstance()->getJobsAttrDescriptionsByItemId(osc_item_id());
    $detail['locale'] = array();
    foreach ($descriptions as $desc) {
        $detail['locale'][$desc['fk_c_locale_code']] = $desc;
    }
    require_once(JOBBOARD_PATH . 'item_detail.php');
}

function job_linkedin() {
    require_once(JOBBOARD_PATH . 'linkedinApply.php');
}

/* CONTACT */
function jobboard_save_contact_listing() {
    jobboard_common_contact(osc_item_id(), osc_item_url());
    require_once(JOBBOARD_PATH . 'email.php');
    send_email_to_applicant('listing');
    send_notifaction_applicant_to_admin('listing');
}

osc_add_hook('post_item_contact_post', 'jobboard_save_contact_listing', 1);
osc_remove_hook('hook_email_item_inquiry', 'fn_email_item_inquiry');

function jobboard_save_contact($params) {
    jobboard_common_contact(null, osc_contact_url(), @$params['attachment']);
    require_once(JOBBOARD_PATH . 'email.php');
    send_email_to_applicant('spontaneous');
    send_notifaction_applicant_to_admin('spontaneous');
    osc_add_flash_ok_message(__('Thanks for sending us your CV', 'jobboard'));
    header('Location: ' . osc_contact_url()); exit;
}
osc_add_hook('pre_contact_post', 'jobboard_save_contact');

function jobboard_common_contact($itemID, $url, $uploadCV = '') {
    $error_attachment = false;

    $name   = Params::getParam('yourName');
    $email  = Params::getParam('yourEmail');
    $cover  = Params::getParam('message');
    $phone  = Params::getParam('phoneNumber');
    $aCV    = Params::getFiles('attachment');

    // check fields
    if( $name === '' ) {
        osc_add_flash_error_message(__("Name is required", 'jobboard'));
        _save_jobboard_contact_listing();
        header('Location: ' . $url); die;
    }
    if( $email === '' ) {
        osc_add_flash_error_message(__("Email is required", 'jobboard'));
        _save_jobboard_contact_listing();
        header('Location: ' . $url); die;
    }
    if( $cover === '' ) {
        osc_add_flash_error_message(__("Cover is required", 'jobboard'));
        _save_jobboard_contact_listing();
        header('Location: ' . $url); die;
    }
    if( isset($aCV['name']) && $aCV['name'] === '' ) {
        osc_add_flash_error_message(__("CV is required", 'jobboard'));
        _save_jobboard_contact_listing();
        header('Location: ' . $url); die;
    }

    require osc_lib_path() . 'osclass/mimes.php';
    // get allowedExt
    $aMimesAllowed = array();
    $aExt = array('pdf', 'rtf', 'doc', 'docx', 'odt');
    foreach($aExt as $ext){
        if(isset($mimes[$ext])) {
            $mime = $mimes[$ext];
            if( is_array($mime) ){
                foreach($mime as $aux){
                    if( !in_array($aux, $aMimesAllowed) ) {
                        array_push($aMimesAllowed, $aux);
                    }
                }
            } else {
                if( !in_array($mime, $aMimesAllowed) ) {
                    array_push($aMimesAllowed, $mime);
                }
            }
        }
    }

    if( $aCV['error'] == UPLOAD_ERR_OK ) {
        if( !in_array($aCV['type'], $aMimesAllowed) ) {
            osc_add_flash_error_message(__("The file you tried to upload does not have a valid extension", 'jobboard'));
            _save_jobboard_contact_listing();
            header('Location: ' . $url); die;
        }
    }

    // insert to database
    $mJB = ModelJB::newInstance();

    $applicantID = $mJB->insertApplicant($itemID, $name, $email, $cover, $phone);
    View::newInstance()->_exportVariableToView('applicantID', $applicantID);
    // return to listing url
    if( !$applicantID ) {
        osc_add_flash_error_message(__("There were some problem processing your application, please try again", 'jobboard'));
        header('Location: ' . $url); die;
    }

    if($uploadCV=='') {
        if(isset($aCV['name']) && $aCV['error'] == UPLOAD_ERR_OK) {
            $tmp_name = $aCV['tmp_name'];
            $fileName = date('YmdHis') . '_' . $aCV['name'];
            if( move_uploaded_file($tmp_name, osc_get_preference('upload_path', 'jobboard_plugin') . $fileName) ) {
                $mJB->insertFile($applicantID, $fileName);
            } else {
                $error_attachment = true;
            }
        } else {
            $error_attachment = true;
        }
    } else {
        $fileName = date('YmdHis') . '_' . $aCV['name'];
        if( copy($uploadCV, osc_get_preference('upload_path', 'jobboard_plugin') . $fileName) ) {
            @unlink($uploadCV);
            $mJB->insertFile($applicantID, $fileName);
        } else {
            $error_attachment = true;
        }
    }

    if( $error_attachment ) {
        ModelJB::newInstance()->deleteApplicant($applicantID);
        osc_add_flash_error_message(__("There were some problem processing your application, please try again", 'jobboard'));
        header('Location: ' . $url); die;
    }

    return true;
}

function _save_jobboard_contact_listing() {
    Session::newInstance()->_setForm('yourEmail',    Params::getParam('yourEmail'));
    Session::newInstance()->_setForm('yourName',     Params::getParam('yourName'));
    Session::newInstance()->_setForm('phoneNumber',  Params::getParam('phoneNumber'));
    Session::newInstance()->_setForm('message_body', Params::getParam('message'));
}
/* /CONTACT */

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
osc_add_hook('admin_header','jobboard_admin_menu');

function jobboard_duplicate_job() {
    if(Params::getParam('page')=='items' && Params::getParam('action')=='post') {
        $id = Params::getParam('duplicatefrom') ;
        if($id!='') {
            $item = Item::newInstance()->findByPrimaryKey($id);

            View::newInstance()->_exportVariableToView("item", $item);
            View::newInstance()->_exportVariableToView("new_item", TRUE);
            View::newInstance()->_exportVariableToView("actions", array());

            $detail       = ModelJB::newInstance()->getJobsAttrByItemId($id);
            $descriptions = ModelJB::newInstance()->getJobsAttrDescriptionsByItemId($id);

            Session::newInstance()->_setForm('pj_positionType',  @$detail['e_position_type'] );
            Session::newInstance()->_setForm('pj_salaryText', @$detail['s_salary_text'] );

            $dataItem = array();
            foreach ($descriptions as $v) {
                $dataItem[$v['fk_c_locale_code']] = array();
                $dataItem[$v['fk_c_locale_code']]['contract'] = $v['s_contract'];
                $dataItem[$v['fk_c_locale_code']]['studies'] = $v['s_studies'];
                $dataItem[$v['fk_c_locale_code']]['desired_exp'] = $v['s_desired_exp'];
                $dataItem[$v['fk_c_locale_code']]['min_reqs'] = $v['s_minimum_requirements'];
                $dataItem[$v['fk_c_locale_code']]['desired_reqs'] = $v['s_desired_requirements'];
            }
            Session::newInstance()->_setForm('pj_data', $dataItem );

            Session::newInstance()->_keepForm('pj_positionType');
            Session::newInstance()->_keepForm('pj_salaryText');
            Session::newInstance()->_keepForm('pj_data');

            osc_current_admin_theme_path('items/frm.php') ;
            Session::newInstance()->_clearVariables();
            osc_run_hook('after_admin_html');
            exit;
        }
    };
}
osc_add_hook('before_admin_html', 'jobboard_duplicate_job');

function jobboard_more_options($options, $aRow) {
    return array();
}
osc_add_filter('more_actions_manage_items', 'jobboard_more_options');

function jobboard_manage_actions($options, $aRow) {
    if($aRow['b_enabled']) {
        $options[] = '<a href="' . osc_admin_base_url(true) . '?page=items&amp;action=status&amp;id=' . $aRow['pk_i_id'] . '&amp;value=DISABLE">' . __('Block', 'jobboard') .'</a>' ;
    } else {
        $options[] = '<a href="' . osc_admin_base_url(true) . '?page=items&amp;action=status&amp;id=' . $aRow['pk_i_id'] . '&amp;value=ENABLE">' . __('Unblock', 'jobboard') .'</a>' ;
    }
    $options[] = '<a href="' . osc_admin_base_url(true) . '?page=items&amp;action=post&amp;duplicatefrom=' . $aRow['pk_i_id'] . '">' . __('Duplicate', 'jobboard') . '</a>' ;
    return $options;
}
osc_add_filter('actions_manage_items', 'jobboard_manage_actions');

//Custom title
osc_add_filter('custom_plugin_title','jobboard_people_title');
function jobboard_people_title($string){
    if(Params::getParam('page') == 'plugins' && Params::getParam('file') == 'jobboard/people_detail.php'){
        $string = __('Applicant', 'jobboard') . '<a href="#" class="btn ico ico-32 ico-help float-right"></a>';
    }
    return $string;
}

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

function jobboard_rating($applicantId, $rating = 0) {
    $str = '<span class="rating" id="rating_'.$applicantId.'" rating="'.$rating.'">';
    for($k=1;$k<=5;$k++) {
        $str .= '<a href="#" class="star" star="'.$k.'" id="rating_'.$applicantId.'_'.$k.'" ><img src="'.osc_base_url().'oc-content/plugins/'.osc_plugin_folder(__FILE__).'img/'.($k<=$rating?'fullstar.png':'emptystar.png').'"/></a>';
    }
    $str .= '</span>';
    return $str;
}

function jobboard_status() {
    $status_array = array();
    $status_array[0] = __("Active", "jobboard");
    $status_array[1] = __("Interview", "jobboard");
    $status_array[2] = __("Rejected", "jobboard");
    $status_array[3] = __("Hired", "jobboard");
    return $status_array;
}

//Title, Location, Created, Modified, Number of Candidates, Views
function job_items_table_header($table) {
    $table->addColumn("mod_date", __("Modified", "jobboard"));
    $table->addColumn("applicants", __("# of applicants", "jobboard"));
    $table->addColumn("views", __("Views", "jobboard"));
    $table->removeColumn("user");
    $table->removeColumn("category");
}

function job_items_row($row, $aRow) {

    list($applicants, $total) = ModelJB::newInstance()->searchCount(array('item' => $aRow['pk_i_id']));

    $row['mod_date'] = @$aRow['dt_mod_date'];
    $row['applicants'] = '<a href="' . osc_admin_render_plugin_url("jobboard/people.php&jobId=") . $aRow['pk_i_id'] . '">' . sprintf(__('%d applicants', 'jobboard'), $applicants) . '</a>';
    $row['views'] = @$aRow['i_num_views'];
    return $row;
}

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

function job_pre_item_post() {
    Session::newInstance()->_setForm('pj_positionType',  Params::getParam('positionType') );
    Session::newInstance()->_setForm('pj_salaryText', Params::getParam('salaryText') );
    // prepare locales
    $dataItem = array();
    $request = Params::getParamsAsArray();
    foreach ($request as $k => $v) {
        if(is_array($v)) {
            foreach($v as $locale => $value) {
                $dataItem[$locale][$k] = $value;
            }
        }
    }
    Session::newInstance()->_setForm('pj_data', $dataItem );

    // keep values on session
    Session::newInstance()->_keepForm('pj_positionType');
    Session::newInstance()->_keepForm('pj_salaryText');
    Session::newInstance()->_keepForm('pj_data');
}

function job_save_inputs_into_session() {
    Session::newInstance()->_keepForm('pj_positionType');
    Session::newInstance()->_keepForm('pj_salaryText');
    Session::newInstance()->_keepForm('pj_data');
}
osc_add_hook('pre_item_post', 'job_pre_item_post') ;
osc_add_hook('save_input_session', 'job_save_inputs_into_session' );

// this is needed in order to be able to activate the plugin
osc_register_plugin(osc_plugin_path(__FILE__), 'job_call_after_install');
// this is a hack to show a Uninstall link at plugins table (you could also use some other hook to show a custom option panel)
osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", 'job_call_after_uninstall');

// show an item special attributes
osc_add_hook('item_detail', 'job_item_detail');
osc_add_hook('item_detail', 'job_linkedin');

// delete locale
osc_add_hook('delete_locale', 'job_delete_locale');
// delete item
osc_add_hook('delete_item', 'job_delete_item');


/* Help sections */
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
                    osc_add_hook('help_box', 'help_add_vacancy', 9);
                break;
                case(''):
                    osc_add_hook('help_box', 'help_list_vacancies', 9);
                break;
            }
        break;
        case('pages'):
            switch($action) {
                case('add'):
                case('edit'):
                    osc_add_hook('help_box', 'help_add_jobboard_page', 9);
                break;
                case(''):
                    osc_add_hook('help_box', 'help_jobboard_pages', 9);
                break;
            }
        break;
        case('plugins'):
            switch(urldecode(Params::getParam('file'))) {
                case('jobboard/people.php'):
                    osc_add_hook('help_box', 'help_list_applicants', 9);
                break;
                case('jobboard/people_detail.php'):
                    osc_add_hook('help_box', 'help_detail_applicant', 9);
                break;
            }
        break;
        case('appearance'):
            switch(urldecode(Params::getParam('file'))) {
                case('oc-content/themes/corporateboard/admin/settings.php'):
                    osc_add_hook('help_box', 'help_settings_jobboard', 9);
                break;
                case('oc-content/themes/corporateboard/admin/colors.php'):
                    osc_add_hook('help_box', 'help_appearance_jobboard', 9);
                break;
            }
        break;
    }
}
osc_add_hook('init_admin', 'help_jobboard_init');
function remove_help_core() {
    osc_remove_hook('help_box','addHelp');
}
osc_add_hook('admin_header', 'remove_help_core');

function applicant_admin_menu_current($class) {
    if( urldecode(Params::getParam('file')) === 'jobboard/people_detail.php' ) {
        return 'current';
    }

    return $class;
}
osc_add_filter('current_admin_menu_corporateboard', 'applicant_admin_menu_current');
/* /Help sections */

// register js and css scripts
osc_register_script('jquery-rating', osc_plugin_url(__FILE__) . 'js/rating/jquery.rating.js', 'jquery');
osc_register_script('jquery-metadata', osc_plugin_url(__FILE__) . 'js/rating/jquery.MetaData.js', 'jquery');

function admin_assets_jobboard() {
    osc_enqueue_style('jobboard-css', osc_plugin_url(__FILE__) . 'css/styles.css');
    switch(urldecode(Params::getParam('file'))) {
        case('jobboard/dashboard.php'):
            osc_enqueue_style('jquery-rating', osc_plugin_url(__FILE__) . 'css/dashboard.css');
        break;
        case('jobboard/people_detail.php'):
            osc_enqueue_style('jobboard-css', osc_plugin_url(__FILE__) . 'css/people_detail.css');
        case('jobboard/people.php'):
            osc_enqueue_script('jquery-rating');
            osc_enqueue_script('jquery-metadata');
            osc_enqueue_style('jquery-rating', osc_plugin_url(__FILE__) . 'js/rating/jquery.rating.css');
        break;
    }
}
osc_add_hook('init_admin', 'admin_assets_jobboard');

function default_settings_jobboard() {
    // always active osc_item_attachment
    if( !osc_item_attachment() ) {
        osc_set_preference('item_attachment', true);
    }
    if( osc_price_enabled_at_items() ) {
        osc_set_preference('enableField#f_price@items', false);
    }
    if( osc_images_enabled_at_items() ) {
        osc_set_preference('enableField#images@items', false);
    }
    if( osc_max_images_per_item() > 0 ) {
        osc_set_preference('numImages@items', 0);
    }
    //reset preferences
    osc_reset_preferences();

    if(Params::getParam('page')=='items' && Params::getParam('action')=='post') {
        Session::newInstance()->_setForm('contactName', osc_page_title());
        Session::newInstance()->_setForm('contactEmail', osc_contact_email());
    }
}
osc_add_hook('init_admin', 'default_settings_jobboard');

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
            }
            break;
        default:
            break;
    }
    return $title;
}
osc_add_filter('admin_title', 'jobboard_titles', 9);

/* H1 titles */
function jobboard_customPageHeader_vacancies() { ?>
    <h1><?php _e('Vacancies', 'jobboard'); ?>
        <a href="#" class="btn ico ico-32 ico-help float-right"></a>
        <a href="<?php echo osc_admin_base_url(true) . '?page=items&action=post' ; ?>" class="btn btn-green ico ico-32 ico-add-white float-right"><?php _e('Add vacancy', 'jobboard'); ?></a>
    </h1>
<?php
}
function jobboard_customPageHeader_vacancies_post() { ?>
    <h1><?php _e('Vacancies', 'jobboard'); ?>
        <a href="#" class="btn ico ico-32 ico-help float-right"></a>
    </h1>
<?php
}
function corporateboard_remove_title_header(){
    osc_remove_hook('admin_page_header','customPageHeader');

}
if(Params::getParam('page') == 'items'){
    osc_add_hook('admin_header','corporateboard_remove_title_header');
    if(Params::getParam('action') == ''){
        osc_add_hook('admin_page_header','jobboard_customPageHeader_vacancies');
    } else {
        osc_add_hook('admin_page_header','jobboard_customPageHeader_vacancies_post');
    }
}

function jobboard_replace_listing($string) {
    return preg_replace(array('|Listing|', '|listing|'), array(__('Vacancy', 'jobboard'), __('vacancy', 'jobboard')), $string);
}
osc_add_filter('gettext', 'jobboard_replace_listing');
function jobboard_replace_listing_plural($string) {
    return preg_replace(array('|Listings|', '|listings|'), array(__('Vacancies', 'jobboard'), __('vacancies', 'jobboard')), $string);
}
osc_add_filter('gettext', 'jobboard_replace_listing_plural', 1);

// Custom title
osc_add_filter('custom_plugin_title','jobboard_dashboard_title');
function jobboard_dashboard_title($string){
    if(Params::getParam('page') == 'plugins' && Params::getParam('file') == 'jobboard/dashboard.php'){
        $string = __('Dashboard', 'jobboard');
    }
    if(Params::getParam('page') == 'plugins' && Params::getParam('file') == 'jobboard/people.php'){
        $string = __('Applicants', 'jobboard') . '<a href="#" class="btn ico ico-32 ico-help float-right"></a>';
    }
    return $string;
}
/* /H1 titles */

osc_add_hook('admin_items_table','job_items_table_header');
osc_add_filter("items_processing_row", "job_items_row");

/**
 * Apply with linkedin - document.domain 
 */
function jobboard_set_domain() 
{
    ?>
    <script type="text/javascript">
	document.domain = 'osclass.com'; 
    </script>
    <?php 
}
// get subdomain - linkedin related - osclass.com/apply/
$url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; 
$parsedUrl = parse_url($url);
$host = explode('.', $parsedUrl['host']);
$a2 = array_pop($host);
$a1 = array_pop($host);
$subdomain = $a1.".".$a2;

if( $subdomain == 'osclass.com') {
    osc_add_hook('header', 'jobboard_set_domain');
}
?>