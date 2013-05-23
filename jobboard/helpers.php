<?php

if(!function_exists('jobboard_attributes_array')) {
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

/**
 * Return sex string given input value
 *
 * @param string $sex
 * @return string
 */
function jobboard_sex_to_string($sex) {
    // array sex
    $aSex = _jobboard_get_sex_array();
    return $aSex[$sex];
}

/**
 * Return array with value and name,
 * used for init dropdown data
 *
 * @return array
 */
function get_jobboard_position_types() {
    $position_types = array(
        'UNDEF' => __('Undefined', 'jobboard'),
        'PART'  => __('Part time', 'jobboard'),
        'FULL'  => __('Full time', 'jobboard')
    );
    return $position_types;
}

if(!function_exists('_jobboard_time_elapsed_string')) {
    /**
     * Return elapsed time from now
     *
     * @param type $ptime
     * @return string
     */
    function _jobboard_time_elapsed_string($ptime) {
        $etime = time() - $ptime;

        if ($etime < 1) {
            return '0 '.__('seconds', 'jobboard');
        }

        $a = array( 12 * 30 * 24 * 60 * 60  =>  __('year', 'jobboard'),
                    30 * 24 * 60 * 60       =>  __('month', 'jobboard'),
                    24 * 60 * 60            =>  __('day', 'jobboard'),
                    60 * 60                 =>  __('hour', 'jobboard'),
                    60                      =>  __('minute', 'jobboard'),
                    1                       =>  __('second', 'jobboard')
                    );

        foreach ($a as $secs => $str) {
            $d = $etime / $secs;
            if ($d >= 1) {
                $r = round($d);
                return $r . ' ' . $str . ($r > 1 ? 's' : '');
            }
        }
    }
}

if(!function_exists('_jobboard_get_age')) {
    /**
     * Returns the age given a birthday date
     *
     * @param type $birthday
     * @return int
     */
    function _jobboard_get_age($birthday){

        if($birthday!='' && $birthday!='0000-00-00') {
            list($year,$month,$day) = explode("-",$birthday);
            $year_diff  = date("Y") - $year;
            $month_diff = date("m") - $month;
            $day_diff   = date("d") - $day;
            if ($day_diff < 0 || $month_diff < 0)
              $year_diff--;
            return $year_diff;
        } else {
            return '-';
        }
    }
}

if(!function_exists('_jobboard_get_sex_array')) {
    /**
     * Used to create gender dropbox
     *
     * @return array
     */
    function _jobboard_get_sex_array() {
        // array sex
        $aSex = array(
            'male'         => __('Male', 'jobboard'),
            'female'       => __('Female', 'jobboard'),
            'prefernotsay' => __('Prefer not say', 'jobboard')
        );
        return $aSex;
    }
}