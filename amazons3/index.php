<?php
/*
Plugin Name: Amazon S3
Plugin URI: http://www.osclass.org/
Description: This plugin allows you to upload users' images to Amazon S3 service
Version: 1.0
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: amazons3
*/


    // load necessary functions
    require_once osc_plugins_path() . osc_plugin_folder(__FILE__) . 'S3.php';

    function amazon_install() {
        $conn = getConnection();
        osc_set_preference('bucket', '', 'amazons3', 'STRING');
        osc_set_preference('access_key', '', 'amazons3', 'STRING');
        osc_set_preference('secret_key', '', 'amazons3', 'STRING');
        $conn->autocommit(true);
    }

    function amazon_uninstall() {
        osc_delete_preference('bucket', 'amazons3');
        osc_delete_preference('access_key', 'amazons3');
        osc_delete_preference('secret_key', 'amazons3');
    }
    
    function amazon_upload($resource) {
        $s3 = new S3(osc_get_preference('access_key', 'amazons3'), osc_get_preference('secret_key', 'amazons3'));
        $s3->putBucket(osc_get_preference('bucket', 'amazons3'), S3::ACL_PUBLIC_READ);
        if(osc_keep_original_image()) {
            $s3->putObjectFile(osc_content_path() . 'uploads/' . $resource['pk_i_id'] . '_original.jpg', osc_get_preference('bucket', 'amazons3'), $resource['pk_i_id'] . '_original.jpg', S3::ACL_PUBLIC_READ);
        }
        $s3->putObjectFile(osc_content_path() . 'uploads/' . $resource['pk_i_id'] . '.jpg', osc_get_preference('bucket', 'amazons3'), $resource['pk_i_id'] . '.jpg', S3::ACL_PUBLIC_READ);
        $s3->putObjectFile(osc_content_path() . 'uploads/' . $resource['pk_i_id'] . '_preview.jpg', osc_get_preference('bucket', 'amazons3'), $resource['pk_i_id'] . '_preview.jpg', S3::ACL_PUBLIC_READ);
        $s3->putObjectFile(osc_content_path() . 'uploads/' . $resource['pk_i_id'] . '_thumbnail.jpg', osc_get_preference('bucket', 'amazons3'), $resource['pk_i_id'] . '_thumbnail.jpg', S3::ACL_PUBLIC_READ);
        amazon_unlink_resource($resource);
    }
    
    function amazon_resource_path($path) {
        return "http://". osc_get_preference('bucket', 'amazons3') .".s3.amazonaws.com/" . str_replace(osc_base_url().osc_resource_field("s_path"), '', $path);
    }
    
    function amazon_regenerate_image($resource) {
        $s3 = new S3(osc_get_preference('access_key', 'amazons3'), osc_get_preference('secret_key', 'amazons3'));
        $path = $resource['pk_i_id']. "_original.jpg";
        $img = @$s3->getObject(osc_get_preference('bucket','amazons3'), $path);
        if(!$img) {
            $path = $resource['pk_i_id']. ".jpg";
            $img = @$s3->getObject(osc_get_preference('bucket','amazons3'), $path);
        }
        if(!$img) {
            $path = $resource['pk_i_id']. "_thumbnail.jpg";
            $img = @$s3->getObject(osc_get_preference('bucket','amazons3'), $path);
        }
        if($img) {
            $s3->getObject(osc_get_preference('bucket','amazons3'), $path, osc_content_path() . 'uploads/' . $resource['pk_i_id'] . ".jpg");
            @$s3->deleteObject(osc_get_preference('bucket','amazons3'), $resource['pk_i_id']. "_original.jpg");
            @$s3->deleteObject(osc_get_preference('bucket','amazons3'), $resource['pk_i_id']. ".jpg");
            @$s3->deleteObject(osc_get_preference('bucket','amazons3'), $resource['pk_i_id']. "_preview.jpg");
            @$s3->deleteObject(osc_get_preference('bucket','amazons3'), $resource['pk_i_id']. "_thumbnail.jpg");
        }
    }
    
    function amazon_unlink_resource($resource) {
        @unlink(osc_content_path() . 'uploads/' . $resource['pk_i_id'] . '_original.jpg');
        @unlink(osc_content_path() . 'uploads/' . $resource['pk_i_id'] . '.jpg');
        @unlink(osc_content_path() . 'uploads/' . $resource['pk_i_id'] . '_preview.jpg');
        @unlink(osc_content_path() . 'uploads/' . $resource['pk_i_id'] . '_thumbnail.jpg');
    }
    
    function amazon_delete_from_bucket($resource) {
        $s3 = new S3(osc_get_preference('access_key', 'amazons3'), osc_get_preference('secret_key', 'amazons3'));
        @$s3->deleteObject(osc_get_preference('bucket','amazons3'), $resource['pk_i_id']. "_original.jpg");
        @$s3->deleteObject(osc_get_preference('bucket','amazons3'), $resource['pk_i_id']. ".jpg");
        @$s3->deleteObject(osc_get_preference('bucket','amazons3'), $resource['pk_i_id']. "_preview.jpg");
        @$s3->deleteObject(osc_get_preference('bucket','amazons3'), $resource['pk_i_id']. "_thumbnail.jpg");
    }
    

    function amazon_admin_menu() {
        echo '<h3><a href="#">Amazon S3</a></h3>
        <ul> 
            <li><a href="' . osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'conf.php') . '">&raquo; ' . __('Settings', 'amazon') . '</a></li>
        </ul>';
    }
    
    function amazon_redirect_to($url) {
        header('Location: ' . $url);
        exit;
    }
    
    function amazon_configure_link() {
        amazon_redirect_to(osc_admin_render_plugin_url(osc_plugin_folder(__FILE__)).'conf.php');
    }
    
    
    /**
     * ADD HOOKS
     */
    osc_register_plugin(osc_plugin_path(__FILE__), 'amazon_install');
    osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", 'amazon_uninstall');
    osc_add_hook(osc_plugin_path(__FILE__)."_configure", 'amazon_configure_link');

    osc_add_hook('uploaded_file', 'amazon_upload');
    osc_add_filter('resource_path', 'amazon_resource_path');
    osc_add_hook('regenerate_image', 'amazon_regenerate_image');
    osc_add_hook('regenerated_image', 'amazon_upload');
    osc_add_hook('delete_resource', 'amazon_delete_from_bucket');
    osc_add_hook('admin_menu', 'amazon_admin_menu');
    
?>