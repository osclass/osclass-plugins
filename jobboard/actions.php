<?php

    if(!osc_is_admin_user_logged_in()) {
        die;
    }

    switch(Params::getParam('paction')) {
        case 'delete_applicant':
            ModelJB::newInstance()->deleteApplicant(Params::getParam("id"));
            job_js_redirect_to(osc_admin_render_plugin_url("jobboard/people.php"));
        break;
        case 'delete_killer_form':
            $result = ModelKQ::newInstance()->removeKillerForm(Params::getParam("id"));
            if($result!==false) {
                osc_add_flash_ok_message(__('Killer form deleted correctly', 'jobboard'), 'admin');
            } else {
                osc_add_flash_message(__('Problems tring to remove killer question form. Try it again.', 'jobboard'), 'admin');
            }
            job_js_redirect_to(osc_admin_render_plugin_url("jobboard/manage_killer.php"));
        break;
        default :
            job_js_redirect_to(osc_admin_render_plugin_url("jobboard/dashboard.php"));
        break;
    }
