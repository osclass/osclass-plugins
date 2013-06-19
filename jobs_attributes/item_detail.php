<?php
    $relations = array('HIRE' => __('Hire someone', 'jobs_attributes'), 'LOOK' => __('Looking for a job', 'jobs_attributes'));
    $positions = array('UNDEF' => __('Undefined', 'jobs_attributes'), 'PART' => __('Part time', 'jobs_attributes'), 'FULL' => __('Full-time', 'jobs_attributes'));
    $salary    = array('HOUR' => __('Hour', 'jobs_attributes'), 'DAY' => __('Day', 'jobs_attributes'), 'WEEK' => __('Week', 'jobs_attributes'), 'MONTH' => __('Month', 'jobs_attributes'), 'YEAR' => __('Year', 'jobs_attributes'));
    $index     = trim(@$detail['e_relation']);
    $locale    = osc_current_user_locale();
?>
<h2><?php _e('Job details', 'jobs_attributes'); ?></h2>
<div class="job-detail">
    <table>
        <?php if(@$relations[$index] != "") { ?>
        <tr>
            <td><label for="relation"><?php _e('Relation', 'jobs_attributes'); ?></label></td>
            <td><?php echo @$relations[$index]; ?></td>
        </tr>
        <?php } ?>
        <?php if(@$detail['s_company_name'] != "") { ?>
        <tr>
            <td><label for="companyName"><?php _e('Company name', 'jobs_attributes'); ?></label></td>
            <td><?php echo @$detail['s_company_name']; ?></td>
        </tr>
        <?php } ?>
        <?php if(@$detail['e_position_type'] != "") { ?>
        <tr>
            <td><label for="positionType"><?php _e('Position type', 'jobs_attributes'); ?></label></td>
            <td><?php echo $positions[$detail['e_position_type']]; ?></td>
        </tr>
        <?php } ?>
        <?php if(@$detail['s_salary_text'] != "" ) { ?>
        <tr>
            <td><label for="salaryText"><?php _e('Salary', 'jobs_attributes'); ?></label></td>
            <td><?php echo @$detail['s_salary_text']; ?></td>
        </tr>
        <?php } ?>
    </table>
    <?php if(isset($detail['locale'][$locale]['s_desired_exp']) && $detail['locale'][$locale]['s_desired_exp']!='') { ?>
    <div>
        <label for="desired_exp"><?php _e('Desired experience', 'jobs_attributes'); ?></label>
        <p><?php echo @$detail['locale'][$locale]['s_desired_exp']; ?></p>
    </div>
    <?php } ?>
    <?php if(isset($detail['locale'][$locale]['s_studies']) && $detail['locale'][$locale]['s_studies']!='') { ?>
    <div>
        <label for="studies"><?php _e('Studies', 'jobs_attributes'); ?></label>
        <p><?php echo @$detail['locale'][$locale]['s_studies']; ?></p>
    </div>
    <?php } ?>
    <?php if(isset($detail['locale'][$locale]['s_minimum_requirements']) && $detail['locale'][$locale]['s_minimum_requirements']!='') { ?>
    <div>
        <label for="min_reqs"><?php _e('Minimum requirements', 'jobs_attributes'); ?></label>
        <p><?php echo nl2br( @$detail['locale'][$locale]['s_minimum_requirements'] ) ; ?></p>
    </div>
    <?php } ?>
    <?php if(isset($detail['locale'][$locale]['s_desired_requirements']) && $detail['locale'][$locale]['s_desired_requirements']!='') { ?>
    <div>
        <label for="desired_reqs"><?php _e('Desired requirements', 'jobs_attributes'); ?></label>
        <p><?php echo nl2br( @$detail['locale'][$locale]['s_desired_requirements'] ); ?></p>
    </div>
    <?php } ?>
    <?php if(isset($detail['locale'][$locale]['s_contract']) && $detail['locale'][$locale]['s_contract']!='') { ?>
    <div>
        <label for="contract"><?php _e('Contract', 'jobs_attributes'); ?></label>
        <p><?php echo @$detail['locale'][$locale]['s_contract']; ?></p>
    </div>
    <?php } ?>
    <?php if(isset($detail['locale'][$locale]['s_company_description']) && $detail['locale'][$locale]['s_company_description']!='') { ?>
    <div>
        <label for="company_desc"><?php _e('Company description', 'jobs_attributes'); ?></label>
        <p><?php echo nl2br( @$detail['locale'][$locale]['s_company_description'] ) ; ?></p>
    </div>
    <?php } ?>
</div>
<?php if(osc_get_preference('allow_cv_upload', 'plugin')=='1' && ((osc_get_preference('allow_cv_unreg', 'jobs_plugin')=='1' && !osc_is_web_user_logged_in()) || osc_is_web_user_logged_in())) { ?>
<br/>
<div id="cv_uploader">
    <noscript>
        <p><?php _e('Please enable JavaScript to use cv uploader', 'jobs_attributes'); ?>.</p>
    </noscript>
</div>
<script src="<?php echo osc_plugin_url(__FILE__); ?>js/fileuploader.js" type="text/javascript"></script>
<script>        
    function createUploader(){            
        var uploader = new qq.FileUploader({
            element: document.getElementById('cv_uploader'),
            action: '<?php
                if(osc_version()<320) {
                    echo osc_ajax_plugin_url(osc_plugin_folder(__FILE__) . "cv_uploader.php?id=" . osc_item_id());
                } else {
                    echo osc_route_ajax_url('jobs-attr-cvupload', array('id' => osc_item_id()));
                }; ?>',
            debug: false
        });           
    }
    window.onload = createUploader;     
</script>
<?php } ?>