<?php

    /**
     * OSClass – software for creating and publishing online classified advertising platforms
     *
     * Copyright (C) 2010 OSCLASS
     *
     * This program is free software: you can redistribute it and/or modify it under the terms
     * of the GNU Affero General Public License as published by the Free Software Foundation,
     * either version 3 of the License, or (at your option) any later version.
     *
     * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
     * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
     * See the GNU Affero General Public License for more details.
     *
     * You should have received a copy of the GNU Affero General Public
     * License along with this program. If not, see <http://www.gnu.org/licenses/>.
     */

    require_once dirname( __FILE__ ) . '/src/facebook.php';

    class OSCFacebook extends DAO
    {
        private static $instance;
        private static $facebook;
        private static $user;
        private static $loginUrl;
        private static $logoutUrl;
        private static $user_profile;

        public static function newInstance()
        {
            if(!self::$instance instanceof self) {
                self::$instance = new self;
            }

            return self::$instance;
        }

        public function __construct()
        {
            parent::__construct();
            $this->setTableName( 't_facebook_connect' );
            $this->setPrimaryKey( 'fk_i_user_id' );
            $this->setFields( array( 'fk_i_user_id', 'i_facebook_uid' ) );


            self::$facebook = new Facebook(array(
                'appId'  => osc_get_preference('fbc_appId', 'facebook_connect'),
                'secret' => osc_get_preference('fbc_secret', 'facebook_connect')
            ));

        }

        public function init() {

            self::$user = $this->getUser();

            self::$logoutUrl = self::$facebook->getLogoutUrl( array('next' => osc_render_file_url(osc_plugin_folder(__FILE__) . 'logout.php')));
            self::$loginUrl  = self::$facebook->getLoginUrl( array('scope' => 'email'));

            if ( !self::$user ) {
                return self::$facebook;
            }

            try {

                self::$user_profile = self::$facebook->api( '/me' );

                $this->dao->select( $this->getFields() );
                $this->dao->from( $this->getTableName() );
                $this->dao->where( 'i_facebook_uid', self::$user );
                $rs = $this->dao->get();

                if( ( $rs !== false ) && ( $rs->numRows() === 1 ) ) {
                    $fbUser = $rs->row();
                    if( count($fbUser) > 0 ) {
                        require_once osc_lib_path() . 'osclass/UserActions.php';
                        $uActions = new UserActions( false );
                        $logged   = $uActions->bootstrap_login( $fbUser['fk_i_user_id'] );

                        switch( $logged ) {
                            case 0: osc_add_flash_error_message( __( 'The username doesn\'t exist', 'facebook' ) );
                            break;
                            case 1: osc_add_flash_error_message( __( 'The user has not been validated yet', 'facebook' ) );
                            break;
                            case 2: osc_add_flash_error_message( __( 'The user has been suspended', 'facebook' ) );
                            break;
                        }
                        return self::$facebook;
                    }
                }

                if( !isset(self::$user_profile['email']) ) {
                    osc_add_flash_error_message( __('Some error occured trying to connect with Facebook.', 'facebook') );
                    header( 'Location: ' . self::$logoutUrl );
                    exit();
                }

                if( Params::getParam('fbc_login') !== 'true' ) {
                    return self::$facebook;
                }

                $manager = User::newInstance();
                $oscUser = $manager->findByEmail( self::$user_profile['email'] );
                // exists on our DB, we merge both accounts
                if( count($oscUser) > 0 ) {
                    $manager->dao->from( $this->getTableName() );
                    $manager->dao->set( 'fk_i_user_id', $oscUser['pk_i_id'] );
                    $manager->dao->set( 'i_facebook_uid', self::$user_profile['id'] );
                    $manager->dao->insert();
                    osc_add_flash_ok_message( __( "You already have an user with this e-mail address. We've merged your accounts", 'facebook' ) );

                    // activate user in case is not activated
                    $manager->update( array('b_active' => '1')
                                     ,array('pk_i_id' => $oscUser['pk_i_id']) );
                    $logged = $uActions->bootstrap_login( $oscUser['pk_i_id'] );
                } else {
                    // Auto-register him
                    $this->register_user( self::$user_profile );
                }

                // redirect to log in
                header( 'Location: ' . osc_base_url() );
                exit;
            } catch (FacebookApiException $e) {
                self::$user = null;
            }

            return self::$facebook;
        }

        public function import( $file )
        {
            $path = osc_plugin_resource( $file );
            $sql  = file_get_contents( $path );

            if( !$this->dao->importSQL( $sql ) ) {
                throw new Exception( __('Error importing the database structure of the jobboard plugin', 'jobboard') );
            }
        }

        public function uninstall()
        {
            $this->resetCookies();
            $this->dao->query( 'DROP TABLE ' . $this->getTableName() );
        }

        public function getFacebook()
        {
            return self::$facebook;
        }

        public function getUser()
        {
            if( self::$user == null ) {
                self::$user = self::$facebook->getUser();
            }

            return self::$user;
        }

        public function logoutUrl()
        {
            return self::$logoutUrl;
        }

        public function loginUrl()
        {
            return self::$loginUrl;
        }

        public function profile()
        {
            if( self::$user_profile == null ) {
                self::$user_profile = self::$facebook->api( '/me' );
            }

            return self::$user_profile;
        }

        private function register_user($user)
        {
            $manager = User::newInstance();

            $input['s_name']      = $user['name'];
            $input['s_email']     = $user['email'];
            $input['s_password']  = sha1( osc_genRandomPassword() );
            $input['dt_reg_date'] = date( 'Y-m-d H:i:s' );
            $input['s_secret']    = osc_genRandomPassword();

            $email_taken = $manager->findByEmail( $input['s_email'] );
            if($email_taken == null) {
                $manager->insert( $input );
                $userID = $manager->dao->insertedId();

                $manager->dao->from( $this->getTableName() );
                $manager->dao->set( 'fk_i_user_id', $userID );
                $manager->dao->set( 'i_facebook_uid', $user['id'] );
                $result = $manager->dao->replace();

                if( $result == false ) {
                    // error inserting user
                    return false;
                }

                osc_run_hook( 'user_register_completed', $userID );

                $userDB = $manager->findByPrimaryKey( $userID );

                if( osc_notify_new_user() ) {
                    osc_run_hook( 'hook_email_admin_new_user', $userDB );
                }

                if(osc_version()>=310) {
                    $manager->update( array('b_active' => '1', 's_username' => $userID)
                                    ,array('pk_i_id' => $userID) );
                } else {
                        $manager->update( array('b_active' => '1')
                            ,array('pk_i_id' => $userID) );
                }

                osc_run_hook('hook_email_user_registration', $userDB);
                osc_run_hook('validate_user', $userDB);

                osc_add_flash_ok_message( sprintf( __('Your account has been created successfully', 'facebook' ), osc_page_title() ) );
                return true;
            }
        }

        public function resetCookies()
        {
            self::$facebook->destroySession();
        }
    }

    /* file end: ./oc-content/plugins/facebook/OSCFacebook.php */
?>