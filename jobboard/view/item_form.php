<h2><?php _e('Job attributes', 'jobboard');?></h2>
<div class="row input">
    <label for="positionType"><?php _e('Position type', 'jobboard') ; ?></label>
    <select name="positionType" id="positionType">
        <option value="UNDEF" <?php if( get_position_type( $detail ) == 'UNDEF' ) { echo 'selected' ; } ?>><?php _e( 'Undefined', 'jobboard' ) ; ?></option>
        <option value="PART" <?php if( get_position_type( $detail ) == 'PART' ) { echo 'selected' ; } ?>><?php _e( 'Part-time', 'jobboard' ) ; ?></option>
        <option value="FULL" <?php if( get_position_type( $detail ) == 'FULL' ) { echo 'selected' ; } ?>><?php _e( 'Full-time', 'jobboard' ) ; ?></option>
    </select>
</div>
<div class="row input">
    <label for="salary"><?php _e('Salary', 'jobboard') ; ?></label>
    <input type="text" id="salary" name="salary" value="<?php echo get_salary( $detail ) ; ?>" />
</div>
<?php
    $aLocales = osc_get_locales() ;
    if( count($aLocales) == 1 ) {
        $locale = $aLocales[0] ;
?>
        <div class="row input">
            <label for="contract"><?php _e('Contract', 'jobboard') ; ?></label>
            <input type="text" id="contract" name="contract[<?php echo $locale['pk_c_code'] ; ?>]" value="<?php echo get_contract( $detail, $locale['pk_c_code'] ) ; ?>" />
        </div>
        <div class="row input">
            <label for="studies"><?php _e('Studies', 'jobboard') ; ?></label>
            <input type="text" id="studies" name="studies[<?php echo $locale['pk_c_code'] ; ?>]" value="<?php echo get_studies( $detail, $locale['pk_c_code'] ) ; ?>" />
        </div>
        <div class="row input">
            <label for="experience"><?php _e('Experience', 'jobboard') ; ?></label>
            <input type="text" id="experience" name="experience[<?php echo $locale['pk_c_code'] ; ?>]" value="<?php echo get_experience( $detail, $locale['pk_c_code'] ) ; ?>" />
        </div>
        <div class="row input">
            <label for="requirements"><?php _e('Requirements', 'jobboard') ; ?></label>
            <input type="text" id="requirements" name="requirements[<?php echo $locale['pk_c_code'] ; ?>]" value="<?php echo get_requirements( $detail, $locale['pk_c_code'] ) ; ?>" />
        </div>
    <?php } else { ?>
        <div class="tabber">
        <?php foreach($locales as $locale) {?>
            <div class="tabbertab">
                <h2><?php echo $locale['s_name']; ?></h2>
                <div class="row">
                    <?php
                        if( Session::newInstance()->_getForm('pj_data') != "" ) {
                            $data = Session::newInstance()->_getForm('pj_data');
                            $detail['locale'][$locale['pk_c_code']]['s_desired_exp'] = $data[$locale['pk_c_code']]['desired_exp'];
                        }
                    ?>
                    <label for="desired_exp"><?php _e('Desired experience', 'jobs_attributes'); ?></label>
                    <input type="text" name="<?php echo @$locale['pk_c_code']; ?>#desired_exp" id="desired_exp" value="<?php echo @$detail['locale'][$locale['pk_c_code']]['s_desired_exp']; ?>" />
                </div>
                <div class="row">
                    <?php
                        if( Session::newInstance()->_getForm('pj_data') != "" ) {
                            $data = Session::newInstance()->_getForm('pj_data');
                            $detail['locale'][$locale['pk_c_code']]['s_studies'] = $data[$locale['pk_c_code']]['studies'];
                        }
                    ?>
                    <label for="studies"><?php _e('Studies', 'jobs_attributes'); ?></label>
                    <input type="text" name="<?php echo @$locale['pk_c_code']; ?>#studies" id="studies" value="<?php echo @$detail['locale'][$locale['pk_c_code']]['s_studies']; ?>" />
                </div>
                <div class="row">
                    <?php
                        if( Session::newInstance()->_getForm('pj_data') != "" ) {
                            $data = Session::newInstance()->_getForm('pj_data');
                            $detail['locale'][$locale['pk_c_code']]['s_minimum_requirements'] = $data[$locale['pk_c_code']]['min_reqs'];
                        }
                    ?>
                    <label for="min_reqs"><?php _e('Minimum requirements', 'jobs_attributes'); ?></label>
                    <textarea name="<?php echo @$locale['pk_c_code']; ?>#min_reqs" id="min_reqs" ><?php echo @$detail['locale'][$locale['pk_c_code']]['s_minimum_requirements']; ?></textarea>
                </div>
                <div class="row">
                    <?php
                        if( Session::newInstance()->_getForm('pj_data') != "" ) {
                            $data = Session::newInstance()->_getForm('pj_data');
                            $detail['locale'][$locale['pk_c_code']]['s_desired_requirements'] = $data[$locale['pk_c_code']]['desired_reqs'];
                        }
                    ?>
                    <label for="desired_reqs"><?php _e('Desired requirements', 'jobs_attributes'); ?></label>
                    <textarea name="<?php echo @$locale['pk_c_code']; ?>#desired_reqs" id="desired_reqs" ><?php echo @$detail['locale'][$locale['pk_c_code']]['s_desired_requirements']; ?></textarea>
                </div>
                <div class="row">
                    <?php
                        if( Session::newInstance()->_getForm('pj_data') != "" ) {
                            $data = Session::newInstance()->_getForm('pj_data');
                            $detail['locale'][$locale['pk_c_code']]['s_contract'] = $data[$locale['pk_c_code']]['contract'];
                        }
                    ?>
                    <label for="contract"><?php _e('Contract', 'jobs_attributes'); ?></label>
                    <input type="text" name="<?php echo @$locale['pk_c_code']; ?>#contract" id="contract" value="<?php echo @$detail['locale'][$locale['pk_c_code']]['s_contract']; ?>" />
                </div>
                <div class="row">
                    <?php
                        if( Session::newInstance()->_getForm('pj_data') != "" ) {
                            $data = Session::newInstance()->_getForm('pj_data');
                            $detail['locale'][$locale['pk_c_code']]['s_company_description'] = $data[$locale['pk_c_code']]['company_desc'];
                        }
                    ?>
                    <label for="company_desc"><?php _e('Company description', 'jobs_attributes'); ?></label>
                    <textarea name="<?php echo @$locale['pk_c_code']; ?>#company_desc" id="company_desc"><?php echo @$detail['locale'][$locale['pk_c_code']]['s_company_description']; ?></textarea>
                </div>
                <div style="clear:both;"></div>
            </div>
        <?php } ?>
        </div>
<?php } ?>
<script type="text/javascript">
    tabberAutomatic();
</script>