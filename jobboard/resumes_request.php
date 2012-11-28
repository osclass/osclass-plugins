<?php

    function jobboard_pack_resumes() {
        $path = preg_replace('/[\/]{2,}/', '/', osc_get_preference('upload_path', 'jobboard_plugin')."/");
        $mjb = ModelJB::newInstance();
        $applicants = $mjb->getAllApplicants();
        $zdate = date('YmdHis');
        $zip_number = 1;
        $zip_k = 0;
        $zip_max_number = 100;
        $zip_name = $path."RESUMES_".$zdate."_{COUNTER}.zip";

        if (class_exists('ZipArchive')) {

            $zip = new ZipArchive;
            if ($zip->open(str_replace("{COUNTER}", $zip_number, $zip_name), ZipArchive::CREATE) === TRUE) {
                foreach($applicants as $a) {
                    $file = $mjb->getCVFromApplicant($a['pk_i_id']);
                    $a_name = osc_sanitizeString($a['s_name'])."_".$a['s_email']."_".$a['s_phone']."_jobid".$a['fk_i_item_id']."_id".$a['pk_i_id'].".";

                    if($file) {
                        if(file_exists($path.$file['s_name'])) {
                            if($zip_k>=$zip_max_number) {
                                $zip->close();
                                $zip_number++;
                                $zip_k = 0;
                                if (!$zip->open(str_replace("{COUNTER}", $zip_number, $zip_name), ZipArchive::CREATE) === TRUE) {
                                    return false;
                                }
                            }
                            $tmp = explode(".", $file['s_name']);
                            $a_name .= $tmp[count($tmp)-1];
                            $zip->addFile($path.$file['s_name'], strtolower($a_name));
                            $zip_k++;
                        }
                    }
                }
                $zip->close();
            } else {
                return false;
            }

        } else {

            require_once LIB_PATH . 'pclzip/pclzip.lib.php';

            $zip = new PclZip(str_replace("{COUNTER}", $zip_number, $zip_name));
            if($zip) {
                $zip_array_files = array();
                foreach($applicants as $a) {
                    $file = $mjb->getCVFromApplicant($a['pk_i_id']);
                    $a_name = osc_sanitizeString($a['s_name'])."_".$a['s_email']."_".$a['s_phone']."_jobid".$a['fk_i_item_id']."_id".$a['pk_i_id'].".";

                    if($file) {
                        if(file_exists($path.$file['s_name'])) {
                            if($zip_k>=$zip_max_number) {
                                if(!$zip->create($zip_array_files, PCLZIP_OPT_REMOVE_PATH, $v_remove)) {
                                    return false;
                                }
                                $zip_array_files = array();
                                $zip_number++;
                                $zip_k = 0;
                                $zip = new PclZip(str_replace("{COUNTER}", $zip_number, $zip_name));
                                if (!$zip) {
                                    return false;
                                }
                            }
                            $tmp = explode(".", $file['s_name']);
                            $a_name .= $tmp[count($tmp)-1];
                            $zip_array_files[] = array( PCLZIP_ATT_FILE_NAME => $path.$file['s_name'], PCLZIP_ATT_FILE_NEW_FULL_NAME => strtolower($a_name) );
                            $zip_k++;
                        }
                    }
                }

                $v_remove = osc_base_path();
                // To support windows and the C: root you need to add the
                // following 3 lines, should be ignored on linux
                if (substr($v_remove, 1,1) == ':') {
                    $v_remove = substr($v_remove, 2);
                }
                $v_list = $zip->create($zip_array_files, PCLZIP_OPT_REMOVE_PATH, $v_remove);
                if ($v_list == 0) {
                    return false;
                }
            } else {
                return false;
            }
        }

        $list = array();
        for($k=1;$k<=$zip_number;$k++) {
            $list[] = str_replace($path, "", str_replace("{COUNTER}", $k, $zip_name));
        }
        return $list;
    }


    $resumes = jobboard_pack_resumes();
    $url = str_replace(osc_base_path(), osc_base_url(), preg_replace('/[\/]{2,}/', '/', osc_get_preference('upload_path', 'jobboard_plugin')."/"));
    $list = '<ul>';
    foreach($resumes as $r) {
        $list .= '<li>'.($url.$r).'</li>';
    }
    $list .= '</ul>';

    $prefLocale = osc_language() ;
    $page = Page::newInstance()->findByInternalName('email_resumes_jobboard') ;
    $page_description = $page['locale'] ;

    $_title = osc_apply_filter('email_title', $page_description[$prefLocale]['s_title']);
    $_body  = osc_apply_filter('email_description', $page_description[$prefLocale]['s_text']);

    $words   = array() ;
    $words[] = array(
        '{CONTACT_NAME}'
        ,'{RESUME_LIST}'
    );
    $words[] = array(
        osc_logged_admin_name()
        ,$list
    );
    $title = osc_mailBeauty($_title, $words);
    $body  = osc_mailBeauty($_body , $words);

    $params = array(
        'subject'  => $title,
        'to'       => osc_contact_email(),
        'to_name'   => __('Admin mail system', 'jobboard'),
        'body'     => $body,
        'alt_body' => $body
    );

    osc_sendMail($params);

?>