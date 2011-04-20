<?php

require_once dirname(dirname(dirname(dirname(__FILE__)))) . "/oc-load.php";
$error = 0;
$file = Params::getFiles('cv');
View::newInstance()->_exportVariableToView('item', Item::newInstance()->findByPrimaryKey(Params::getParam('id')));

if(osc_get_preference('allow_cv_upload', 'jobs_plugin')=='1' && ((osc_get_preference('allow_cv_unreg', 'jobs_plugin')=='1' && !osc_is_web_user_logged_in()) || osc_is_web_user_logged_in())) {
    $target_path = CONTENT_PATH . "uploads/";
    $target_name =  date('YmdHis') . "_" . basename($file['name']);

    if(move_uploaded_file($file['tmp_name'], $target_path . $target_name)) {
    
        $params = array();
        $params['subject'] = sprintf(__('Someone sent you his/her resume. ( %s )', 'jobs_plugin'), osc_item_title());
        $params['body'] = sprintf(__('Someone sent you his/her resume. You could find it attached on this email, you could find the job offer here : %s', 'jobs_plugin'), osc_item_url());
        $params['alt_body'] = $params['body'];
        $params['attachment'] = $target_path . $target_name;
        
        if(osc_get_preference('send_me_cv', 'jobs_plugin')) {
            $params['to'] = osc_get_preference('cv_email', 'jobs_plugin');
        } else {
            $params['to'] = osc_item_contact_email();;
        }
        
        if(@osc_sendMail($params)) {
            $error = 0;
        } else {
            $error = 1;
        }
        
        @unlink($target_path . $target_name);
        
    } else{
        $error = 1;
    }
} else {
    $error = 1;
}

if($error==0) {
    echo json_encode(array(
        'html' => __('Your resume was sent correctly', 'jobs_plugin'),
        'code' => 1,
    ));
} else if($error==1) {
    echo json_encode(array(
        'html' => __('There were some errors, please try again', 'jobs_plugin'),
        'code' => 0,
    ));
}

?>
