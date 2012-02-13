<?php 
View::newInstance()->_exportVariableToView('page', Page::newInstance()->findByInternalName('lopd'));
?>
<input type="checkbox" id="lopd_box" name="lopd_box" value="1"/>
<label for="lopd_box"><?php echo sprintf(__('Acepto la <a href="%s" >política de privacidad</a> de la web', 'lopd'), osc_static_page_url()) ; ?></label>
<br />

<script>
    $(document).ready(function(){
        $("#lopd_box").rules("add", {required: true, messages: { required: "<?php _e('Es obligatorio aceptar la política de privacidad', 'lopd'); ?>" }});
    }); 
</script>