<?php
/*
Plugin Name: Social Connect
Plugin URI: http://www.osclass.org/
Description: Allow user to login with their Social networks users.
Version: 1.0
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: social_connect
*/


    require_once 'src/facebook.php';

function fbc_init() {

    global $facebook;
    
    $preferences = Preference::newInstance()->toArray('social_connect');
    // Create our Application instance (replace this with your appId and secret).
    $facebook = new Facebook(array(
      'appId'  => $preferences['fbc_appId'],//'117743971608120',
      'secret' => $preferences['fbc_secret'],//'943716006e74d9b9283d4d5d8ab93204',
      'cookie' => true,
    ));

    // We may or may not have this data based on a $_GET or $_COOKIE based session.
    //
    // If we get a session here, it means we found a correctly signed session using
    // the Application Secret only Facebook and the Application know. We dont know
    // if it is still valid until we make an API call using the session. A session
    // can become invalid if it has already expired (should not be getting the
    // session back in this case) or if the user logged out of Facebook.
    $session = $facebook->getSession();

    $me = null;
    // Session based API call.
    if ($session) {
      try {
        $me = $facebook->api('/me');
        $conn = getConnection();
        $user = $conn->osc_dbFetchResult(sprintf("SELECT * FROM %st_social_connect WHERE i_facebook_uid = %d", DB_TABLE_PREFIX, $me['id']));

        // It's linked on our DB!
        if($user) {
            $_SESSION['userId'] = $user['fk_i_user_id'];
            return $me;
        } else {
            if(isset($me['email'])) {
                $osc_user = $conn->osc_dbFetchResult(sprintf("SELECT s_username FROM %st_user WHERE s_email = '%s'", DB_TABLE_PREFIX, $me['email']));
                // Exists on our DB, ask him to link it
                if($osc_user) {
                    // User is logged into her/his OSClass account
                    if(isset($_SESSION['userId']) && $_SESSION['userId']!=null && $_SESSION['userId']!='') {
                        $user = User::newInstance()->findByPrimaryKey($_SESSION['userId']);
                        if($user) {
                        
                            $conn->osc_dbExec(sprintf("REPLACE INTO `%st_social_connect` SET `fk_i_user_id` = %d, `i_facebook_uid` = '%s'", DB_TABLE_PREFIX, $_SESSION['userId'], $me['id']));
                        } else {
                            osc_addFlashMessage(__('Hey! We just discovered some user with your same email address. Log into your account to link it to Facebook.'));
                        }
                    } else {
                        osc_addFlashMessage(__('Hey! We just discovered some user with your same email address. Log into your account to link it to Facebook.'));
                    }
                // Auto-register him    
                } else {
                    socialconnect_register_user($me, 'FB');
                }
            } else {
                osc_addFlashMessage(__('Some error occured trying to connect with Facebook.'));
                osc_redirectTo($facebook->getLogoutUrl());
            }
        }
      } catch (FacebookApiException $e) {
        error_log($e);
      }
    }

    ?>
    <div id="fb-root"></div>
    <script>
      window.fbAsyncInit = function() {
        FB.init({
          appId   : '<?php echo $facebook->getAppId(); ?>',
          session : null;//<?php echo json_encode($session); ?>, // don't refetch the session when PHP already has it
          status  : true, // check login status
          cookie  : true, // enable cookies to allow the server to access the session
          xfbml   : true // parse XFBML
        });

        // whenever the user logs in, we refresh the page
        FB.Event.subscribe('auth.login', function(response) {
            if (response.session){
                browserFixSession(response.session);
                window.location.reload();
            } 
        });
        
        
      };

        function browserFixSession(sess) {
            var cookieString = "";
            $.each(sess,function(j) { //only place jquery is used on this page really, you could probably rewrite this if you didn't want to use it.
                cookieString += (j + '=' + sess[j] + '&');
            });
                
            var exdate=new Date();
            exdate.setDate(exdate.getDate() + 3);
            document.cookie="fbs_<?php echo $preferences['fbc_appId']; ?>=" + cookieString + ';expires="' + exdate.toUTCString() + '"';
                
        }
        
        function login(){
            FB.api('/me', function(response) {
                alert(response.name + " se ha logeado.!");
            });
        }


      (function() {
        var e = document.createElement('script');
        e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
        e.async = true;
        document.getElementById('fb-root').appendChild(e);
      }());
    </script>
<?php 

    
    return $me;

}

