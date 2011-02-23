<h3><?php _e("Job attributes", 'jobs_attributes');?></h3>
<table>
    <tr>
        <td><label for="relation"><?php _e('Relation'); ?></label></td>
        <td>
            <label for="hire"><input type="radio" name="relation" value="HIRE" id="hire" /><?php _e('Hire someone', 'jobs_attributes'); ?></label><br />
            <label for="look"><input type="radio" name="relation" value="LOOK" id="look" /><?php _e('Looking for a job', 'jobs_attributes'); ?></label><br />
	</td>
    </tr>
    <tr>
        <td><label for="companyName"><?php _e('Company name', 'jobs_attributes'); ?></label></td>
        <td><input type="text" name="companyName" value="" /></td>
    </tr>
    <tr>
        <td><label for="positionType"><?php _e('Position type', 'jobs_attributes'); ?></label></td>
        <td>
            <select name="positionType" id="positionType">
                <option value="UNDEF"><?php _e('Undefined', 'jobs_attributes'); ?></option>
                <option value="PART"><?php _e('Part time', 'jobs_attributes'); ?></option>
                <option value="FULL"><?php _e('Full-time', 'jobs_attributes'); ?></option>
            </select>
        </td>
    </tr>
    <tr>
        <td><label for="salaryRange"><?php _e('Salary range', 'jobs_attributes'); ?></label></td>
        <td>
            <input type="text" name="salaryMin" value="0" size="7" maxlength="6" /> - <input type="text" name="salaryMax" value="0" size="7" maxlength="6" />
            <select name="salaryPeriod" id="salaryPeriod">
                <option value="HOUR"><?php _e('Hour', 'jobs_attributes'); ?></option>
                <option value="WEEK"><?php _e('Week', 'jobs_attributes'); ?></option>
                <option value="MONTH"><?php _e('Month', 'jobs_attributes'); ?></option>
                <option value="YEAR"><?php _e('Year', 'jobs_attributes'); ?></option>
            </select>
        </td>
    </tr>
</table>
<?php
    $locales = osc_get_locales();
    if(count($locales)==1) {
        $locale=$locales[0];
?>
        <p>
            <label for="desired_exp"><?php _e('Desired experience', 'jobs_attributes'); ?></label><br />
            <input type="text" name="<?php echo $locale['pk_c_code']; ?>#desired_exp" id="desired_exp" style="width: 100%;" />
        </p>
        <p>
            <label for="studies"><?php _e('Studies', 'jobs_attributes'); ?></label><br />
            <input type="text" name="<?php echo $locale['pk_c_code']; ?>#studies" id="studies" style="width: 100%;" />
        </p>
        <p>
            <label for="min_reqs"><?php _e('Minimum requirements', 'jobs_attributes'); ?></label><br />
            <textarea name="<?php echo $locale['pk_c_code']; ?>#min_reqs" id="min_reqs" style="width: 100%;"></textarea>
        </p>
        <p>
            <label for="desired_reqs"><?php _e('Desired requirements', 'jobs_attributes'); ?></label><br />
            <textarea name="<?php echo $locale['pk_c_code']; ?>#desired_reqs" id="desired_reqs" style="width: 100%;"></textarea>
        </p>
        <p>
            <label for="contract"><?php _e('Contract', 'jobs_attributes'); ?></label><br />
            <input type="text" name="<?php echo $locale['pk_c_code']; ?>#contract" id="contract" style="width: 100%;" />
        </p>
        <p>
            <label for="company_desc"><?php _e('Company description', 'jobs_attributes'); ?></label><br />
            <textarea name="<?php echo $locale['pk_c_code']; ?>#company_desc" id="company_desc" style="width: 100%;"></textarea>
        </p>
<?php } else { ?>
        <div class="tabber">
        <?php foreach($locales as $locale) {?>
            <div class="tabbertab">
                <h2><?php echo $locale['s_name']; ?></h2>
                <p>
                    <label for="desired_exp"><?php _e('Desired experience', 'jobs_attributes'); ?></label><br />
                    <input type="text" name="<?php echo $locale['pk_c_code']; ?>#desired_exp" id="desired_exp" style="width: 100%;" />
                </p>
                <p>
                    <label for="studies"><?php _e('Studies', 'jobs_attributes'); ?></label><br />
                    <input type="text" name="<?php echo $locale['pk_c_code']; ?>#studies" id="studies" style="width: 100%;" />
                </p>
                <p>
                    <label for="min_reqs"><?php _e('Minimum requirements', 'jobs_attributes'); ?></label><br />
                    <textarea name="<?php echo $locale['pk_c_code']; ?>#min_reqs" id="min_reqs" style="width: 100%;"></textarea>
                </p>
                <p>
                    <label for="desired_reqs"><?php _e('Desired requirements', 'jobs_attributes'); ?></label><br />
                    <textarea name="<?php echo $locale['pk_c_code']; ?>#desired_reqs" id="desired_reqs" style="width: 100%;"></textarea>
                </p>
                <p>
                    <label for="contract"><?php _e('Contract', 'jobs_attributes'); ?></label><br />
                    <input type="text" name="<?php echo $locale['pk_c_code']; ?>#contract" id="contract" style="width: 100%;" />
                </p>
                <p>
                    <label for="company_desc"><?php _e('Company description', 'jobs_attributes'); ?></label><br />
                    <textarea name="<?php echo $locale['pk_c_code']; ?>#company_desc" id="company_desc" style="width: 100%;"></textarea>
                </p>
            </div>
        <?php }; ?>
        </div>
<?php }; ?>

<script type="text/javascript">
    tabberAutomatic();
</script>
