<?php

    define('ABS_PATH', dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/');
    require_once ABS_PATH . 'oc-load.php';

    $data = Mpayment_get_custom(Params::getParam('extra'));
    if($data['itemid']=='dash') { // PACK PAYMENT FROM USER'S DASHBOARD
        $url = osc_user_dashboard_url();
    } else {
        $item     = Item::newInstance()->findByPrimaryKey($data['itemid']);
        $category = Category::newInstance()->findByPrimaryKey($item['fk_i_category_id']);
        View::newInstance()->_exportVariableToView('category', $category);
        $url = osc_search_category_url();
    }
    
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <script type="text/javascript" src="https://www.paymentobjects.com/js/external/dg.js"></script>
        <title><?php echo osc_page_title(); ?></title>
    </head>
    <body>
        <?php 
            if(osc_get_preference('paypal_standard', 'payment')==1) {
                osc_add_flash_error_message(__('You cancel the payment process or there was an error. If the error continue, please contact the administrator', 'payment'));
                payment_js_redirect_to($url);
            }
        ?>
        <script type="text/javascript">
            <?php if($url!='') { ?>
            top.rd.innerHTML = '<?php _e('You cancel the payment process or there was an error. If the error continue, please contact the administrator', 'payment'); ?>.<br/><br/><?php _e('If you do not want to continue the process', 'payment'); ?> <a href="<?php echo $url; ?>" /><?php _e('click here', 'payment'); ?></a>';
            <?php }; ?>
            top.dg_<?php echo $data['random'];?>.closeFlow();
        </script>
    </body>
</html>