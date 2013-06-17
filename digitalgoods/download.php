<?php
    require_once('../../../oc-load.php');
    $filename= Params::getParam('file');
    $file = osc_get_preference('upload_path', 'digitalgoods').$filename;
    $tmp = explode("_", $filename);

    $download = DGModel::newInstance()->getFileByItemNameCode(@$tmp[1], @$tmp[2], $tmp[0]);

    if (isset($download['pk_i_id']) && file_exists($file)) {
        DGModel::newInstance()->updateDownloads($download['pk_i_id'], $download['i_downloads']+1);

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        @ob_clean();
        flush();
        readfile($file);
        exit;
    }
?>