function fbc_button($use_js = false) {
global $facebook;

    $me = fbc_is_logged();
    if($me!=null) { ?>
        <a href="<?php echo $facebook->getLogoutUrl(); ?>">
          <img src="http://static.ak.fbcdn.net/rsrc.php/z2Y31/hash/cxrz4k7j.gif">
        </a>
    <?php } else { 
        if($use_js) { ?>
            <div>
              <fb:login-button perms="email"></fb:login-button>
            </div>
        <?php } else { ?>
            <div>
              <a href="<?php echo $facebook->getLoginUrl(array('req_perms' => 'email')); ?>">
                <img src="http://static.ak.fbcdn.net/rsrc.php/zB6N8/hash/4li2k73z.gif">
              </a>
            </div>
    <?php };
    };
}

function fbc_is_logged() {
global $facebook;
$session = $facebook->getSession();
$me = null;

    // Session based API call.
    if ($session) {
      try {
        //$uid = $facebook->getUser();
        $me = $facebook->api('/me');
      } catch (FacebookApiException $e) {
        error_log($e);
      }
    }

    return $me;
}


function socialconnect_register_user($me = null, $socialnetwork = 'FB') {

    global $facebook, $preferences;

    if($me==null) {
        if($socialnetwork=='FB') {
            osc_addFlashMessage(__('Some error occured trying to connect with Facebook.'));
            osc_redirectTo($facebook->getLogoutUrl());
        }
    } else {

        if($socialnetwork=='FB') {
            $input['s_name'] = $me['name'];
            $input['s_username'] = preg_replace('|([\.]+)|', '.' , str_replace(" ", ".", preg_replace('|^a-z0-9\.|', '', strtolower($me['name']))));
            if(strlen($input['s_username'])<6) {
                $input['s_username'] = 'fb.user1';
            }
            $input['s_email'] = $me['email'];
        }
        
        $input['s_password'] = sha1(osc_genRandomPassword());
        $input['dt_reg_date'] = DB_FUNC_NOW;
           
        $code = osc_genRandomPassword();
        $input['s_secret'] = $code;
        $reg_ok = false;
        $user_counter = 0;
        $username = $input['s_username'];
        $manager = User::newInstance();
        while($reg_ok==false) {
            try {
                $username_taken = $manager->findByUsername($input['s_username']);
                if($username_taken==null) {
                    $manager->insert($input);
                    $userId = $manager->getConnection()->get_last_id();
                    
                    
                    $conn = getConnection();
                    $conn->osc_dbExec(sprintf("REPLACE INTO `%st_social_connect` SET `fk_i_user_id` = %d, `i_facebook_uid` = '%s'", DB_TABLE_PREFIX, $userId, $me['id']));
                    
                    
                    if(isset($preferences['enabled_user_validation']) && $preferences['enabled_user_validation']) {
                        $user = $manager->findByPrimaryKey($userId);

                        $content = Page::newInstance()->findByInternalName('email_user_validation');
                        if (!is_null($content)) {
                            $validationLink = sprintf('%s/user.php?action=validate&id=%d&code=%s', ABS_WEB_URL, $user['pk_i_id'], $code);
				            $words = array();
                            $words[] = array('{USER_NAME}', '{USER_EMAIL}', '{WEB_URL}', '{VALIDATION_LINK}');
                            $words[] = array($user['s_name'], $user['s_email'], ABS_WEB_URL, $validationLink);
                            $title = osc_mailBeauty($content['s_title'], $words);
                            $body = osc_mailBeauty($content['s_text'], $words);
				
                            $params = array(
                                'subject' => $title,
                                'to' => $_POST['s_email'],
                                'to_name' => $_POST['s_name'],
                                'body' => $body,
                                'alt_body' => $body
                            );
                            osc_sendMail($params);
                        }

                        osc_addFlashMessage(__('An automatic account for ').$preferences['pageTitle'].__(' has been created.'));
                        osc_addFlashMessage(__('An activation email has been sent to your email address.'));
                        $reg_ok = true;
                    } else {
                        User::newInstance()->update(
                            array('b_enabled' => '1'),
                            array('pk_i_id' => $userId)
                        );
                        osc_addFlashMessage(__('An automatic account for ').$preferences['pageTitle'].__(' has been created. You\'re ready to go.'));
                        $reg_ok = true;
                    }
                } else {
                    // USERNAME ALREADY IN USE
                    $user_counter++;
                    $input['s_username'] = $username.$user_counter;
                    $reg_ok = false;
                }
            } catch (Exception $e) {
                if($socialnetwork=='FB') {
                    $reg_ok = true;
                    osc_addFlashMessage(__('Some error occured trying to connect with Facebook.'));
                    osc_redirectTo($facebook->getLogoutUrl());
                }
            }

        }    
    
    
    }
}



