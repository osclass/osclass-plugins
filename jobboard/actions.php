<?php

    if(!osc_is_admin_user_logged_in()) {
        die;
    }

    switch(Params::getParam('paction')) {
        case 'delete_applicant':
            ModelJB::newInstance()->deleteApplicant(Params::getParam("id"));
            job_js_redirect_to(osc_admin_render_plugin_url("jobboard/people.php"));
        break;
        default :
            job_js_redirect_to(osc_admin_render_plugin_url("jobboard/dashboard.php"));
        break;
    }
