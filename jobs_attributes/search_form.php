<h3><?php _e("Job attributes", 'jobs_attributes');?></h3>
<table>
    <tr>
        <td><label for="relation"><?php _e('Relation', 'jobs_attributes'); ?></label></td>
        <td>
            <input type="radio" name="relation" value="HIRE" id="hire" /> <label for="hire"><?php _e('Hire someone', 'jobs_attributes'); ?></label><br />
            <input type="radio" name="relation" value="LOOK" id="look" /> <label for="look"><?php _e('Looking for a job', 'jobs_attributes'); ?></label><br />
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
                <option value="FULL"><?php _e('Full-time', 'jobs_attributes'); ?></option>
                <option value="PART"><?php _e('Part time', 'jobs_attributes'); ?></option>
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
