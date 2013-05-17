<?php if (!defined('OC_ADMIN') || OC_ADMIN!==true) exit('Access is not allowed.');

    $dao_preference = new Preference() ;
    $webid          = Params::getParam('webid') ;
    $option         = Params::getParam('option') ;
    
    if( $option == 'stepone' ) {
        $dao_preference->update(array("s_value"   => $webid)
                               ,array("s_section" => "plugin-yandex_metrica", "s_name" => "yandex_metrica_id")) ;
        echo '<div style="text-align:center; font-size:22px; background-color:#00bb00;"><p>' . __('Congratulations. The plugin is now configured', 'yandex_metrica') . '.</p></div>' ;
    } else {
        $webid = osc_yandex_metrica_id() ;
    }

?>

<form action="<?php osc_admin_base_url(true); ?>" method="get">
    <input type="hidden" name="page" value="plugins" />
    <input type="hidden" name="action" value="renderplugin" />
    <input type="hidden" name="file" value="yandex_metrica/admin.php" />
    <input type="hidden" name="option" value="stepone" />
    
    <div>
        <p>
            <?php _e('Please enter your Yandex.Metrika', 'yandex_metrica') ; ?> <label for="id" style="font-weight: bold;"><?php _e('key', 'yandex_metrica') ; ?></label>: <input type="text" name="webid" id="webid" value="<?php echo $webid; ?>" /> <input type="submit" value="<?php _e('Save', 'yandex_metrica') ; ?>" />
        </p>
        <bp>
            <?php _e('You can get your id here <a href="http://metrika.yandex.ru/">Yandex.Metrica</a>', 'yandex_metrica') ; ?>
        </p>
    </div>
</form>
