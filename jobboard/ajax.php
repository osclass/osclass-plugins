<?php

    if(!osc_is_admin_user_logged_in()) {
        die;
    }
    
    $paction = Params::getParam("paction");
    switch($paction) {
        case 'rating':
            ModelJB::newInstance()->setRating(Params::getParam("applicantId"), Params::getParam("rating"));
            break;
        case 'applicant_status':
            ModelJB::newInstance()->changeStatus(Params::getParam("applicantId"), Params::getParam("status"));
            break;
        default:
            break;
    }


?>
