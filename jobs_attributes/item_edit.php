<script type="text/javascript">
    $(document).ready(function(){
        $('#plugin-hook input:text, #plugin-hook select, #plugin-hook input:text').uniform();
    });
</script>
<h2><?php _e("Job attributes", 'jobs_attributes');?></h2>
<div class="box">
    <div class="row">
        <label for="relation"><?php _e('Relation', 'jobs_attributes'); ?></label>
    </div>
    <div class="row _20">
        <label for="hire"><?php _e('Hire someone', 'jobs_attributes'); ?></label>
        <input type="radio" name="relation" value="HIRE" id="hire" <?php if( @$detail['e_relation'] == 'HIRE' ) { echo 'checked'; }; ?>/>
    </div>
    <div class="row _20">
        <label for="look"><?php _e('Looking for a job', 'jobs_attributes'); ?></label>
        <input type="radio" name="relation" value="LOOK" id="look" <?php if( @$detail['e_relation'] == 'LOOK' ) { echo 'checked'; }; ?>/>
    </div>
    <div class="row _200">
        <label for="companyName"><?php _e('Company name', 'jobs_attributes'); ?></label>
        <input type="text" name="companyName" value="<?php echo @$detail['s_company_name']; ?>" />
    </div>
    <div class="row _200 auto">
        <label for="positionType"><?php _e('Position type', 'jobs_attributes'); ?></label>
        <select name="positionType" id="positionType">
            <option value="UNDEF" <?php if( @$detail['e_position_type'] == 'UNDEF' ) { echo 'selected'; }; ?>><?php _e('Undefined', 'jobs_attributes'); ?></option>
            <option value="PART" <?php if( @$detail['e_position_type'] == 'UNDEF' ) { echo 'selected'; }; ?>><?php _e('Part time', 'jobs_attributes'); ?></option>
            <option value="FULL" <?php if( @$detail['e_position_type'] == 'FULL' ) { echo 'selected'; }; ?>><?php _e('Full-time', 'jobs_attributes'); ?></option>
        </select>
    </div>
    <div class="row _100 auto">
        <label for="salaryRange"><?php _e('Salary range', 'jobs_attributes'); ?></label>
        <input type="text" name="salaryMin" value="0" size="7" maxlength="6" /> - <input type="text" name="salaryMax" value="0" size="7" maxlength="6" />
        <select name="salaryPeriod" id="salaryPeriod">
            <option value="HOUR" <?php if(@$detail['e_salary_period']=='HOUR') { echo 'selected'; }; ?>><?php _e('Hour', 'jobs_attributes'); ?></option>
            <option value="WEEK" <?php if(@$detail['e_salary_period']=='WEEK') { echo 'selected'; }; ?>><?php _e('Week', 'jobs_attributes'); ?></option>
            <option value="MONTH" <?php if(@$detail['e_salary_period']=='MONTH') { echo 'selected'; }; ?>><?php _e('Month', 'jobs_attributes'); ?></option>
            <option value="YEAR" <?php if(@$detail['e_salary_period']=='YEAR') { echo 'selected'; }; ?>><?php _e('Year', 'jobs_attributes'); ?></option>
        </select>
    </div>

