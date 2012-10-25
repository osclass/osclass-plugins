<?php

    function send_email_to_applicant($type = '') {
        $email_txt = array(
            'applicant_name'  => Params::getParam('yourName'),
            'company_url'     => osc_base_url(),
            'company_link'    => sprintf('<a href="%1$s">%2$s</a>', osc_base_url(), osc_page_title()),
            'company_name'    => osc_page_title(),
        );
        switch($type) {
            case('listing'):
                $email_txt['job_offer_title'] = osc_item_title();
                $email_txt['job_offer_link']  = sprintf('<a href="%1$s">%2$s</a>', osc_item_url(), osc_item_title());
                $email_txt['job_offer_url']   = osc_item_url();
            break;
            case('spontaneous'):
                $email_txt['job_offer_title'] = __('spontaneous', 'jobboard');
                $email_txt['job_offer_link']  = sprintf('<a href="%1$s">%2$s</a>', osc_contact_url(), __('spontaneous', 'jobboard'));
                $email_txt['job_offer_url']   = osc_contact_url();
            break;
        }

        $email_msg = array();
        $email_msg['en_US'] = "Hi {$email_txt['applicant_name']},

You have just applied to {$email_txt['job_offer_link']} job offer at {$email_txt['company_link']}.

This is just an automatic message, to check the status of your application go to {$email_txt['company_name']}.

Thanks and good luck!
{$email_txt['company_link']}";
        $email_msg['es_ES'] = "Hola {$email_txt['applicant_name']},

Acabas de inscribirte a la oferta de empleo {$email_txt['job_offer_link']} de {$email_txt['company_link']}.

Este es un mensaje automático, para saber el estado de tu candidatura deberías dirigirte a {$email_txt['company_name']}.

Gracias y suerte!
{$email_txt['company_link']}";
        $email_body = $email_msg['en_US'];
        if( array_key_exists(osc_current_user_locale(), $email_msg) ) {
            $email_body = $email_msg[osc_current_user_locale()];
        }

        $email_title = array();
        $email_title['en_US'] = sprintf('Job application received at %1$s', $email_txt['company_name']);
        $email_title['es_ES'] = sprintf('Candidato recibido en %1$s', $email_txt['company_name']);
        $email_subject = $email_title['en_US'];
        if( array_key_exists(osc_current_user_locale(), $email_title) ) {
            $email_subject = $email_title[osc_current_user_locale()];
        }

        $params = array(
            'to'       => Params::getParam('yourEmail'),
            'to_name'  => Params::getParam('yourName'),
            'reply_to' => osc_contact_email(),
            'subject'  => $email_subject,
            'body'     => nl2br($email_body)
        );
        osc_sendMail($params);
    }

    function send_notifaction_applicant_to_admin($type = '') {
        $email_txt = array(
            'applicant_name'  => Params::getParam('yourName'),
            'company_url'     => osc_base_url(),
            'company_link'    => sprintf('<a href="%1$s">%2$s</a>', osc_base_url(), osc_page_title()),
            'company_name'    => osc_page_title(),
            'applicant_url'   => osc_admin_render_plugin_url("jobboard/people_detail.php") . '&people=' . View::newInstance()->_get('applicantID'),
            'admin_login_url' => osc_admin_base_url()
        );
        $email_txt['applicant_link']   = sprintf('<a href="%1$s">%1$s</a>', $email_txt['applicant_url']);
        $email_txt['admin_login_link'] = sprintf('<a href="%1$s">%1$s</a>', $email_txt['admin_login_url']);
        switch($type) {
            case('listing'):
                $email_txt['job_offer_title'] = osc_item_title();
                $email_txt['job_offer_link']  = sprintf('<a href="%1$s">%2$s</a>', osc_item_url(), osc_item_title());
                $email_txt['job_offer_url']   = osc_item_url();
            break;
            case('spontaneous'):
                $email_txt['job_offer_title'] = __('spontaneous', 'jobboard');
                $email_txt['job_offer_link']  = sprintf('<a href="%1$s">%2$s</a>', osc_contact_url(), __('spontaneous', 'jobboard'));
                $email_txt['job_offer_url']   = osc_contact_url();
            break;
        }

        $email_msg = array();
        $email_msg['en_US'] = "Dear {$email_txt['company_name']},

A new candidate has just applied for a job offer: {$email_txt['job_offer_link']}.

To view and manage his/her CV, please click here: {$email_txt['applicant_link']}.

Remember that you can access your job board anytime at: {$email_txt['admin_login_url']}

Thank you and regards,
Osclass.com";
        $email_msg['es_ES'] = "Estimado {$email_txt['company_name']},

Un nuevo candidato se ha apuntado a tu oferta de empleo: {$email_txt['job_offer_link']}.

Para consultar y gestionar su currículo haz click aquí: {$email_txt['applicant_link']}.

Recuerda que puedes acceder a tu job board desde la dirección: {$email_txt['admin_login_url']}

Gracias,
Osclass.com";
        $email_body = $email_msg['en_US'];
        if( array_key_exists(osc_current_user_locale(), $email_msg) ) {
            $email_body = $email_msg[osc_current_user_locale()];
        }

        $email_title = array();
        $email_title['en_US'] = 'A new candidate has applied to a job offer';
        $email_title['es_ES'] = 'Se ha apuntado un nuevo candidato a una oferta de empleo';
        $email_subject = $email_title['en_US'];
        if( array_key_exists(osc_current_user_locale(), $email_title) ) {
            $email_subject = $email_title[osc_current_user_locale()];
        }

        $params = array(
            'to'       => osc_contact_email(),
            'to_name'  => osc_page_title(),
            'subject'  => $email_subject,
            'body'     => nl2br($email_body)
        );
        osc_sendMail($params);
    }

    // End of file: ./jobboard/email.php