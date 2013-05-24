<?php if ( !defined('ABS_PATH') ) exit('ABS_PATH is not loaded. Direct access is not allowed.');

class JobboardAjax
{
    /**
     * Set applicant rating, logging the action
     */
    function ajax_rating_request() {
        $result = ModelJB::newInstance()->setRating(Params::getParam('applicantId'), Params::getParam('rating'));
        if( ($result !== false) && ($result > 0) ) {
            // log rate an applicant
            $st = new Stream();
            $st->log_rate_applicant(Params::getParam('applicantId'), Params::getParam('rating'));
        }
    }

    /**
     * Set answer punctuation recalculating the puntuation of test (set of questions)
     */
    function ajax_answer_punctuation() {
        // update punctuation of << open questions >>
        $result = ModelKQ::newInstance()->updatePunctuationQuestionResult(Params::getParam('killerFormId'), Params::getParam('applicantId'), Params::getParam('questionId'), Params::getParam('punctuation'));
        if($result !== false) {
            $aInfo = ModelKQ::newInstance()->calculatePunctuationOfApplicant(Params::getParam('applicantId'));
            echo json_encode( array('punctuation'   => Params::getParam('punctuation'),
                                     'score'       => $aInfo['score'],
                                     'corrected'   => $aInfo['corrected']) );
        } else {
            echo '0';
        }
    }

    /**
     * Change applicant statusm logging the action
     */
    function ajax_applicant_status() {
        $result = ModelJB::newInstance()->changeStatus(Params::getParam("applicantId"), Params::getParam("status"));
        if( ($result !== false) && ($result > 0 ) ) {
            $st = new Stream();
            $st->log_change_status_application(Params::getParam('applicantId'), Params::getParam('status'));
        }
    }

    /**
     * Send email applicant to inform about a change of candidature status
     * @return type
     */
    function ajax_applicant_status_message() {
        $applicantID = Params::getParam('applicantID');
        $status      = Params::getParam('status');

        $aStatus    = jobboard_status();
        $aApplicant = ModelJB::newInstance()->getApplicant($applicantID);

        if( count($aApplicant) === 0 ) {
            $json = array('error' => true);
            echo json_encode($json);
            return false;
        }

        $email_txt = array(
            'company_url'      => osc_base_url(),
            'company_link'     => sprintf('<a href="%1$s">%2$s</a>', osc_base_url(), osc_page_title()),
            'company_name'     => osc_page_title(),
            'admin_login_url'  => osc_admin_base_url(),
            'applicant_name'   => $aApplicant['s_name'],
            'applicant_status' => $aStatus[$aApplicant['i_status']]
        );

        if( !is_null($aApplicant['fk_i_item_id']) ) {
            $aItem = Item::newInstance()->findByPrimaryKey($aApplicant['fk_i_item_id']);
            View::newInstance()->_exportVariableToView('item', $aItem);

            $email_txt['job_offer_title'] = osc_item_title();
            $email_txt['job_offer_link']  = sprintf('<a href="%1$s">%2$s</a>', osc_item_url(), osc_item_title());
            $email_txt['job_offer_url']   = osc_item_url();
        } else {
            $email_txt['job_offer_title'] = __('spontaneous', 'jobboard');
            $email_txt['job_offer_link']  = sprintf('<a href="%1$s">%2$s</a>', osc_contact_url(), __('spontaneous', 'jobboard'));
            $email_txt['job_offer_url']   = osc_contact_url();
        }

        $email_msg = array();
        $email_msg['en_US'] = "Hi {$email_txt['applicant_name']},

        The {$email_txt['company_name']} company would like to inform you that your application for {$email_txt['job_offer_link']} has changed to: {$email_txt['applicant_status']}.

        This is just an automatic message, to check the status of your application go to {$email_txt['company_link']}.

        Thanks and good luck!,
        {$email_txt['company_link']}";
            $email_msg['es_ES'] = "Hola {$email_txt['applicant_name']},

        La empresa {$email_txt['company_name']} te comunica que tu candidatura para el empleo {$email_txt['job_offer_link']} ha pasado al estado: {$email_txt['applicant_status']}.

        Este es un mensaje automático, para conocer más sobre el estado de tu candidatura deberás dirigirte a {$email_txt['company_link']}.

        Gracias,
        {$email_txt['company_link']}";

        $email_body = $email_msg['en_US'];
        if( array_key_exists(osc_current_user_locale(), $email_msg) ) {
            $email_body = $email_msg[osc_current_user_locale()];
        }

        $json = array('message' => nl2br($email_body), 'status' => $status, 'error' => false);
        echo json_encode($json);
        return true;
    }

