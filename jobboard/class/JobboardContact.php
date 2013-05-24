<?php

/**
 *  - remove default mailing related to contact, replace it too
 *  - Add extra fields like birtday, sex... contact or item contact page
 *  - save applicant contact / save spontaneous applicant contact
 */
class JobboardContact
{
    public function __construct() {
        // remove contact email
        osc_remove_hook('hook_email_item_inquiry', 'fn_email_item_inquiry');
        // add extra fields like birtday, sex... contact or item contact page
        osc_add_hook('item_contact_form', array(&$this, 'jobboard_add_extra_fields') );
        osc_add_hook('contact_form'     , array(&$this, 'jobboard_add_extra_fields') );
        // add killer questions only if is a vacancy and have questions
        osc_add_hook('item_contact_form', array(&$this, 'job_add_killer_question_form'), 6);
        // save applicant contact / save spontaneous applicant contact
        osc_add_hook('post_item_contact_post', array(&$this, 'jobboard_save_contact_listing'), 1);
        osc_add_hook('pre_contact_post',       array(&$this, 'jobboard_save_contact') );
        // detail / contact_form
    }

    /**
     * Add extra fields to contact form
     */
    function jobboard_add_extra_fields() {
        // add age field [m/d/y]
        // add sex field [Male/Femele/Undef]
        require_once(JOBBOARD_PATH . 'extra_contact_form.php');
    }

    /**
     * Add killer questions to vacancy contact form if have questions
     * @param type $jobId
     */
    function job_add_killer_question_form( $jobId ) {
        $job = ModelJB::newInstance()->getJobsAttrByItemId($jobId);
        if(@$job['fk_i_killer_form_id']!='') {
            $aQuestions = ModelKQ::newInstance()->getKillerQuestions($job['fk_i_killer_form_id']);
            require_once(JOBBOARD_PATH . 'item_detail_killer_questions.php');
        }
    }

    /**
     * Contact/Apply to a vacancy
     */
    function jobboard_save_contact_listing() {
        $this->jobboard_common_contact(osc_item_id(), osc_item_url());
        require_once(JOBBOARD_PATH . 'email.php');
        send_email_to_applicant('listing');
        send_notifaction_applicant_to_admin('listing');
    }

    /**
     * Contact/Apply to spontaneous job
     * @param type $params
     */
    function jobboard_save_contact($params) {
        $this->jobboard_common_contact(null, osc_contact_url(), @$params['attachment']);
        require_once(JOBBOARD_PATH . 'email.php');
        send_email_to_applicant('spontaneous');
        send_notifaction_applicant_to_admin('spontaneous');
        osc_add_flash_ok_message(__('Thanks for sending us your CV', 'jobboard'));
        header('Location: ' . osc_contact_url()); exit;
    }