function socialconnect_call_after_install() {
    // Insert here the code you want to execute after the plugin's install
    // for example you might want to create a table or modify some values
    $conn = getConnection() ;
    $conn->autocommit(false) ;
    try {
        $path = osc_pluginResource('social_connect/struct.sql');
        $sql = file_get_contents($path);
        $conn->osc_dbImportSQL($sql);
        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        echo $e->getMessage();
    }
    $conn->autocommit(true);
        
    $prefs = new Preference();
    $prefs->insert(array('s_section' => 'social_connect', 's_name'  => 'fbc_appId', 's_value' => '', 'e_type' => 'STRING')) ;
    $prefs->insert(array('s_section' => 'social_connect', 's_name'  => 'fbc_secret', 's_value' => '', 'e_type' => 'STRING')) ;
    
}


// Display User's menu for Social Connect
function socialconnect_user_page($params = null) {

    if(isset($params[0]) && $params[0]=='social_connect') {
        $conn = getConnection();
        $user = $conn->osc_dbFetchResult(sprintf("SELECT * FROM %st_social_connect WHERE fk_i_user_id = %d", DB_TABLE_PREFIX, $_SESSION['userId']));
        
        if(isset($user['i_facebook_uid']) && $user['i_facebook_uid']!=null && $user['i_facebook_uid']!='') {
        
            _e('Your account is linked with this Facebook\'s account: ');
            $me = json_decode(file_get_contents('http://graph.facebook.com/'.$user['i_facebook_uid']));
            echo '<br /><img src="http://graph.facebook.com/'.$user['i_facebook_uid'].'/picture" /> '.$me->name."<br />";
            _e('You could de-attach your Facebook\'s account: ');
            echo '<form action="'.osc_createUserOptionsPostURL('social_connect').'" method="post" enctype="multipart/form-data">
        		<input type="hidden" name="subaction" value="unlink_facebook" />
        		<button class="socialconnect_button" type="submit">'._('De-attach').'</button>
        		</form>';
        } else {
            echo "<br />";
            _e(' Would you like to connect your account to Facebook? So next time you could login using your Facebook\'s account!');
            ?>
            <br /><div>
              <fb:login-button perms="email" next="<?php echo osc_createUserOptionsURL('social_connect');?>"></fb:login-button>
            </div><br /><br />
        <?php }
        
    }
}

// Display User's menu for Social Connect
function socialconnect_user_page_post($params = null) {

    if(isset($params[0]) && $params[0]=='social_connect') {
        if(isset($_REQUEST['subaction'])) {
            if($_REQUEST['subaction']=='unlink_facebook') {
                global $facebook;
                $preferences = Preference::newInstance()->toArray('social_connect');
                // Create our Application instance (replace this with your appId and secret).
                $facebook = new Facebook(array(
                  'appId'  => $preferences['fbc_appId'],//'117743971608120',
                  'secret' => $preferences['fbc_secret'],//'943716006e74d9b9283d4d5d8ab93204',
                  'cookie' => true,
                ));
     $fb_session = $facebook->getSession();
                $logoutURL = $facebook->getLogoutUrl(array('next' => osc_createUserOptionsURL('social_connect')));
                $facebook->setSession(null, null);
                setcookie('fbs_'.$facebook->getAppId(), '', time()-100, '/', 'osclass');
                $conn = getConnection();
                $conn->osc_dbExec(sprintf("REPLACE INTO `%st_social_connect` SET `fk_i_user_id` = %d, `i_facebook_uid` = NULL", DB_TABLE_PREFIX, $_SESSION['userId']));

                $facebook->setSession(null);
                osc_redirectTo($logoutURL);
            }
        }
        osc_redirectTo(osc_createUserOptionsURL('social_connect'));
    }
}

// Display help
function socialconnect_conf() {
    osc_renderPluginView(dirname(__FILE__) . '/conf.php') ;
}


// Display option in users' account
function socialconnect_menu() {
    add_option_menu(array( 'name' => 'Social Connect options', 'url' => osc_createUserOptionsURL('social_connect')));
}

// This is needed in order to be able to activate the plugin
osc_registerPlugin(__FILE__, 'socialconnect_call_after_install');
// This is a hack to show a Configure link at plugins table (you could also use some other hook to show a custom option panel)
osc_addHook(__FILE__."_configure", 'socialconnect_conf');
// This is a hack to show a Uninstall link at plugins table (you could also use some other hook to show a custom option panel)
osc_addHook(__FILE__."_uninstall", '');

// Load the library and stuff
osc_addHook("header", 'fbc_init');
// Make a new option appear on user's menu
osc_addHook("user_menu", 'socialconnect_menu');
// Display SC page for users
osc_addHook("user_options", 'socialconnect_user_page');
// Manage post SC page for users
osc_addHook("user_options_post", 'socialconnect_user_page_post');

?>
