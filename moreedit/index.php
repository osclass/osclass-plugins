<?php
/*
  Plugin Name: More edit
  Plugin URI: http://www.osclass.org/
  Description: More edit options
  Version: 1.1.0
  Author: OSClass
  Author URI: http://www.osclass.org/
  Short Name: moreedit
  Plugin update URI: more-edit
 */
require_once 'ModelMoreedit.php';

function moreedit_install()
{
    osc_set_preference('moderate_all',  '0', 'moreedit', 'BOOLEAN');
    osc_set_preference('moderate_edit', '0', 'moreedit', 'BOOLEAN');
    osc_set_preference('disable_edit',  '0', 'moreedit', 'BOOLEAN');
    osc_set_preference('max_ads_week',  '0', 'moreedit', 'INTEGER');
    osc_set_preference('max_ads_month', '0', 'moreedit', 'INTEGER');
    osc_set_preference('notify_edit',   '0', 'moreedit', 'INTEGER');

    ModelMoreedit::newInstance()->install();
}

function moreedit_uninstall()
{
    osc_delete_preference('moderate_all',   'moreedit');
    osc_delete_preference('moderate_edit',  'moreedit');
    osc_delete_preference('disable_edit',   'moreedit');
    osc_delete_preference('max_ads_week',   'moreedit');
    osc_delete_preference('max_ads_month',  'moreedit');
    osc_delete_preference('notify_edit',    'moreedit');

    ModelMoreedit::newInstance()->uninstall();
}

function moreedit_moderate_all($item)
{
    $cat_id     = $item['fk_i_category_id'];
    $item_id    = $item['pk_i_id'];

    if (osc_get_preference('max_ads_week', 'moreedit') > 0) {
        $total_per_week = 0;
        if(osc_is_web_user_logged_in()) {
            $total_per_week = ModelMoreedit::newInstance()->ads_per_week( osc_logged_user_id() );
        } else {
            $total_per_week = ModelMoreedit::newInstance()->ads_per_week_email( $item['s_contact_email'] );
        }
        if ($total_per_week >= (osc_get_preference('max_ads_week', 'moreedit') + 1)) {
            $item = Item::newInstance()->findByPrimaryKey($item_id);
            $mItems = new ItemActions(false);
            $success = $mItems->delete($item['s_secret'], $item['pk_i_id']);
            osc_add_flash_error_message(__('Sorry, you have reached your maximun number of ads per week allowed', 'moreedit'));
            header("location: " . osc_base_url());
            exit;
        }
    }
    if (osc_get_preference('max_ads_month', 'moreedit') > 0) {
        $total_per_month = 0;
        if(osc_is_web_user_logged_in()) {
            $total_per_month = ModelMoreedit::newInstance()->ads_per_month( osc_logged_user_id() );
        } else {
            $total_per_month = ModelMoreedit::newInstance()->ads_per_month_email( $item['s_contact_email'] );
        }
        if ($total_per_month >= (osc_get_preference('max_ads_month', 'moreedit') + 1)) {
            $item = Item::newInstance()->findByPrimaryKey($item_id);
            $mItems = new ItemActions(false);
            $success = $mItems->delete($item['s_secret'], $item['pk_i_id']);
            osc_add_flash_error_message(__('Sorry, you have reached your maximun number of ads per month allowed', 'moreedit'));
            header("location: " . osc_base_url());
            exit;
        }
    }
}

// hack rewrite flash_messages
if ( osc_get_preference('moderate_all', 'moreedit') == '1' ||
     osc_get_preference('moderate_edit', 'moreedit') == '1')
{
    osc_add_hook('before_html', 'moreedit_flash_message');
}

function moreedit_flash_message()
{
    $error = Session::newInstance()->_get('moreedit_fm_info');
    if( $error!='' ) {
        osc_add_flash_info_message($error);
        Session::newInstance()->_drop('moreedit_fm_info');
    }
}

function moreedit_posted_item($item)
{
    if (osc_get_preference('moderate_all', 'moreedit') == '1') {
        $error = Session::newInstance()->_get('moreedit_fm_info');
        if($error=='') {
            Session::newInstance()->_set('moreedit_fm_info', __('Your ad needs to be approved by the administrator, it could take a while until it appear on the website', 'moreedit') );
        }
        Item::newInstance()->update(array('b_enabled' => 0), array('pk_i_id' => $item['pk_i_id']));
    }
}


