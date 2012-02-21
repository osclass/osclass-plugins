<?php 
View::newInstance()->_exportVariableToView('page', Page::newInstance()->findByInternalName('lopd'));
?>
<div id="lopd_page" style="display:none;">
    <?php echo osc_static_page_text(); ?>
</div>
<input type="checkbox" id="lopd_box" name="lopd_box" value="1"/>
<label for="lopd_box"><?php echo sprintf(__('He leído, entendido y acepto las <a id="lopd_a" href="#" >condiciones de uso</a> de <a href="%s" >%s</a>', 'lopd'), osc_base_url(), osc_base_url()) ; ?></label>
<br />

<link href="<?php echo osc_base_url().'oc-content/plugins/'.osc_plugin_folder(__FILE__);?>css/basic.css" rel="stylesheet" type="text/css" />
<link href="<?php echo osc_base_url().'oc-content/plugins/'.osc_plugin_folder(__FILE__);?>css/demo.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="<?php echo osc_base_url().'oc-content/plugins/'.osc_plugin_folder(__FILE__);?>js/jquery.simplemodal.1.4.2.min.js"></script>
<script>
    $(document).ready(function(){
        $("#lopd_box").rules("add", {required: true, messages: { required: "<?php _e('Es obligatorio aceptar las condiciones de uso', 'lopd'); ?>" }});
        $("#lopd_a").click(function(){
            $("#lopd_page").modal();
        });
    }); 
</script>