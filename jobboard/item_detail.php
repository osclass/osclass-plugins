<?php
    $positions = array('UNDEF' => __('Undefined', 'jobs_attributes'), 'PART' => __('Part time', 'jobs_attributes'), 'FULL' => __('Full-time', 'jobs_attributes'));
    $locale    = osc_current_user_locale();
?>
<h2><?php _e('Job details', 'jobs_attributes'); ?></h2>
<div class="job-detail">
    <table>
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
</div>

<div id="apply_job">
    <form id="job_apply_form" action="<?php echo osc_base_url(true)."?page=custom&file=".osc_plugin_folder(__FILE__)."apply.php" ;?>" method="post" enctype="multipart/form-data" >
        <input type="hidden" name="item_id" value="<?php echo osc_item_id(); ?>" />
        <div id="job_error_list" ></div>
        <div class="row">
            <label for="job_name"><?php _e('Name', 'jobs_attributes'); ?></label>
            <input type="text" id="job_name" name="job_name" value="<?php if(Session::newInstance()->_getForm('pj_job_name')!="") { echo Session::newInstance()->_getForm('pj_job_name'); }; ?>" />
        </div>
        <div class="row">
            <label for="job_email"><?php _e('E-mail', 'jobs_attributes'); ?></label>
            <input type="text" id="job_email" name="job_email" value="<?php if(Session::newInstance()->_getForm('pj_job_email')!="") { echo Session::newInstance()->_getForm('pj_job_email'); }; ?>" />
        </div>
        <div class="row">
            <label for="job_cover_letter"><?php _e('Do you want to tell us something?', 'jobs_attributes'); ?></label>
            <textarea type="text" id="job_cover_letter" name="job_cover_letter"><?php if(Session::newInstance()->_getForm('pj_job_cover_letter')!="") { echo Session::newInstance()->_getForm('pj_job_cover_letter'); }; ?></textarea>
        </div>
        <?php if(osc_get_preference('allow_cv_upload', 'jobboard_plugin')>0) { ?>
            <div id="cv_uploader">
                <noscript>
                    <p><?php _e('Please enable JavaScript to use cv uploader', 'jobs_attributes'); ?>.</p>
                </noscript>
            </div>
            <div class="row">
                <label for="job_resume"><?php _e('Select your resume', 'jobs_attributes'); ?></label>
                <input type="file" name="job_resume[]" />
            </div>
        <?php } ?>
        <input type="submit" value="Apply" />
    </form>

</div>


<script>
    $(document).ready(function(){
        
        $("form[id=job_apply_form]").validate({
            rules: {
                job_email: {
                    required: true,
                    email: true
                },
                job_name: {
                    required: true
                },
                job_cover_letter: {
                    required: false,
                    minlength: 5
                }
            },
            messages: {
                job_email: {
                    required: "<?php _e("Email: this field is required", "jobs_attributes"); ?>.",
                    email: "<?php _e("Invalid email address", "jobs_attributes"); ?>."
                },
                job_name: {
                    required: "<?php _e("Name: this field is required", "jobs_attributes"); ?>."
                },
                job_cover_letter: {
                    minlength: "<?php _e("Cover letter: enter at least 5 characters", "jobs_attributes"); ?>."
                }
            },
            errorLabelContainer: "#job_error_list",
            wrapper: "li",
            invalidHandler: function(form, validator) {
                $('html,body').animate({ scrollTop: $('form').offset().top }, { duration: 250, easing: 'swing'});
            }
        });        
    }); 
</Script>