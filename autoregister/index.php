<?php
/*
Plugin Name: Auto register
Plugin URI: http://www.osclass.org/
Description: Auto register plugin, no register needed to add new listings and if user is not logged or registered, make user registration.
Version: 1.0
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: autoregister
Plugin update URI: autoregister
*/

    /**
     * Set plugin preferences 
     */
    function autoregister_install() 
    {
        // read email template for new autoregister
        $content = file_get_contents( osc_plugins_path() . osc_plugin_folder(__FILE__).'autoregister_new_user_info' );
        $aContent = json_decode($content, true);
        $s_internal_name    = 'autoregister_new_user_info';
        $aFields            = array('s_internal_name' => $s_internal_name, 'b_indelible' => '1');
        $aFieldsDescription = array();

        foreach($aContent as $key => $value) {
            $aFieldsDescription[$key]['s_title'] = $value['title'];
            $aFieldsDescription[$key]['s_text']  = $value['description'];
        }
        // add page as email template
        $page = Page::newInstance()->findByInternalName($s_internal_name);
        if(!isset($page['pk_i_id'])) {
            $result = Page::newInstance()->insert($aFields, $aFieldsDescription) ;
        } else {
            osc_add_flash_error_message(_m("Oops! That internal name is already in use. We can't make the changes", 'autoregister'), 'admin') ;
        }
    }

    /**
     * Delete plugin preferences 
     */
    function autoregister_uninstall() 
    {
        $s_internal_name    = 'autoregister_new_user_info';
        Page::newInstance()->deleteByInternalName($s_internal_name);
    }
    
    function autoregister_create_new_user($catId, $itemId)
    {
        // check if exist user id 
        $item = Item::newInstance()->findByPrimaryKey($itemId);
        // if not exist user
        if( $item['fk_i_user_id'] == NULL ) {
            // create new user + send email 
            $name  = $item['s_contact_name'];
            $email = $item['s_contact_email'];
            // prepare data for register user
            $aux_password = osc_genRandomPassword();
            // clear params ....
            $input = array();
            $input['s_name']         = Params::getParam('s_name') ;
            Params::setParam('s_name', $name );  // from inserted item
            $input['s_email']        = Params::getParam('s_email') ;
            Params::setParam('s_email', $email ); // from inserted item
            $input['s_password']     = Params::getParam('s_password') ;
            Params::setParam('s_password', $aux_password );  // generated 
            $input['s_password2']    = Params::getParam('s_password2') ;
            Params::setParam('s_password2', $aux_password ); // generated 
            $input['s_website']      = Params::getParam('s_website') ;
            Params::setParam('s_website', '');
            $input['s_phone_land']   = Params::getParam('s_phone_land') ;
            Params::setParam('s_phone_land', '');
            $input['s_phone_mobile'] = Params::getParam('s_phone_mobile') ;
            Params::setParam('s_phone_mobile', '');
            $input['countryId']      = Params::getParam('countryId');
            Params::setParam('countryId', '');
            $input['regionId']       = Params::getParam('regionId');
            Params::setParam('regionId', '');
            $input['cityId']         = Params::getParam('cityId');
            Params::setParam('cityId', '');
            $input['cityArea']       = Params::getParam('cityArea') ;
            Params::setParam('cityArea', '');
            $input['address']        = Params::getParam('address') ;
            Params::setParam('address', '');
            $input['b_company']      = (Params::getParam('b_company') != '' && Params::getParam('b_company') != 0) ? 1 : 0 ;
            Params::setParam('b_company', '0');
                        
            require_once LIB_PATH . 'osclass/UserActions.php' ;
            $userActions = new UserActions(false) ;
            $success     = $userActions->add() ;
            // update user of item
            $user = User::newInstance()->findByEmail($email);
            Item::newInstance()->update(array('fk_i_user_id' => $user['pk_i_id'] ), array('pk_i_id' => $itemId ) );
            $item = Item::newInstance()->findByPrimaryKey($itemId);
            
            // not activated
            if( $item['b_active'] != 1 ) {
                osc_run_hook('hook_email_item_validation', $item);
            }
            
            /* code sendMail */
            $aPage = Page::newInstance()->findByInternalName('autoregister_new_user_info');
            $locale = osc_current_user_locale() ;

            $content = array();
            if(isset($aPage['locale'][$locale]['s_title'])) {
                $content = $aPage['locale'][$locale];
            } else {
                $content = current($aPage['locale']);
            }

            if (!is_null($content)) {
                $words   = array();
                $words[] = array(
                    '{WEB_LINK}',
                    '{USER_NAME}',
                    '{USER_EMAIL}',
                    '{USER_PASSWORD}'
                );
                $words[] = array(
                    '<a href="' . osc_base_url() . '">' . osc_page_title() . '</a>',
                    $user['s_name'],
                    $user['s_email'],
                    $aux_password
                );
                $title = osc_mailBeauty( $content['s_title'], $words);
                $body = osc_mailBeauty( $content['s_text'], $words);

                $emailParams = array(
                    'subject'  => $title,
                    'from'     => osc_contact_email(),
                    'to'       => $user['s_email'],
                    'to_name'  => $user['s_name'],
                    'body'     => $body,
                    'alt_body' => $body
                );

                osc_sendMail($emailParams);
            }
            /* END code sendMail */
            
            // set params again
            Params::setParam('s_name', $input['s_name']);
            Params::setParam('s_email', $input['s_email']);
            Params::getParam('s_password', $input['s_password']) ;
            Params::getParam('s_password2', $input['s_password2']) ;
            Params::setParam('s_website', $input['s_website']);
            Params::setParam('s_phone_land',    $input['s_phone_land']);
            Params::setParam('s_phone_mobile',  $input['s_phone_mobile']); 
            Params::setParam('countryId',   $input['countryId']);
            Params::setParam('regionId',    $input['regionId']);
            Params::setParam('cityId',      $input['cityId']);
            Params::setParam('cityArea',    $input['cityArea'] );
            Params::setParam('address',     $input['address']);
            Params::setParam('b_company',   $input['b_company']);
            // end
        }
    }
    
    /**
     * ADD HOOKS
     */
    osc_register_plugin(osc_plugin_path(__FILE__), 'autoregister_install');
    osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", 'autoregister_uninstall');
    
    // remove hook no user registered
    osc_remove_hook('hook_email_new_item_non_register_user', 'fn_email_new_item_non_register_user');
    
    osc_add_hook('item_form_post', 'autoregister_create_new_user');
?>
