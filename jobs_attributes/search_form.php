<fieldset>
    <h3><?php _e("Job attributes", 'jobs_attributes'); ?></h3>
    <div class="row one_input">
        <h6><?php _e('Relation', 'jobs_attributes'); ?></h6>
        <div class="auto">
            <select name="relation" id="relation">
                <option value="" <?php echo (Params::getParam('relation')=='')?'selected':''; ?>><?php _e('Undefined', 'jobs_attributes'); ?></option>
                <option value="HIRE" <?php echo (Params::getParam('relation')=='HIRE')?'selected':''; ?>><?php _e('Hire someone', 'jobs_attributes'); ?></option>
                <option value="LOOK" <?php echo (Params::getParam('relation')=='LOOK')?'selected':''; ?>><?php _e('Looking for a job', 'jobs_attributes'); ?></option>
            </select>
        </div>
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
</fieldset>
