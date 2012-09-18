<?php

if(!function_exists('jobboard_attributes_array')){
    function jobboard_attributes_array(){
        $detail = ModelJB::newInstance()->getJobsAttrByItemId(osc_item_id());
        $descriptions = ModelJB::newInstance()->getJobsAttrDescriptionsByItemId(osc_item_id());
        $detail['locale'] = array();
        foreach ($descriptions as $desc) {
            $detail['locale'][$desc['fk_c_locale_code']] = $desc;
        }
        return $detail;
    }
}