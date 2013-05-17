<?php if (!defined('OC_ADMIN') || OC_ADMIN!==true) exit('Access is not allowed.');

switch(Params::getParam("subaction")) {

    case 'parsead':
        $num_ad = Params::getParam("num_ad");
        $file = Params::getParam("importfile");
        if(is_numeric($num_ad) && $file!='') {
            $cat_info = Params::getParam("cat_info");
            if(!is_array($cat_info)) {
                $cat_info = array();
            }
            $meta_info = Params::getParam("meta_info");
            if(!is_array($meta_info)) {
                $meta_info = array();
            }
            list($success, $cat_info, $meta_info) = adimporter_adfromfile($file, $num_ad, $cat_info, $meta_info);
            echo json_encode(array('error' => $success, 'cat_info' => $cat_info, 'meta_info' => $meta_info));
        } else {
            echo json_encode(array('error' => 1, 'msg' => __('Invalid ad number', 'adimporter')));
        }
        break;

    default:
        echo json_encode(array('error' => 1, 'msg' => __('No action defined', 'adimporter')));
        break;
}


?>
