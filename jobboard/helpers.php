<?php

/**
 * Managed jobboard, means admin can activate/block/spam/premium a vacancy
 *
 * definde('JB_NOT_MANAGED', 'foobar')
 *
 * @return type
 */
function jb_is_managed() {
    if(JB_NOT_MANAGED) {
        return false;
    }
    return true;
}

/*  ------------------------------------------------------------------------   */

/*
 * Set default settings
 */
function default_settings_jobboard() {
    // always active osc_item_attachment
    if( !osc_item_attachment() ) {
        osc_set_preference('item_attachment', true);
    }
    if( osc_price_enabled_at_items() ) {
        osc_set_preference('enableField#f_price@items', false);
    }
    if( osc_images_enabled_at_items() ) {
        osc_set_preference('enableField#images@items', false);
    }
    if( osc_max_images_per_item() > 0 ) {
        osc_set_preference('numImages@items', 0);
    }
    //reset preferences
    osc_reset_preferences();

    if(Params::getParam('page')=='items' && Params::getParam('action')=='post') {
        Session::newInstance()->_setForm('contactName', osc_page_title());
        Session::newInstance()->_setForm('contactEmail', osc_contact_email());

        Session::newInstance()->_setForm('country', osc_get_preference('country', 'jobboard'));
        Session::newInstance()->_setForm('countryId', osc_get_preference('countryId', 'jobboard'));
        Session::newInstance()->_setForm('region', osc_get_preference('region', 'jobboard'));
        Session::newInstance()->_setForm('regionId', osc_get_preference('regionId', 'jobboard'));
        Session::newInstance()->_setForm('city', osc_get_preference('city', 'jobboard'));
        Session::newInstance()->_setForm('cityId', osc_get_preference('cityId', 'jobboard'));

    }
}
osc_add_hook('init_admin', 'default_settings_jobboard');
/*  ------------------------------------------------------------------------   */

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
 * Redirect to function via JS
 *
 * @param string $url
 */
function job_js_redirect_to($url) { ?>
    <script type="text/javascript">
        window.location = "<?php echo $url; ?>"
    </script>
<?php }

/**
 * Show dropdown punctuation in html code
 *
 * @param type $questionId
 * @param type $answerId
 * @param type $default
 */
function _punctuationSelect_insert( $questionId, $answerId, $default = '', $disabled = false) {
    _punctuationSelect(true , $questionId, $answerId, $default, $disabled);
}
function _punctuationSelect_update( $questionId, $answerId, $default = '', $disabled = false) {
    _punctuationSelect(false, $questionId, $answerId, $default, $disabled);
}
function _punctuationSelect($new, $questionId, $answerId, $default = '', $disabled = false) {
    $aux = 'answer_punct';
    if($new) {
        $aux = 'new_answer_punct';
    }
    ?><select <?php if($disabled){ ?>disabled="disabled"<?php } ?>name="question[<?php echo $questionId;?>][<?php echo $aux;?>][<?php echo $answerId;?>]" class="select-box-medium">
        <option value="" <?php if($default==''){ echo 'selected'; } ?>><?php _e('Punctuation', 'jobboard'); ?></option>
        <option value="reject" <?php if($default=='reject'){ echo 'selected'; } ?>><?php _e('Reject', 'jobboard'); ?></option>
        <option value="1" <?php if($default=='1'){ echo 'selected'; } ?>>1</option>
        <option value="2" <?php if($default=='2'){ echo 'selected'; } ?>>2</option>
        <option value="3" <?php if($default=='3'){ echo 'selected'; } ?>>3</option>
        <option value="4" <?php if($default=='4'){ echo 'selected'; } ?>>4</option>
        <option value="5" <?php if($default=='5'){ echo 'selected'; } ?>>5</option>
        <option value="6" <?php if($default=='6'){ echo 'selected'; } ?>>6</option>
        <option value="7" <?php if($default=='7'){ echo 'selected'; } ?>>7</option>
        <option value="8" <?php if($default=='8'){ echo 'selected'; } ?>>8</option>
        <option value="9" <?php if($default=='9'){ echo 'selected'; } ?>>9</option>
        <option value="10" <?php if($default=='10'){ echo 'selected'; } ?>>10</option>
    </select>
    <?php
}

/**
 * Return rating html code given a rating to represent.
 *
 * @param type $applicantId
 * @param type $rating
 * @return string
 */
function jobboard_rating($applicantId, $rating = 0) {
    $str = '<span class="rating" id="rating_'.$applicantId.'" rating="'.$rating.'">';
    for($k=1;$k<=5;$k++) {
        $str .= '<a href="#" class="star" star="'.$k.'" id="rating_'.$applicantId.'_'.$k.'" ><img src="'.osc_base_url().'oc-content/plugins/'.osc_plugin_folder(__FILE__).'img/'.($k<=$rating?'fullstar.png':'emptystar.png').'"/></a>';
    }
    $str .= '</span>';
    return $str;
}

/**
 * Return array with posible status
 *
 * @return type
 */
function jobboard_status() {
    $status_array = array();
    $status_array[0] = __("Active", "jobboard");
    $status_array[1] = __("Interview", "jobboard");
    $status_array[2] = __("Rejected", "jobboard");
    $status_array[3] = __("Hired", "jobboard");
    return $status_array;
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