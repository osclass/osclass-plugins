<div>
    <h2><?php _e('Create Ad', 'ads4osc'); ?></h2>
    <form action="<?php echo osc_admin_render_plugin_url("ads4osc/launcher.php");?>" method="post" id="adv-form" enctype="multipart/form-data">
        <input type="hidden" name="ads-action" id="ads-action" value="import">
        <div>
            <h3><?php _e('Step 1: Import Your Ad Code', 'ads4osc'); ?></h3>
            <p><?php _e('Simply <strong>paste your Ad Code below</strong> and Import!', 'ads4osc'); ?></p>
            <label><?php _e('Code', 'ads4osc'); ?></label><br />
            <textarea rows="8" cols="60" name="ads-code" tabindex="6"></textarea>
            <p style="font-size:small;color:gray;"><?php _e('Ads 4 OSClass will detect if the code is from a supported ad network (currently only Google Adsense)', 'ads4osc'); ?>.</p>
            <div>
                <input type="submit" value="<?php _e('Import', 'ads4osc'); ?>" />
            </div>
            <div class="clear"></div>
        </div>
    </form>
</div>