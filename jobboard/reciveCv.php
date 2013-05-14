<?php
/*
 * Save file to
 */

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
    if(isset($file['name']) && $file['name']!='') {
        if( $file['error'] == UPLOAD_ERR_OK ) {

            $fileName = $file['name'];
            $tmp_name = $file['tmp_name'];
            $path = osc_get_preference('upload_path', 'jobboard_plugin');

            if( move_uploaded_file($tmp_name, $path.$fileName) ) {
                // update applicant cv
                ModelJB::newInstance()->updateFile($applicantId, $fileName, $secret);
            }
        }
    }
}
?>