    /**
     * applicant to inform about a change of candidature status
     *
     * @return type
     */
    function ajax_applicant_status_notification() {
        $applicantID = Params::getParam('applicantID');
        $message     = Params::getParam('message', false, false);

        if( $message === '' ) {
            echo 'false';
            return false;
        }

        // check if the applicant exist
        $aApplicant = ModelJB::newInstance()->getApplicant($applicantID);
        if( count($aApplicant) === 0 ) {
            echo 'false';
            return false;
        }

        // prepare email subject
        $email_title = array();
        $email_title['en_US'] = sprintf('Application status change at %1$s', osc_page_title());
        $email_title['es_ES'] = sprintf('Cambio de estado de la solicitud de empleo en %1$s', osc_page_title());
        $email_subject = $email_title['en_US'];
        if( array_key_exists(osc_current_user_locale(), $email_title) ) {
            $email_subject = $email_title[osc_current_user_locale()];
        }
        // prepare email params
        $params = array(
            'to'       => $aApplicant['s_email'],
            'to_name'  => $aApplicant['s_name'],
            'subject'  => $email_subject,
            'body'     => $message
        );
        // send email
        osc_sendMail($params);
        echo 'true';
        return true;
    }

    /**
     * Save applicant note, logging the action
     */
    function ajax_note_add() {
        $noteID = ModelJB::newInstance()->insertNote(Params::getParam('applicantID'), Params::getParam('noteText'));
        if( ($noteID !== false) && ($noteID > 0 ) ) {
            $st = new Stream();
            $st->log_new_note(Params::getParam('applicantID'));
        }
        $aNote = ModelJB::newInstance()->getNoteByID($noteID);
        $aNote['day']   = date('d', strtotime($aNote['dt_date']));
        $aNote['month'] = date('M', strtotime($aNote['dt_date']));
        $aNote['year']  = date('Y', strtotime($aNote['dt_date']));
        echo json_encode($aNote);
    }

    /**
     * Update applicant note, logging the action
     */
    function ajax_note_edit() {
        $result = ModelJB::newInstance()->updateNote(Params::getParam('noteID'), Params::getParam('noteText'));
        if( ($result !== false) && ($result > 0 ) ) {
            $st = new Stream();
            $st->log_edit_note(Params::getParam('applicantID'), Params::getParam('noteID'));
        }
        $aNote = ModelJB::newInstance()->getNoteByID(Params::getParam('noteID'));
        $aNote['day']   = date('d', strtotime($aNote['dt_date']));
        $aNote['month'] = date('M', strtotime($aNote['dt_date']));
        $aNote['year']  = date('Y', strtotime($aNote['dt_date']));
        echo json_encode($aNote);
    }

    /**
     * Remove applicant note, logging the action
     */
    function ajax_note_delete() {
        $note   = ModelJB::newInstance()->getNoteByID(Params::getParam('noteID'));
        $result = ModelJB::newInstance()->deleteNote(Params::getParam('noteID'));
        if( ($result !== false) && ($result > 0 ) ) {
            $st = new Stream();
            $st->log_remove_note(Params::getParam('applicantID'), Params::getParam('noteID'));
        }
    }

    /**
     * Remove 'killer' question
     */
    function ajax_question_delete() {
        $result = ModelKQ::newInstance()->removeKillerQuestion(Params::getParam('killerFormId'), Params::getParam('questionId'));

        if( ($result !== false) && ($result > 0) ) {
            echo 1;
        } else {
            echo 0;
        }
    }
}