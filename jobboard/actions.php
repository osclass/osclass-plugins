<?php

    if(!osc_is_admin_user_logged_in()) {
        die;
    }
    
    switch(Params::getParam('paction')) {
        case 'delete_applicant':
            ModelJB::newInstance()->deleteApplicant(Params::getParam("id"));
            job_js_redirect_to(osc_admin_render_plugin_url("jobboard/people.php"));
            break;
        case 'delete_note':
            $id = Params::getParam("id");
            $applicantId = Params::getParam("applicantId");
            ModelJB::newInstance()->deleteNote($id);
            job_js_redirect_to(osc_admin_render_plugin_url("jobboard/people_detail.php&people=".$applicantId));
            break;
        case 'add_note':
            $applicantId = Params::getParam("applicantId");
            $text = Params::getParam("note_text");
            ModelJB::newInstance()->insertNote($applicantId, $text);
            job_js_redirect_to(osc_admin_render_plugin_url("jobboard/people_detail.php&people=".$applicantId));
            break;
        case 'edit_note':
            $id = Params::getParam("id");
            $applicantId = Params::getParam("applicantId");
            $text = Params::getParam("note_text");
            ModelJB::newInstance()->updateNote($id, $text);
            job_js_redirect_to(osc_admin_render_plugin_url("jobboard/people_detail.php&people=".$applicantId));
            break;
        default :
            job_js_redirect_to(osc_admin_render_plugin_url("jobboard/dashboard.php"));
            break;
    }
    
?>
