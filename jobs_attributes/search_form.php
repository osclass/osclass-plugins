<fieldset>
    <h3><?php _e("Job attributes", 'jobs_attributes');?></h3>
    
    <div class="row one_input">
        <h6><?php _e('Relation', 'jobs_attributes'); ?></h6>
        <ul>
            <li>
                <input style="width: 20px;" type="radio" name="relation" value="HIRE" id="hire" /> <label for="hire"><?php _e('Hire someone', 'jobs_attributes'); ?></label>
            </li>
            <li>
                <input style="width: 20px;" type="radio" name="relation" value="LOOK" id="look" /> <label for="look"><?php _e('Looking for a job', 'jobs_attributes'); ?></label>
            </li>
        </ul>
    </div>
    <div class="row one_input">
        <h6><?php _e('Company name', 'jobs_attributes'); ?></h6>
        <input type="text" name="companyName" value="" />
    </div>
    <div class="row one_input">
        <h6><?php _e('Position type', 'jobs_attributes'); ?></h6>
        <div class="auto">
            <select name="positionType" id="positionType">
                <option value="UNDEF"><?php _e('Undefined', 'jobs_attributes'); ?></option>
                <option value="FULL"><?php _e('Full-time', 'jobs_attributes'); ?></option>
                <option value="PART"><?php _e('Part time', 'jobs_attributes'); ?></option>
            </select>
        </div>
    </div>
    <div class="row two_input">
        <h6><?php _e('Salary range', 'jobs_attributes'); ?></h6>
        <input type="text" name="salaryMin" value="0" size="7" maxlength="6" /> - <input type="text" name="salaryMax" value="0" size="7" maxlength="6" />
        <div class="auto">
            <select name="salaryPeriod" id="salaryPeriod">
                <option value="HOUR"><?php _e('Hour', 'jobs_attributes'); ?></option>
                <option value="WEEK"><?php _e('Week', 'jobs_attributes'); ?></option>
                <option value="MONTH"><?php _e('Month', 'jobs_attributes'); ?></option>
                <option value="YEAR"><?php _e('Year', 'jobs_attributes'); ?></option>
            </select>
        </div>
    </div>
</fieldset>