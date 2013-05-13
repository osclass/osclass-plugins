<?php
/*
 * Save file to
 */
error_reporting(E_ALL);

$token_valid = "ninja";
$applicantId = Params::getParam('applicantId');
if(!isset($applicantId) || $applicantId=='') {
    // error applicantId is a required field
    exit;
}
$secret = Params::getParam('secret');
if(!isset($secret) || $secret=='') {
    // error applicantId is a required field
    exit;
}

if(Params::getParam('token') == $token_valid) {

    $applicantCv = ModelJB::newInstance()->getCVFromApplicant($applicantId);
    if($applicantCv['s_secret'] != $secret) {
        // error secret don't match
        exit;
    }

    $file = Params::getFiles('uploaded_file');
    echo "token ok !  ";
    if(isset($file['name']) && $file['name']!='') {
        if( $file['error'] == UPLOAD_ERR_OK ) {
            echo "file no error !  ";
            error_log('destination : ' . $path.$fileName);
            $fileName = $file['name'];
            $tmp_name = $file['tmp_name'];
            $path = osc_get_preference('upload_path', 'jobboard_plugin');
            echo "file move to " . $path.$fileName ;
            if( move_uploaded_file($tmp_name, $path.$fileName) ) {
                // update applicant cv
                ModelJB::newInstance()->updateFile($applicantId, $filename, $secret);
            }
        }
    }
}
?>
