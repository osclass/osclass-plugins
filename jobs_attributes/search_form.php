<fieldset>
    <h3><?php _e("Job attributes", 'jobs_attributes');?></h3>
    
    <div class="row one_input">
        <h6><?php _e('Relation', 'jobs_attributes'); ?></h6>
        <ul>
            <li>
                <input style="width: 20px;" type="radio" name="relation" value="HIRE" id="hire" <?php echo (Params::getParam('relation')=='HIRE')?'checked':''; ?>/> <label for="hire"><?php _e('Hire someone', 'jobs_attributes'); ?></label>
            </li>
            <li>
                <input style="width: 20px;" type="radio" name="relation" value="LOOK" id="look" <?php echo (Params::getParam('relation')=='LOOK')?'checked':''; ?>/> <label for="look"><?php _e('Looking for a job', 'jobs_attributes'); ?></label>
            </li>
        </ul>
    </div>
    <div class="row one_input">
        <h6><?php _e('Company name', 'jobs_attributes'); ?></h6>
        <input type="text" name="companyName" value="<?php echo Params::getParam('companyName'); ?>" />
    </div>
    <div class="row one_input">
        <h6><?php _e('Position type', 'jobs_attributes'); ?></h6>
        <div class="auto">
            <select name="positionType" id="positionType">
                <option value="UNDEF" <?php echo (Params::getParam('positionType')=='UNDEF')?'selected':''; ?>><?php _e('Undefined', 'jobs_attributes'); ?></option>
                <option value="FULL" <?php echo (Params::getParam('positionType')=='FULL')?'selected':''; ?>><?php _e('Full-time', 'jobs_attributes'); ?></option>
                <option value="PART" <?php echo (Params::getParam('positionType')=='PART')?'selected':''; ?>><?php _e('Part time', 'jobs_attributes'); ?></option>
            </select>
        </div>
    </div>
    <div class="row two_input">
        <h6><?php _e('Salary range', 'jobs_attributes'); ?></h6>
        <input type="text" name="salaryMin" value="<?php echo (Params::getParam('salaryMin')=='')?'0':Params::getParam('salaryMin'); ?>" size="7" maxlength="6" /> - <input type="text" name="salaryMax" value="<?php echo (Params::getParam('salaryMax')=='')?'0':Params::getParam('salaryMax'); ?>" size="7" maxlength="6" />
        <div class="auto">
            <select name="salaryPeriod" id="salaryPeriod">
                <option value="HOUR" <?php echo (Params::getParam('salaryPeriod')=='HOUR')?'selected':''; ?>><?php _e('Hour', 'jobs_attributes'); ?></option>
                <option value="WEEK" <?php echo (Params::getParam('salaryPeriod')=='WEEK')?'selected':''; ?>><?php _e('Week', 'jobs_attributes'); ?></option>
                <option value="MONTH" <?php echo (Params::getParam('salaryPeriod')=='MONTH')?'selected':''; ?>><?php _e('Month', 'jobs_attributes'); ?></option>
                <option value="YEAR" <?php echo (Params::getParam('salaryPeriod')=='YEAR')?'selected':''; ?>><?php _e('Year', 'jobs_attributes'); ?></option>
            </select>
        </div>
    </div>
</fieldset>