<?php if (!defined('OC_ADMIN') || OC_ADMIN!==true) exit('Access is not allowed.');

    $dao_preference = new Preference() ;
    $webid          = Params::getParam('webid');
    $option         = Params::getParam('option');
    
    if( $option == 'stepone' ) {
        $dao_preference->update(array("s_value" => $webid)
                               ,array("s_section" => "plugin-yandex_maps", "s_name" => "yandex_maps_key")) ;
        echo '<div style="text-align:center; font-size:22px; background-color:#00bb00;"><p>' . __('Congratulations. The plugin is now configured', 'yandex_map') . '.</p></div>' ;
    } else {
        $webid = osc_yandex_map_key() ;
    }

?>

<form action="<?php osc_admin_base_url(true); ?>" method="get">
    <input type="hidden" name="page" value="plugins" />
    <input type="hidden" name="action" value="renderplugin" />
    <input type="hidden" name="file" value="yandex_maps/admin.php" />
    <input type="hidden" name="option" value="stepone" />
    <div>
        <p>
            <?php _e('Please enter your Yandex.Map ', 'yandex_maps'); ?> <label for="id" style="font-weight: bold;"><?php _e('API key', 'yandex_maps'); ?></label>: <input type="text" name="webid" id="webid" value="<?php echo $webid; ?>" /> <input type="submit" value="<?php _e('Save', 'yandex_maps'); ?>" />
        </p>
        <p>
            <?php _e('You can get your API key here <a href="http://api.yandex.ru/maps/">Yandex.Map</a>', 'yandex_maps') ; ?>
        </p>
    </div>
</form>
