<?php
    if(Params::getParam('confirm')=='true' && osc_is_web_user_logged_in()) {
        ModelLOPD::newInstance()->acceptLOPD(osc_logged_user_id());
?>
        <script type="text/javascript">
            window.location = "<?php echo osc_base_url(); ?>"
        </script>
<?php
    } else {
            View::newInstance()->_exportVariableToView('page', Page::newInstance()->findByInternalName('lopd'));
?>


            <div class="content user_account">
                <h1>
                    <strong><?php _e('Aceptar la política de privacidad', 'lopd') ; ?></strong>
                </h1>
                <div id="main">
                    <h2><?php _e('Aceptar la política de privacidad', 'lopd'); ?></h2>
                        <h3><?php echo sprintf(__('Antes de continuar debe aceptar la <a href="%s">política de privacidad</a>.', 'lopd'), osc_static_page_url()); ?></h3>
                        <a href="<?php echo osc_route_url('lopd-accept', array('confirm' => 'true')); ?>" /><?php _e('He leido y acepto la política de privacidad', 'lopd'); ?></a>
                </div>
            </div>

<?php }; ?>