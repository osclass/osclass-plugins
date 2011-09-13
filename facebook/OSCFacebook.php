<?php
require_once 'src/facebook.php';
class OSCFacebook {

    private static $instance ;
    private $facebook;
    private $user;
    private $loginUrl;
    private $logoutUrl;
    private $user_profile;

	public static function newInstance() { 
        if(!self::$instance instanceof self) {
            self::$instance = new self ;
        }
        return self::$instance ;
    }

    public function __construct() {
    }
    
    public function init($appId, $secret) {
        $this->facebook = new Facebook(array(
            'appId'  => $appId,
            'secret' => $secret,
            'cookie' => true
        ));
        $this->logoutUrl = $this->facebook->getLogoutUrl(array('redirect_uri' => osc_base_url()));
        if(osc_is_web_user_logged_in()) {
            $this->loginUrl = $this->facebook->getLoginUrl(array('scope' => 'email', 'redirect_uri' => osc_user_dashboard_url()));
        } else {
            $this->loginUrl = $this->facebook->getLoginUrl(array('scope' => 'email', 'redirect_uri' => osc_base_url()));
        }
        $this->user = $this->getUser();
        if ($this->user) {
            try {
                $this->user_profile = $this->facebook->api('/me');
                $conn = getConnection();
                $user = $conn->osc_dbFetchResult(sprintf("SELECT * FROM %st_facebook_connect WHERE i_facebook_uid = %s", DB_TABLE_PREFIX, $this->user));
                // It's linked on our DB!
                if(isset($user['fk_i_user_id'])) {
                    require_once LIB_PATH . 'osclass/UserActions.php' ;
                    $uActions = new UserActions(false);
                    $logged = $uActions->bootstrap_login($user['fk_i_user_id']) ;
                                        
                    if($logged==0) {
                        osc_add_flash_error_message(__('The username doesn\'t exist', 'facebook')) ;
                    } else if($logged==1) {
                        osc_add_flash_error_message(__('The user has not been validated yet', 'facebook'));
                    } else if($logged==2) {
                        osc_add_flash_error_message(__('The user has been suspended', 'facebook'));
                    } else if($logged==3) {
                    
                    }
                } else {
                    if(isset($this->user_profile['email'])) {
                        $osc_user = $conn->osc_dbFetchResult(sprintf("SELECT s_name FROM %st_user WHERE s_email = '%s'", DB_TABLE_PREFIX, $this->user_profile['email']));
                        // Exists on our DB, ask him to link it
                        if(isset($osc_user['s_name'])) {
                            // User is logged into her/his OSClass account
                            if(osc_is_web_user_logged_in()) {
                                $user = User::newInstance()->findByPrimaryKey(osc_logged_user_id());
                                if($user) {
                                    $conn->osc_dbExec(sprintf("REPLACE INTO `%st_facebook_connect` SET `fk_i_user_id` = %d, `i_facebook_uid` = '%s'", DB_TABLE_PREFIX, osc_logged_user_id(), $this->user_profile['id']));
                                } else {
                                    osc_add_flash_ok_message(__('Hey! We just discovered some user with your same email address. Log into your account to link it to Facebook', 'facebook'));
                                }
                            } else {
                                osc_add_flash_ok_message(__('Hey! We just discovered some user with your same email address. Log into your account to link it to Facebook.', 'facebook'));
                            }
                        // Auto-register him    
                        } else {
                            $this->register_user($this->user_profile);
                        }
                    } else {
                        osc_add_flash_error_message(__('Some error occured trying to connect with Facebook.','facebook'));
                        header("Location: " . $this->logoutUrl);
                        exit();
                    }
                }
            } catch (FacebookApiException $e) {
                //error_log($e);
                $this->user = null;
            }
        }
        return $this->facebook;
    }
    
    public function getFacebook() {
        return $this->facebook;
    }
    
    public function getUser() {
        if($this->user==null) {
            $this->user = $this->facebook->getUser();
        }
        return $this->user;
    }
    
    public function logoutUrl() {
        return $this->logoutUrl;
    }

    public function loginUrl() {
        return $this->loginUrl;
    }
    
    public function profile() {
        if($this->user_profile==null) {
            $this->user_profile = $this->facebook->api('/me');
        }
        return $this->user_profile;
    }
    
    private function register_user($user) {

        $input['s_name'] = $user['name'];
        $input['s_email'] = $user['email'];
        $input['s_password'] = sha1(osc_genRandomPassword());
        $input['dt_reg_date'] = date('Y-m-d H:i:s');
           
        $code = osc_genRandomPassword();
        $input['s_secret'] = $code;
        $manager = User::newInstance();
        $email_taken = $manager->findByEmail($input['s_email']) ;
        if($email_taken == null) {
            $manager->insert($input) ;
            $conn = getConnection();                    
            $userId = $manager->getConnection()->get_last_id() ;
            $conn->osc_dbExec(sprintf("REPLACE INTO `%st_facebook_connect` SET `fk_i_user_id` = %d, `i_facebook_uid` = '%s'", DB_TABLE_PREFIX, $userId, $user['id']));

            osc_run_hook('user_register_completed', $userId) ;
            
            if( osc_user_validation_enabled()) {
                $user = $manager->findByPrimaryKey($userId) ;
                $mPages = new Page() ;
                $locale = osc_current_user_locale() ;
                $aPage = $mPages->findByInternalName('email_user_validation') ;
                $content = array() ;
                if(isset($aPage['locale'][$locale]['s_title'])) {
                    $content = $aPage['locale'][$locale] ;
                } else {
                    $content = current($aPage['locale']) ;
                }
                if (!is_null($content)) {
                    $validation_url = osc_user_activate_url($user['pk_i_id'], $input['s_secret']);
                    $words   = array();
                    $words[] = array('{USER_NAME}', '{USER_EMAIL}', '{WEB_URL}', '{VALIDATION_LINK}', '{VALIDATION_URL}') ;
                    $words[] = array($user['s_name'], $user['s_email'], '<a href="'.osc_base_url().'" >'.osc_base_url().'</a>', '<a href="' . $validation_url . '" >' . $validation_url . '</a>', '<a href="' . $validation_url . '" >' . $validation_url . '</a>') ;
                    $title = osc_mailBeauty($content['s_title'], $words) ;
                    $body = osc_mailBeauty($content['s_text'], $words) ;

                    $emailParams = array('subject'  => $title
                                         ,'to'       => $user['s_email']
                                         ,'to_name'  => $user['s_name']
                                         ,'body'     => $body
                                         ,'alt_body' => $body
                    ) ;
                    osc_sendMail($emailParams);
                }
                osc_add_flash_ok_message(sprintf(__("An automatic account for %s has been created. You'll receive an email to confirm", 'facebook'), osc_page_title()));
            } else {
                $manager->update(
                                array('b_active' => '1')
                                ,array('pk_i_id' => $userId)
                );
                osc_add_flash_ok_message(sprintf(__("An automatic account for %s has been created. You're ready to go", 'facebook'), osc_page_title()));
            }
        }
    }
        
}
    
?>
