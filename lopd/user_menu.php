<?php
    $user = User::newInstance()->findByPrimaryKey(osc_logged_user_id());
    if(Params::getParam('confirm')=='true' && Params::getParam('s')==$user['s_secret'] && ModelLOPD::newInstance()->couldDelete(osc_logged_user_id())) {
        Log::newInstance()->insertLog('user', 'delete', osc_logged_user_id(), $user['s_email'], 'LOPD Plugin', osc_logged_user_id());
        User::newInstance()->deleteUser(osc_logged_user_id());
        Session::newInstance()->_drop('userId') ;
        Session::newInstance()->_drop('userName') ;
        Session::newInstance()->_drop('userEmail') ;
        Session::newInstance()->_drop('userPhone') ;

        @Cookie::newInstance()->pop('oc_userId') ;
        @Cookie::newInstance()->pop('oc_userSecret') ;
        @Cookie::newInstance()->set();
?>
        <script type="text/javascript">
            window.location = "<?php echo osc_base_url(); ?>"
        </script>
<?php
    } else {

?>


            <div class="content user_account">
                <h1>
                    <strong><?php _e('Borrar cuenta', 'lopd') ; ?></strong>
                </h1>
                <div id="sidebar">
                    <?php echo osc_private_user_menu() ; ?>
                </div>
                <div id="main">
                    <h2><?php _e('Borrar centa', 'lopd'); ?></h2>
                    <?php if(ModelLOPD::newInstantece()->couldDelete(osc_logged_user_id())) { ?>
                        <h3><?php _e('Está a punto de borrar su cuenta, borrará su cuenta de usuario y sus anuncios publicados ¿Desea continuar?', 'lopd'); ?></h3>
                        <a href="<?php echo osc_render_file_url(osc_plugin_folder(__FILE__)."user_menu.php")."&confirm=true&s=".$user['s_secret']; ?>" /><?php _e('Entiendo los riesgos y desde BORRAR MI CUENTA', 'lopd'); ?></a>
                    <?php } else { ?>
                        <h3><?php _e('En estos momentos no puede borrar su cuenta.', 'lopd'); ?></h3>
                        <p><?php echo sprintf(__('Posiblemente tenga algún pago o algún otro asunto pendiente en la web y su cuenta no puede ser borrada en estos momentos. Si aún así desea borrar su cuenta, por favor <a href="">contacte con nosotros</a>.', 'lopd'), osc_contact_url()); ?></p>
                    <?php }; ?>
                </div>
            </div>

<?php }; ?>