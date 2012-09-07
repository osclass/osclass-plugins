<?php

    define('ABS_PATH', dirname(dirname(dirname(dirname($_SERVER['SCRIPT_FILENAME'])))) . '/');
    require_once(ABS_PATH . 'oc-load.php');

    $tmp = explode("|", Params::getParam('data'));
    $id = $tmp[0];
    $secret = isset($tmp[1])?$tmp[1]:"osclass.org";

    
    if($id!='') {
        $pdf = ModelJB::newInstance()->getCVFromApplicant($id);
        
        if(osc_is_admin_user_logged_in() || ($secret==$pdf['s_secret'] && ((strtotime($pdf['dt_secret_date'])+10*60)>=time()))) {

            $applicant = ModelJB::newInstance()->getApplicant($id);


            $filename = osc_sanitizeString($applicant['s_name'])."_resume.pdf";
            $path = osc_get_preference('upload_path', 'jobboard_plugin').$pdf['s_name'];

            header('Content-Description: '.$filename);
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename='.$filename);
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($path));
            @ob_clean();
            flush();
            readfile($path);
            exit;
        }
    }
?>