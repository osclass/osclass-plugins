<?php

    require_once('../../../oc-load.php');

    if(osc_is_admin_user_logged_in()) {

        $id = Params::getParam('id');

        if($id!='') {

            $pdf = ModelJB::newInstance()->getCVFromApplicant($id);
            $applicant = ModelJB::newInstance()->getApplicant($id);


            $filename = osc_sanitizeString($applicant['s_name'])."_resume.pdf";
            $path = osc_get_preference('upload_path', 'jobboard_plugin').$pdf['s_name'];

            header('Content-Description: '.$filename);
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename='.$filename);
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