function moreedit_moderate_edit($item_)
{
    $cat_id     = $item_['fk_i_category_id'];
    $item_id    = $item_['pk_i_id'];
    if (osc_get_preference('notify_edit', 'moreedit') == '1') {
        $aPage = Page::newInstance()->findByInternalName('email_moreedit_notify_edit');
        $content = array();
        $locale = osc_current_user_locale();
        if (isset($aPage['locale'][$locale]['s_title'])) {
            $content = $aPage['locale'][$locale];
        } else {
            $content = current($aPage['locale']);
        }
        $item = Item::newInstance()->findByPrimaryKey($item_id);
        View::newInstance()->_exportVariableToView('item', $item);
        $item_url = osc_item_url();
        $item_url = '<a href="' . $item_url . '" >' . $item_url . '</a>';

        $admin_edit_url = osc_item_admin_edit_url($item['pk_i_id']);

        $words = array();
        $words[] = array('{EDIT_LINK}', '{EDIT_URL}', '{ITEM_DESCRIPTION}', '{ITEM_ID}',
            '{USER_NAME}', '{USER_EMAIL}', '{WEB_URL}', '{ITEM_TITLE}', '{ITEM_URL}',
            '{WEB_TITLE}');
        $words[] = array('<a href="' . $admin_edit_url . '" >' . $admin_edit_url . '</a>', $admin_edit_url, $item['s_description'], $item['pk_i_id'],
            $item['s_contact_name'], $item['s_contact_email'], '<a href="' . osc_base_url() . '" >' . osc_base_url() . '</a>', $item['s_title'],
            $item_url, osc_page_title());
        $title = osc_mailBeauty($content['s_title'], $words);
        $body = osc_mailBeauty($content['s_text'], $words);

        $emailParams = array(
            'subject' => $title
            , 'to' => osc_contact_email()
            , 'to_name' => 'admin'
            , 'body' => $body
            , 'alt_body' => $body
                );
        osc_sendMail($emailParams);
    }
    if (osc_get_preference('moderate_edit', 'moreedit') == '1') {
        Item::newInstance()->update(array('b_enabled' => 0), array('pk_i_id' => $item_id));
    }
}

/**
 * Blocks item_add page if limit is reached, only if web_user_logged_in
 */
function moreedit_item_add() {
    if ( !osc_is_web_user_logged_in() ) {
        return true;
    }
    if (osc_get_preference('max_ads_week', 'moreedit') > 0) {
        $total_per_week = ModelMoreedit::newInstance()->ads_per_week( osc_logged_user_id());
        if ($total_per_week >= osc_get_preference('max_ads_week', 'moreedit')) {
            osc_add_flash_error_message(__('Sorry, you have reached your maximun number of ads per week allowed', 'moreedit'));
            header("location: " . osc_base_url());
            exit;
        }
    }
    if (osc_get_preference('max_ads_month', 'moreedit') > 0) {
        $total_per_month = ModelMoreedit::newInstance()->ads_per_month( osc_logged_user_id() );
        if ($total_per_month >= osc_get_preference('max_ads_month', 'moreedit')) {
            osc_add_flash_error_message(__('Sorry, you have reached your maximun number of ads per month allowed', 'moreedit'));
            header("location: " . osc_base_url());
            exit;
        }
    }
}

function moreedit_edited_item($item) {
    if (osc_get_preference('moderate_edit', 'moreedit') == '1') {
        $error = Session::newInstance()->_get('moreedit_fm_info');
        if($error=='') {
            Session::newInstance()->_set('moreedit_fm_info', __('Your ad needs to be approved by the administrator, it could take a while until it appear on the website', 'moreedit') );
        }
        Item::newInstance()->update(array('b_enabled' => 0), array('pk_i_id' => $item['pk_i_id']));
    }
}

function moreedit_item_edit() {
    if(Params::getParam('page')=='item' && Params::getParam('action')=='item_edit') {
        if (osc_get_preference('disable_edit', 'moreedit') == '1') {
            osc_add_flash_error_message(__('Sorry, editing is not allowed', 'moreedit'));
            if (osc_is_web_user_logged_in()) {
                header("location: " . osc_user_dashboard_url());
            } else {
                header("location: " . osc_base_url());
            }
            exit;
        }
        if (osc_get_preference('moderate_edit', 'moreedit') == '1') {
            osc_add_flash_info_message(__('Your ad will be needed to be moderated by an admin after you edit it. Until it gets approved it will not be visible to the rest of the users', 'moreedit'));
        };
    }
}

function moreedit_admin_menu()
{
    echo '<h3><a href="#">More Edit Options</a></h3>
        <ul>
            <li><a href="' . osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'admin/conf.php') . '">&raquo; ' . __('More Options', 'moreedit') . '</a></li>
        </ul>';
}

function moreedit_init_admin_menu()
{
    osc_add_admin_submenu_divider('plugins', 'More edit plugin', 'moreedit_divider', 'administrator');
    osc_add_admin_submenu_page('plugins', __('More edit options', 'moreedit'), osc_route_admin_url('moreedit-conf'), 'moreedit_settings', 'administrator');
}
/**
 * ADD HOOKS
 */
osc_register_plugin(osc_plugin_path(__FILE__), 'moreedit_install');
osc_add_hook(osc_plugin_path(__FILE__) . "_uninstall", 'moreedit_uninstall');

if(osc_version()<320) {
    osc_add_hook('admin_menu',      'moreedit_admin_menu');
} else {
    osc_add_route('moreedit-conf', 'moreedit/conf', 'moreedit/conf', osc_plugin_folder(__FILE__).'admin/conf.php');
    osc_add_hook('admin_menu_init', 'moreedit_init_admin_menu');
}

osc_add_hook('posted_item',     'moreedit_moderate_all');
osc_add_hook('edited_item',     'moreedit_moderate_edit');

osc_add_hook('posted_item',     'moreedit_posted_item');
osc_add_hook('edited_item',     'moreedit_edited_item');

osc_add_hook('post_item',       'moreedit_item_add');
osc_add_hook('init',            'moreedit_item_edit');
?>
