<?php

    define('ABS_PATH', dirname(dirname(dirname(dirname($_SERVER['SCRIPT_FILENAME'])))) . '/');
    require_once(ABS_PATH . 'oc-load.php');

    $tmp = explode("|", Params::getParam('data'));
    $id  = $tmp[0];
    $secret = isset($tmp[1])?$tmp[1]:"osclass.org";

    if( $id === '' ) {
        require_once(osc_lib_path() . 'osclass/helpers/hErrors.php');
        $title   = 'Osclass &raquo; Error';
        $message = __("This resume doesn't exist", 'jobboard');
        osc_die($title, $message);
    }

    $pdf = ModelJB::newInstance()->getCVFromApplicant($id);

    if( count($pdf) === 0 ) {
        require_once(osc_lib_path() . 'osclass/helpers/hErrors.php');
        $title   = 'Osclass &raquo; Error';
        $message = __("This resume doesn't exist", 'jobboard');
        osc_die($title, $message);
    }

    if(osc_is_admin_user_logged_in() || ($secret==$pdf['s_secret'] && ((strtotime($pdf['dt_secret_date'])+10*60)>=time()))) {
        require_once(osc_lib_path() . 'osclass/mimes.php');

        $applicant = ModelJB::newInstance()->getApplicant($id);

        $filename  = osc_sanitizeString($applicant['s_name']);
        $path      = osc_get_preference('upload_path', 'jobboard_plugin') . $pdf['s_name'];

        // get content-type
        $filename_extension = preg_replace('|.*?\.([0-9a-z]+)$|i', '$01', $applicant['s_name']);
        $file_mime = $mimes[$filename_extension];
        if( is_array($file_mime) ) {
            $file_mime = $file_mime[0];
        }

        header('Content-Description: '.$filename);
        header('Content-Type: ' . $file_mime);
        header('Content-Disposition: attachment; filename=' . $pdf['s_name']);
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