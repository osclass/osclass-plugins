<?php

    // RETRIEVE DATA
    $itemId = Params::getParam("item_id");
    $name = Params::getParam("job_name");
    $email = Params::getParam("job_email");
    $cover = Params::getParam("job_cover_letter");
    $job_files = Params::getFiles("job_resume");
    
    Session::newInstance()->_setForm('pj_job_name', $name);
    Session::newInstance()->_setForm('pj_job_email', $email);
    Session::newInstance()->_setForm('pj_job_cover_letter', $cover);
    Session::newInstance()->_keepForm('pj_job_name');
    Session::newInstance()->_keepForm('pj_job_email');
    Session::newInstance()->_keepForm('pj_job_cover_letter');
    
    $msg = '';
    $error = false;
    if($itemId=='') {
        $error = true;
        $msg .= __("Something went wrong", "jobs_attributes")."<br/>";
    }
    if($name=='') {
        $error = true;
        $msg .= __("Name is required", "jobs_attributes")."<br/>";
    }
    if($email=='') {
        $error = true;
        $msg .= __("Email is required", "jobs_attributes")."<br/>";
    } else if(!osc_validate_email($email)) {
        $error = true;
        $msg .= __("Email is not valid", "jobs_attributes")."<br/>";
    }

    if(!isset($job_files['name']) || !isset($job_files['name'][0]) || $job_files['name'][0]=='') {
        $error = true;
        $msg .= __("Your resume is required", "jobs_attributes")."<br/>";
    } else {

        require LIB_PATH . 'osclass/mimes.php';
        // get allowedExt
        $aMimesAllowed = array();
        $aExt = array('pdf', 'rtf', 'doc', 'docx', 'odt');
        foreach($aExt as $ext){
            if(isset($mimes[$ext])) {
                $mime = $mimes[$ext];
                if( is_array($mime) ){
                    foreach($mime as $aux){
                        if( !in_array($aux, $aMimesAllowed) ) {
                            array_push($aMimesAllowed, $aux );
                        }
                    }
                } else {
                    if( !in_array($mime, $aMimesAllowed) ) {
                        array_push($aMimesAllowed, $mime );
                    }
                }
            }
        }
        foreach ($job_files['error'] as $key => $error) {
            if ($error == UPLOAD_ERR_OK) {
                // check mime file
                $fileMime = $job_files['type'][$key] ;

                if(!in_array($fileMime,$aMimesAllowed)) {
                    $error = true;
                    $msg .= __("The file you tried to upload does not have a valid extension", "jobs_attributes")."<br/>";
                    break;
                }
            }
        }

    }
    
    if($error) {
        osc_add_flash_error_message($msg);
        job_js_redirect_to(osc_get_http_referer());
    } else {
        // REACHED THIS POINT, EVERY CHECK WAS GOOD
        
        
        $mjb = ModelJB::newInstance();
        $applicantId = $mjb->insertApplicant($itemId, $name, $email, $cover);
        
        if($applicantId) {
            $error_files = 0;
            foreach ($job_files["error"] as $key => $error) {
                if ($error == UPLOAD_ERR_OK) {
                    $tmp_name = $job_files["tmp_name"][$key];
                    $fileName = date("YmdHis")."_".$job_files["name"][$key];
                    if(move_uploaded_file($tmp_name, osc_get_preference("upload_path", "jobboard_plugin").$fileName)) {
                        $mjb->insertFile($applicantId, $fileName);
                    } else {
                        $error_files++;
                    };
                } else {
                    $error_files++;
                }
            }
            
            if($error_files>0) {
                osc_add_flash_error_message(__("There were some problem processing your application, please try again", "jobs_attributes"));
                job_js_redirect_to(osc_get_http_referer());
            } else {
                ?>

                <br/>
                <br/>
                SOME NICE MESSAGE TELLING THE APPLICANT TO LOOK TO THEIR EMAIL SOON OR SOMETHING
                <br/>
                <br/>

                <?php
            }
            
        } else {
            osc_add_flash_error_message(__("There were some problem processing your application, please try again", "jobs_attributes"));
            job_js_redirect_to(osc_get_http_referer());
        }


        
    }


?>
