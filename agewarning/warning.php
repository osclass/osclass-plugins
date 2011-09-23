<div><p><?php _e('This site contains adult content. Click accept if you want to continue and has the legal age on your country, otherwise click cancel', 'agewarning'); ?></p>
</div>
<div>
    <a href="<?php echo osc_render_file_url(osc_plugin_folder(__FILE__) . 'confirm.php&backto='.Session::newInstance()->_get('agew_backto'));?>"><?php _e('I agree', 'agewarning');?></a> or <a href="http://www.google.com/"><?php _e('Take me out!', 'agewarning');?></a>
</div>