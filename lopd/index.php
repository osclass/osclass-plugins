<?php
/*
Plugin Name: Ley Organica de Proteccion de Datos
Plugin URI: http://www.osclass.org/
Description: Complete the requirements of the Spanish law for personal data protection (LOPD)
Version: 1.0
Author: OSClass & Berni2201
Author URI: http://www.osclass.org/
Short Name: lopd
*/


    require_once( osc_plugins_path() . 'lopd/ModelLOPD.php' ) ;

    function lopd_install() {
        ModelLOPD::newInstance()->import('lopd/struct.sql') ;
        $aFields = array(
            's_internal_name' => 'lopd',
            'b_indelible' => 0
        );
        $aFieldsDescription = array();
        $locales = OSCLocale::newInstance()->listAll();
        foreach($locales as $locale) {
            $aFieldsDescription[$locale['pk_c_code']] = array(
                's_title' => 'Política de privacidad',
                's_text' => 'Los datos personales de nuestros clientes serán tratados conforme a la Ley 15/1999, del 13 de diciembre, de Protección de Datos de Carácter (LOPD).<br/><br/>Este Reglamento se aplica tanto en caso que usted acceda a nuestro sitio web y decida simplemente navegar por sus contenidos y utilizar sus servicios, sin llegar a contratar ningún servicio, como si, usted accede a nombre del dominio y decide navegar por sus contenidos utilizando los servicios que ofrece y contratando uno o varios servicios.<br/><br/>En virtud de lo dispuesto en la LOPD, le informamos que mediante el rellenado de nuestro formulario sus datos personales quedarán incorporados y serán tratados en los ficheros de titularidad de \XXXXXXXXX\", con el fin de permitir la ejecución de los servicios solicitados y/o enviar material informativo, (así como para mantenerle informado, incluso por medios electrónicos, sobre cuestiones relacionadas a la actividad de la Compañía y sus servicios). Los datos serán transferidos a servidores seguros alojados en territorio europeo.<br/><br/>Usted puede ejercer, en cualquier momento, los derechos de acceso, rectificación, cancelación y oposición de sus datos de carácter personal mediante correo electrónico dirigido a dirección de xxx@tudominios.com  o bien mediante un escrito dirigido a XXXXXXXXXXXXXXXXXX, acompañando siempre una fotocopia de su D.N.I.<br/><br/>En ningún caso, y por ningún motivo, se comunicarán o divulgarán sus datos a terceras partes que no sean parte del proceso de los servicios contratados.<br/><br/>Las cookies que puedan enviarse, no se utilizarán en ningún caso para elaborar perfiles del usuario, sino únicamente para facilitar el uso del sitio y la gestión de los pedidos.<br/><br/>Seguridad<br/><br/><br/>Este sitio adopta medidas de seguridad aptas para proteger de la perdida, el abuso o la alteración de la información bajo nuestro control. Naturalmente, ninguno de los datos de nuestros clientes viene cedido a terceros que no sean parte del proceso de contratación de servicios.<br/>La información financiera del Cliente (como el número de tarjeta de crédito, la fecha de vencimiento, los datos personales) llegarán directamente al Banco encargado de la transacción de pago mediante protocolo criptográfico.<br/><br/>En ningún caso \"xxxxxxxxxxxxxxxxxxx\"  o sujetos terceros podrán tener acceso alguno a dichos datos.<br/><br/>Si tenéis algunas preguntas o dudas sobre nuestra política de seguridad y privacidad o respecto a las practicas realizada en nuestro sitio podéis contactarnos a la siguiente dirección de correo: xxxx@tudominios.com'
            );
        }
        Page::newInstance()->insert($aFields, $aFieldsDescription);
        
        // LOG-OUT every user, so they need to log-in accepting the new terms and conditions
        $tmp['adminId']         = $_SESSION['adminId'];
        $tmp['adminUserName']   = $_SESSION['adminUserName'];
        $tmp['adminName']       = $_SESSION['adminName'];
        $tmp['adminEmail']      = $_SESSION['adminEmail'];
        $tmp['adminLocale']     = $_SESSION['adminLocale'];
        session_destroy();
        $_SESSION['adminId']          = $tmp['adminId'];
        $_SESSION['adminUserName']    = $tmp['adminUserName'];
        $_SESSION['adminName']        = $tmp['adminName'];
        $_SESSION['adminEmail']       = $tmp['adminEmail'];
        $_SESSION['adminLocale']      = $tmp['adminLocale'];
        
    }
    
    function lopd_uninstall() {
        try {
            Page::newInstance()->deleteByInternalName('lopd');
            ModelLOPD::newInstance()->uninstall();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    function lopd_form() {
        include_once 'form.php';
    }


    function lopd_before_register() {
        if(Params::getParam('lopd_box')!='1') {
            osc_add_flash_error_message( __('Debe aceptar la política de privacidad para poder registrarse', 'lopd')) ;
            header('Location: ' . osc_register_account_url());
            exit;
        }
    }
    
    function lopd_save($userId) {
        ModelLOPD::newInstance()->acceptLOPD($userId);
    }
    
    /**
     * Create a new menu option on users' dashboards
     */
    function lopd_user_menu() {
        echo '<li class="opt_lopd" ><a href="' . osc_render_file_url(osc_plugin_folder(__FILE__)."user_menu.php") . '" >' . __("Borrar mi cuenta", "lopd") . '</a></li>' ;
    }
    
    function lopd_delete_user($userId) {
        ModelLOPD::newInstance()->delete(array('fk_i_user_id' => $userId));
    }

    function lopd_admin_menu() {
        echo '<h3><a href="#">Ayuda LOPD</a></h3>
        <ul> 
            <li><a href="' . osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'help.php') . '">&raquo; ' . __('Ayuda', 'lopd') . '</a></li>
            <li><a href="' . osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'admin_user.php') . '">&raquo; ' . __('Administrar usuarios', 'lopd') . '</a></li>
        </ul>';
    }
    
    function lopd_init() {
        if(osc_is_web_user_logged_in() && Rewrite::newInstance()->get_location()!='custom' && Rewrite::newInstance()->get_location()!='page') {
            if(!ModelLOPD::newInstance()->hasAccepted(osc_logged_user_id())) {
                if(Params::getParam('lopd_r')!='no') {
                    header('Location: '.osc_render_file_url(osc_plugin_folder(__FILE__)."accept_lopd.php&lopd_r=no"));
                    exit;
                }
            }
        }
    }
    


    /**
     * ADD HOOKS
     */
    osc_register_plugin(osc_plugin_path(__FILE__), 'lopd_install');
    osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", 'lopd_uninstall');

    // run at registration form
    osc_add_hook('user_register_form', 'lopd_form');
    
    // run ONCE the user is registered
    osc_add_hook('user_register_completed', 'lopd_save');
    osc_add_hook('before_user_register', 'lopd_before_register');

    
    osc_add_hook('user_menu', 'lopd_user_menu');
    osc_add_hook('delete_user', 'lopd_delete_user');

    osc_add_hook('admin_menu', 'lopd_admin_menu');
    osc_add_hook('init', 'lopd_init');
    
?>
