<?php
    $relations = array('HIRE' =>  __('Hire someone', 'jobs_attributes') , 'LOOK' => __('Looking for a job', 'jobs_attributes'));
    $index = trim(@$detail['e_relation']);
?>

<h3 style="margin-left: 40px;margin-top: 20px;"><?php _e('Job attributes', 'jobs_attributes'); ?></h3>

        <p><label style="font-weight:bold;"  for="relation"><?php _e('Relation', 'jobs_attributes'); ?></label><br/>
        &nbsp;&nbsp;<?php echo @$relations[$index]; ?></p>
    
    <?php if(isset($detail['s_company_name']) && $detail['s_company_name']!='') { ?>
    
        <p><label style="font-weight:bold;"  for="companyName"><?php _e('Company name', 'jobs_attributes'); ?></label><br/>
        &nbsp;&nbsp;<?php echo @$detail['s_company_name']; ?></p>
    
    <?php }; ?>
    <?php if(isset($detail['e_position_type']) && $detail['e_position_type']!='') { ?>
    
        <p><label style="font-weight:bold;" for="positionType"><?php _e('Position type', 'jobs_attributes'); ?></label><br/>
        &nbsp;&nbsp;<?php echo @$detail['e_position_type']; ?></p>
    
    <?php }; ?>
    <?php if((isset($detail['i_salary_min']) && $detail['i_salary_min']!='') || (isset($detail['i_salary_max']) && $detail['i_salary_max']!='')) { ?>
    
        <p><label style="font-weight:bold;" for="salaryRange"><?php _e('Salary range', 'jobs_attributes'); ?></label><br/>
        &nbsp;&nbsp;<?php echo @$detail['i_salary_min']; ?> - <?php echo @$detail['i_salary_max']; ?> <?php echo osc_currency();?>/<?php echo @$detail['e_salary_period']; ?></p>
    
    <?php }; ?>

    <?php $locale = osc_current_user_locale();?>
        <br />
        <?php if(isset($detail['locale'][$locale]['s_desired_exp']) && $detail['locale'][$locale]['s_desired_exp']!='') { ?>
        <p>
            <label style="font-weight:bold;" for="desired_exp"><?php _e('Desired experience', 'jobs_attributes'); ?></label><br />
            <p style="padding-left: 10px;"><?php echo @$detail['locale'][$locale]['s_desired_exp']; ?></p>
        </p>
        <?php }; ?>
        <?php if(isset($detail['locale'][$locale]['s_studies']) && $detail['locale'][$locale]['s_studies']!='') { ?>
        <p>
            <label style="font-weight:bold;" for="studies"><?php _e('Studies', 'jobs_attributes'); ?></label><br />
            <p style="padding-left: 10px;"><?php echo @$detail['locale'][$locale]['s_studies']; ?></p>
        </p>
        <?php }; ?>
        <?php if(isset($detail['locale'][$locale]['s_minimum_requirements']) && $detail['locale'][$locale]['s_minimum_requirements']!='') { ?>
        <p>
            <label style="font-weight:bold;" for="min_reqs"><?php _e('Minimum requirements', 'jobs_attributes'); ?></label><br />
            <p style="padding-left: 10px;"><?php echo nl2br( @$detail['locale'][$locale]['s_minimum_requirements'] ) ; ?></p>
        </p>
        <?php }; ?>
        <?php if(isset($detail['locale'][$locale]['s_desired_requirements']) && $detail['locale'][$locale]['s_desired_requirements']!='') { ?>
        <p>
            <label style="font-weight:bold;" for="desired_reqs"><?php _e('Desired requirements', 'jobs_attributes'); ?></label><br />
            <p style="padding-left: 10px;"><?php echo nl2br( @$detail['locale'][$locale]['s_desired_requirements'] ); ?></p>
        </p>
        <?php }; ?>
        <?php if(isset($detail['locale'][$locale]['s_contract']) && $detail['locale'][$locale]['s_contract']!='') { ?>
        <p>
            <label style="font-weight:bold;" for="contract"><?php _e('Contract', 'jobs_attributes'); ?></label><br />
            <p style="padding-left: 10px;"><?php echo @$detail['locale'][$locale]['s_contract']; ?></p>
        </p>
        <?php }; ?>
        <?php if(isset($detail['locale'][$locale]['s_company_description']) && $detail['locale'][$locale]['s_company_description']!='') { ?>
        <p>
            <label style="font-weight:bold;" for="company_desc"><?php _e('Company description', 'jobs_attributes'); ?></label><br />
            <p style="padding-left: 10px;">"<?php echo nl2br( @$detail['locale'][$locale]['s_company_description'] ) ; ?></p>
        </p>
        <?php }; ?>
        
    
    
    <?php if(osc_get_preference('allow_cv_upload', 'jobs_plugin')=='1' && ((osc_get_preference('allow_cv_unreg', 'jobs_plugin')=='1' && !osc_is_web_user_logged_in()) || osc_is_web_user_logged_in())) { ?>
    <br/>
    <div id="cv_uploader">
		<noscript>			
			<p><?php _e('Please enable JavaScript to use cv uploader.', 'jobs_plugin');?></p>
		</noscript>         
	</div>
    <link href="<?php echo osc_plugin_url(__FILE__);?>css/fileuploader.css" rel="stylesheet" type="text/css">    
    <script src="<?php echo osc_plugin_url(__FILE__);?>js/fileuploader.js" type="text/javascript"></script>
    <script>        
        function createUploader(){            
            var uploader = new qq.FileUploader({
                element: document.getElementById('cv_uploader'),
                action: '<?php echo osc_plugin_url(__FILE__);?>cv_uploader.php?id=<?php echo osc_item_id();?>',
                debug: false
            });           
        }
        window.onload = createUploader;     
    </script>
    <?php }; ?>

