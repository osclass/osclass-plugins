<?php
    if(!osc_is_admin_user_logged_in()) {
        die;
    }

    if(Params::getParam('paction')=='createpack') {
        $params = array(
            'page' => 'ajax'
            ,'action' => 'custom'
            ,'ajaxfile' => osc_plugin_folder(__FILE__)."resumes_request.php"
        );
        osc_doRequest(osc_base_url(), $params);
    }

?>
<h2 class="render-title"><?php _e('Download all the resumes of the applicants', 'jobboard'); ?></h2>
<div class="relative resumes">
    <?php if(Params::getParam('paction')=='createpack') { ?>
        <div class="form-horizontal search-form" style="padding-top: 15px;">
            <div class="grid-system">
                <div class="grid-row">
                    <div class="row-wrapper">
                        <div class="form-row">
                            <p><?php _e('We are packing all the resumes nicely, once we finish we will send you and email. Please, be patient.', 'jobboard'); ?></p>
                        </div>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    <?php } else { ?>
        <form method="get" action="<?php echo osc_admin_base_url(true); ?>" id="shortcut-filters" class="">
            <input type="hidden" name="page" value="plugins">
            <input type="hidden" name="action" value="renderplugin">
            <input type="hidden" name="file" value="jobboard/resume_download.php">
            <input type="hidden" name="paction" value="createpack">
            <div class="form-horizontal search-form" style="padding-top: 15px;">
                <div class="grid-system">
                    <div class="grid-row">
                        <div class="row-wrapper">
                            <div class="form-row">
                                <p><?php _e('Click on the button below to initiate the process to download all the resumes of the applicants.', 'jobboard'); ?></p>
                                <p><?php _e("WARNING: Depending on how much resumes were submitted, the process could take several minutes to finish. An email will be sent after the process with the links to download the resumes. Feel free to close this window, we'll inform you once we finished.", 'jobboard'); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="form-row filters-submit">
                    <input type="submit" class="btn" value="<?php echo osc_esc_html( __('Create package and download resumes', 'jobboard') ) ; ?>">
                </div>
            </div>
        </form>
    <?php }; ?>
</div>