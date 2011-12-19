<?php
    $salaryRange = explode(" - ", str_replace(osc_currency(),'',Params::getParam('salaryRange') ) );
    $salaryMin = ($salaryRange[0]!='') ? $salaryRange[0] : job_plugin_salary_min();
    $salaryMax = (isset($salaryRange[1]) && $salaryRange[1]!='') ? $salaryRange[1] : job_plugin_salary_max();
?>
<style type="text/css">
    .right .selector {
        float: right;
    } 
</style>
<script type="text/javascript">
    $(function() {
        $("#salary-range").slider({
            range: true,
            min: <?php echo job_plugin_salary_min();?>,
            max: <?php echo job_plugin_salary_max();?>,
            step: <?php echo job_plugin_salary_step();?>,
            values: [<?php echo $salaryMin;?>, <?php echo $salaryMax;?>],
            slide: function(event, ui) {
                $("#salaryRange").val(ui.values[0] + ' <?php echo osc_currency();?> - ' + ui.values[1] + ' <?php echo osc_currency();?>');
            }
        });            
        $("#salaryRange").val($("#salary-range").slider("values", 0) + ' <?php echo osc_currency();?> - ' + $("#salary-range").slider("values", 1) + ' <?php echo osc_currency();?>');
    });
</script>
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
    <div class="row one_input">
        <h6><?php _e('Salary range', 'jobs_attributes'); ?></h6>
        <div style="height: 20px;">
            <div style="width: 60%;float:left;" class="auto">
                <input type="text" id="salaryRange" name="salaryRange" style="background-color: transparent;border:0; color:#f6931f; font-weight:bold;width: auto;" readonly/>
            </div>
            <div style="width: 40%;float:left;" class="right auto">
                <select name="salaryPeriod" id="salaryPeriod">
                    <option value="HOUR" <?php echo (Params::getParam('salaryPeriod')=='HOUR')?'selected':''; ?>><?php _e('Hour', 'jobs_attributes'); ?></option>
                    <option value="WEEK" <?php echo (Params::getParam('salaryPeriod')=='WEEK')?'selected':''; ?>><?php _e('Week', 'jobs_attributes'); ?></option>
                    <option value="MONTH" <?php echo (Params::getParam('salaryPeriod')=='MONTH')?'selected':''; ?>><?php _e('Month', 'jobs_attributes'); ?></option>
                    <option value="YEAR" <?php echo (Params::getParam('salaryPeriod')=='YEAR')?'selected':''; ?>><?php _e('Year', 'jobs_attributes'); ?></option>
                </select>
            </div>
        </div>
        <br/>
        <div class="slider" >
            <div id="salary-range"></div>
        </div>
    </div>
</fieldset>