<?php

    $paction = Params::getParam("paction");
    switch($paction) {
        case 'rating':
            ModelJB::newInstance()->setRating(Params::getParam("applicantId"), Params::getParam("rating"));
            break;
        default:
            break;
    }


?>
