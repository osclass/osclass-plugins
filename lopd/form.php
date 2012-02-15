<?php 
View::newInstance()->_exportVariableToView('page', Page::newInstance()->findByInternalName('lopd'));
?>
<input type="checkbox" id="lopd_box" name="lopd_box" value="1"/>
<label for="lopd_box"><?php echo sprintf(__('He leído, entendido y acepto las <a href="%s" >condiciones de uso</a> de <a href="%s" >%s</a>', 'lopd'), osc_static_page_url(), osc_base_url(), osc_base_url()) ; ?></label>
<br />

<script>
    $(document).ready(function(){
        $("#lopd_box").rules("add", {required: true, messages: { required: "<?php _e('Es obligatorio aceptar las condiciones de uso', 'lopd'); ?>" }});
    }); 
</script>