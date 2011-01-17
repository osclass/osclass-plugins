<?php

    $preference = new Preference() ;
    $preferences = $preference->toArray('social_connect');
    if(isset($_REQUEST['fbc_appId'])) {
        $fbc_appId = $_REQUEST['fbc_appId'] ;
    } else {
        $fbc_appId = isset($preferences['fbc_appId']) ? $preferences['fbc_appId'] : '' ;
    }
    if(isset($_REQUEST['fbc_secret'])) {
        $fbc_secret = $_REQUEST['fbc_secret'] ;
    } else {
        $fbc_secret = isset($preferences['fbc_secret']) ? $preferences['fbc_secret'] : '' ;
    }
    
    if ( isset($_REQUEST['option']) && $_REQUEST['option'] == 'stepone' ) 
    {
        $preference->update(array("s_value" => $fbc_appId), array("s_section" => "social_connect", "s_name" => "fbc_appId")) ;
        $preference->update(array("s_value" => $fbc_secret), array("s_section" => "social_connect", "s_name" => "fbc_secret")) ;
        echo '<div><p>Congratulations. The plugin is now configured.</p></div>';
    }
    unset($dao_preference) ;
    
?>

<form action="plugins.php" method="post">
    <input type="hidden" name="action" value="renderplugin" />
    <input type="hidden" name="file" value="social_connect/conf.php" />
    <input type="hidden" name="option" value="stepone" />
    <div>
        Please enter your Facebook <label for="key" style="font-weight: bold;">appId and secret*</label>:<br />
        <label>appId:</label> <input type="text" name="fbc_appId" id="fbc_appId" value="<?php echo $fbc_appId; ?>" maxlength="100" size="60" /><br />
        <label>secret:</label> <input type="text" name="fbc_secret" id="fbc_secret" value="<?php echo $fbc_secret; ?>" maxlength="100" size="60" /><br />
        <input type="submit" value="<?php echo __('Save'); ?>" />
    </div>
</form>
<br />
<div style="font-size: small;"><strong>*</strong> <?php echo __('You can freely obtain an appId and secret key after signing up on this URL:'); ?> <a rel="nofollow" target="_blank" href="http://www.facebook.com/developers/createapp.php">http://www.facebook.com/developers/createapp.php</a>.</div>
<br/>
<div>
<fieldset>
<legend><h1><?php _e("Social Connect Help");?></h1></legend>

<h2><?php _e("What is Social Connect Plugin?");?></h2>
<?php _e("Social Connect plugin allows your users to log into your webpage with their Facebook, Twitter and other social networks ID's. External authentication usually requires to create an application key or some sort of verification code, please refer to each case.");?>

<h2><?php _e("Using Facebook login");?></h2>
<?php echo __('You can freely obtain an appId and secret key (needed to use Facebook login on your website) after signing up on this URL:'); ?> <a rel="nofollow" target="_blank" href="http://www.facebook.com/developers/createapp.php">http://www.facebook.com/developers/createapp.php</a><br />
<?php _e("In order to use Facebook login in your website you should include at least one facebook button for login (and logout). To do that place the following code where you want it to appear: ");?><br/>
<pre>
&lt;?php fbc_button(); ?&gt;
</pre>

</fieldset>
</div>