<?php
    $locales = osc_get_locales();
    if(count($locales)==1) {
        $locale = $locales[0];
?>
        <div class="row">
            <label for="desired_exp"><?php _e('Desired experience', 'jobs_attributes'); ?></label>
            <input type="text" name="<?php echo @$locale['pk_c_code']; ?>#desired_exp" id="desired_exp" value="<?php echo @$detail['locale'][$locale['pk_c_code']]['s_desired_exp']; ?>" />
        </div>
        <div class="row">
            <label for="studies"><?php _e('Studies', 'jobs_attributes'); ?></label>
            <input type="text" name="<?php echo @$locale['pk_c_code']; ?>#studies" id="studies" value="<?php echo  @$detail['locale'][$locale['pk_c_code']]['s_studies']; ?>" />
        </div>
        <div class="row">
            <label for="min_reqs"><?php _e('Minimum requirements', 'jobs_attributes'); ?></label>
            <textarea name="<?php echo @$locale['pk_c_code']; ?>#min_reqs" id="min_reqs" ><?php echo @$detail['locale'][$locale['pk_c_code']]['s_minimum_requirements']; ?></textarea>
        </div>
        <div class="row">
            <label for="desired_reqs"><?php _e('Desired requirements', 'jobs_attributes'); ?></label>
            <textarea name="<?php echo @$locale['pk_c_code']; ?>#desired_reqs" id="desired_reqs" ><?php echo @$detail['locale'][$locale['pk_c_code']]['s_desired_requirements']; ?></textarea>
        </div>
        <div class="row">
            <label for="contract"><?php _e('Contract', 'jobs_attributes'); ?></label>
            <input type="text" name="<?php echo @$locale['pk_c_code']; ?>#contract" id="contract"  value="<?php echo @$detail['locale'][$locale['pk_c_code']]['s_contract']; ?>" />
        </div>
        <div class="row">
            <label for="company_desc"><?php _e('Company description', 'jobs_attributes'); ?></label>
            <textarea name="<?php echo @$locale['pk_c_code']; ?>#company_desc" id="company_desc" ><?php echo @$detail['locale'][$locale['pk_c_code']]['s_company_description']; ?></textarea>
        </div>
    <?php } else { ?>
        <div class="tabber">
        <?php foreach($locales as $locale) {?>
            <div class="tabbertab">
                <h2><?php echo $locale['s_name']; ?></h2>
                <div class="row">
                    <label for="desired_exp"><?php _e('Desired experience', 'jobs_attributes'); ?></label>
                    <input type="text" name="<?php echo @$locale['pk_c_code']; ?>#desired_exp" id="desired_exp" value="<?php echo @$detail['locale'][$locale['pk_c_code']]['s_desired_exp']; ?>" />
                </div>
                <div class="row">
                    <label for="studies"><?php _e('Studies', 'jobs_attributes'); ?></label>
                    <input type="text" name="<?php echo @$locale['pk_c_code']; ?>#studies" id="studies" value="<?php echo @$detail['locale'][$locale['pk_c_code']]['s_studies']; ?>" />
                </div>
                <div class="row">
                    <label for="min_reqs"><?php _e('Minimum requirements', 'jobs_attributes'); ?></label>
                    <textarea name="<?php echo @$locale['pk_c_code']; ?>#min_reqs" id="min_reqs" ><?php echo @$detail['locale'][$locale['pk_c_code']]['s_minimum_requirements']; ?></textarea>
                </div>
                <div class="row">
                    <label for="desired_reqs"><?php _e('Desired requirements', 'jobs_attributes'); ?></label>
                    <textarea name="<?php echo @$locale['pk_c_code']; ?>#desired_reqs" id="desired_reqs" ><?php echo @$detail['locale'][$locale['pk_c_code']]['s_desired_requirements']; ?></textarea>
                </div>
                <div class="row">
                    <label for="contract"><?php _e('Contract', 'jobs_attributes'); ?></label>
                    <input type="text" name="<?php echo @$locale['pk_c_code']; ?>#contract" id="contract" value="<?php echo @$detail['locale'][$locale['pk_c_code']]['s_contract']; ?>" />
                </div>
                <div class="row">
                    <label for="company_desc"><?php _e('Company description', 'jobs_attributes'); ?></label>
                    <textarea name="<?php echo @$locale['pk_c_code']; ?>#company_desc" id="company_desc"><?php echo @$detail['locale'][$locale['pk_c_code']]['s_company_description']; ?></textarea>
                </div>
                <div style="clear:both;"></div>
            </div>
        <?php }; ?>
        </div>
<?php }; ?>


