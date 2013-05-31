<?php

    require_once('../../../oc-load.php');
    $itemId = Params::getParam('item');
    $lang = Params::getParam('lang');
    if($lang=='') {
        $lang = osc_current_user_locale();
    }

    if($itemId!='') {

        $item = Item::newInstance()->findByPrimaryKey($itemId);
        $item['s_title'] = $item['locale'][$lang]['s_title'];
        $item['s_description'] = $item['locale'][$lang]['s_description'];
        
        
        View::newInstance()->_exportVariableToView('item', $item);
        
        $filename = osc_item_id()."_".osc_sanitizeString(osc_item_title())."_".$lang.".pdf";
        $path = osc_get_preference('upload_path', 'printpdf').$filename;

        
        //@unlink($path);
        if(!file_exists($path)) {
            require_once("template.php");
        }
        
        header('Content-Description: PDF Show');
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename='.basename($path));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path));
        @ob_clean();
        flush();
        readfile($path);
        exit;
    }
?>