    /**
     * Save fields form into session and create new applicant.
     * apply vacancy/apply spontaneous
     *
     * @param type $itemID
     * @param type $url
     * @param type $uploadCV
     * @return type
     */
    function jobboard_common_contact($itemID, $url, $uploadCV = '') {
        $error_attachment   = false;
        $convert_to_pdf     = false;

        $source = Params::getParam('from');

        $name   = Params::getParam('yourName');
        $email  = Params::getParam('yourEmail');

        $birth  = Params::getParam('birthday');
        $sex    = Params::getParam('sex');

        $cover  = Params::getParam('message');
        $phone  = Params::getParam('phoneNumber');
        $aCV    = Params::getFiles('attachment');

        // get killer form id
        $killerFormId = Params::getParam('killerFormId');

        // GET EXTRA PARMAS
        // check fields
        if( $name === '' ) {
            osc_add_flash_error_message(__("Name is required", 'jobboard'));
            $this->_save_jobboard_contact_listing();
            header('Location: ' . $url); die;
        }
        if( $email === '' ) {
            osc_add_flash_error_message(__("Email is required", 'jobboard'));
            $this->_save_jobboard_contact_listing();
            header('Location: ' . $url); die;
        }
        if( $birth === '' ) {
            osc_add_flash_error_message(__("Birthday is required", 'jobboard'));
            $this->_save_jobboard_contact_listing();
            header('Location: ' . $url); die;
        } else {
            // check date format & convert date to mysql date format
            // we recive mm/dd/yyyy id valid ?
            $aDate = explode('/', $birth);
            $birth = date("Y-m-d", mktime(0,0,0,$aDate[0],$aDate[1],$aDate[2]) );
            if($birth === false) {
                osc_add_flash_error_message(__("Invalid birthday date", 'jobboard'));
                $this->_save_jobboard_contact_listing();
                header('Location: ' . $url); die;
            }
        }

        if( $sex === '' ) {
            osc_add_flash_error_message(__("Sex is required", 'jobboard'));
            $this->_save_jobboard_contact_listing();
            header('Location: ' . $url); die;
        }
        if( $cover === '' ) {
            osc_add_flash_error_message(__("Cover is required", 'jobboard'));
            $this->_save_jobboard_contact_listing();
            header('Location: ' . $url); die;
        }
        if( isset($aCV['name']) && $aCV['name'] === '' ) {
            osc_add_flash_error_message(__("CV is required", 'jobboard'));
            $this->_save_jobboard_contact_listing();
            header('Location: ' . $url); die;
        }
        $s_source = '';
        if($source == 'linkedin') {
            $s_source = 'linkedinapply';
        }

        // check fields  --  get killer question results
        $aQuestionResults = array();
        $error_required_questions = false;
        if($killerFormId!='') {
            // if there are killer questions all are required!
            $aQuestionResults   = Params::getParam('question');
            $aQuestionsId       = Params::getParam('questionsId');
            // check if all questions have answer
            $auxQuestionsId = array();
            foreach($aQuestionsId as $key => $value) {
                if(!key_exists($value, $aQuestionResults) ) {
                    $error_required_questions = true;
                } else {
                    // check open questions ...
                    $auxValue = $aQuestionResults[$value];
                    if(is_array($auxValue)) {
                        $a_open_answer = trim($auxValue['open']);
                        if($a_open_answer=='') {
                            $error_required_questions = true;
                        }
                    } else if(is_null($auxValue) || empty($auxValue)) {
                        $error_required_questions = true;
                    }
                }
            }
            if($error_required_questions) {
                // errors
                osc_add_flash_error_message(__("All questions are required", 'jobboard'));
                $this->_save_jobboard_contact_listing();
                header('Location: ' . $url); die;
            }
        }

        // check: apply only once for each job offer
        $numberApplys = ModelJB::newInstance()->countApply($itemID, $email);

        if( $numberApplys > 0 ) {
            osc_add_flash_error_message(__("You can only apply once a job offer", 'jobboard'));
            $this->_save_jobboard_contact_listing();
            header('Location: ' . $url); die;
        }

        $s_source = '';
        if($source!='linkedin') {
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
                    $this->_save_jobboard_contact_listing();
                    header('Location: ' . $url); die;
                }

                $_name = $aCV['name'];
                if(preg_match('/pdf$/', strtolower($_name))==0) {
                    $convert_to_pdf = true;
                }
            }
        }

        // insert to database
        $mJB = ModelJB::newInstance();

        $applicantID = $mJB->insertApplicant($itemID, $name, $email, $cover, $phone, $birth, $sex, $s_source);
        View::newInstance()->_exportVariableToView('applicantID', $applicantID);
        // return to listing url
        if( !$applicantID ) {
            osc_add_flash_error_message(__("There were some problem processing your application, please try again", 'jobboard'));
            header('Location: ' . $url); die;
        }

        if($source!='linkedin') {
            $fileName = date('YmdHis') . '_' . $aCV['name'];
            if($uploadCV=='') {
                if(isset($aCV['name']) && $aCV['error'] == UPLOAD_ERR_OK) {
                    $tmp_name = $aCV['tmp_name'];
                    if( move_uploaded_file($tmp_name, osc_get_preference('upload_path', 'jobboard_plugin') . $fileName) ) {
                        $mJB->insertFile($applicantID, $fileName);
                    } else {
                        $error_attachment = true;
                    }
                } else {
                    $error_attachment = true;
                }
            } else {
                if( copy($uploadCV, osc_get_preference('upload_path', 'jobboard_plugin') . $fileName) ) {
                    @unlink($uploadCV);
                    $mJB->insertFile($applicantID, $fileName);
                } else {
                    $error_attachment = true;
                }
            }

            if( $error_attachment ) {
                ModelJB::newInstance()->deleteApplicant($applicantID);
                $this->_save_jobboard_contact_listing();
                osc_add_flash_error_message(__("There were some problem processing your application, please try again", 'jobboard'));
                header('Location: ' . $url); die;
            } else {
                /*
                 * send file to convert -> server pdf convert
                 */
                if( $convert_to_pdf === true ) {
                    $applicantCv = ModelJB::newInstance()->getCVFromApplicant($applicantID);

                    $tmpfile    = osc_get_preference('upload_path', 'jobboard_plugin') . $fileName;
                    $filename   = basename($aCV['name']);

                    $callback   = osc_base_url(true) .'?page=ajax&action=custom&ajaxfile='.osc_plugin_folder(__FILE__).'reciveCv.php';

                    $data = array(
                        'uploaded_file' => '@'.$tmpfile,
                        'applicantId'   => $applicantID,
                        'secret'        => $applicantCv['s_secret'],
                        'callback'      => $callback
                    );

                    $url = osc_get_preference('url_pdf_convert', 'jobboard_plugin');

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    // debug curl
                    curl_setopt($ch, CURLOPT_VERBOSE, 1);
                    if( ! $result = curl_exec($ch))
                    {
                        trigger_error(curl_error($ch));
                    }
                    curl_close($ch);
                }
            }
        } else {
            // from linkedin + download cv.pdf
            // downoad file
            $url_pdf  = Params::getParam('pdfUrl');
            $fileName = date('YmdHisu') . '.pdf' ;
            $dest     = osc_get_preference('upload_path', 'jobboard_plugin').$fileName;

            $res = file_put_contents($dest, file_get_contents($url_pdf));
             // add job file
            if($res !== false) {
                $mJB->insertFile($applicantID, $fileName);
            }
        }

        // save killer question form results and save a temporal punctuation
        if($killerFormId!='') {
            // do stuff
            foreach($aQuestionResults as $questionId => $q_answer) {
                if( is_array($q_answer) && isset($q_answer['open']) ) {
                    ModelKQ::newInstance()->insertAnswerOpened($applicantID, $killerFormId, $questionId, $q_answer['open']);
                } else {
                    ModelKQ::newInstance()->insertAnswerClosed($applicantID, $killerFormId, $questionId, $q_answer);
                }
            }
            /*
             * Evaluate killer questions form ...
             * Sum all answer punctuation and save into applicant table
             *
             * If any answer punctuation is reject, update applicant status automatically.
             */
            $aInfo = ModelKQ::newInstance()->calculatePunctuationOfApplicant($applicantID);
        }

        // end
        $st = new Stream();
        $st->log_new_applicant($applicantID, $itemID);

        return true;
    }

    /**
     * save jobboard contact fields into session
     */
    function _save_jobboard_contact_listing() {
        Session::newInstance()->_setForm('yourEmail',    Params::getParam('yourEmail'));
        Session::newInstance()->_setForm('yourName',     Params::getParam('yourName'));
        Session::newInstance()->_setForm('phoneNumber',  Params::getParam('phoneNumber'));
        Session::newInstance()->_setForm('message_body', Params::getParam('message'));
        // v 1.2
        Session::newInstance()->_setForm('birthday',     Params::getParam('birthday'));
        Session::newInstance()->_setForm('sex',          Params::getParam('sex'));

        // v 1.3 - save killer questions
        // get killer form id
        $killerFormId = Params::getParam('killerFormId');

        $aQuestionResults = array();
        $error_required_questions = false;
        if($killerFormId!='') {
            // if there are killer questions all are required!
            $aQuestionResults   = Params::getParam('question');

            foreach($aQuestionResults as $questionId => $q_answer) {
                if( is_array($q_answer) && isset($q_answer['open']) ) {
                    Session::newInstance()->_setForm('question['.$questionId.']', $q_answer['open']);
                } else {
                    Session::newInstance()->_setForm('question['.$questionId.']', $q_answer );
                }
            }
        }

    }
}

