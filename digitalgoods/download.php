<?php

    require_once('../../../oc-load.php');
    $filename= Params::getParam('file');
    $file = osc_get_preference('upload_path', 'digitalgoods').$filename;
    $tmp = explode("_", $filename);
    
    $conn = getConnection();
    $download = $conn->osc_dbFetchResult("SELECT * FROM %st_item_dg_files WHERE fk_i_item_id = %d AND s_name = '%s' AND s_code = '%s'", DB_TABLE_PREFIX, $tmp[1], $tmp[2], $tmp[0]);
    

    if (isset($download['pk_i_id']) && file_exists($file)) {
        
        $downs = $conn->osc_dbFetchResult("SELECT * FROM %st_item_dg_downloads WHERE fk_i_file_id = %d", DB_TABLE_PREFIX, $download['pk_i_id']);
        if(isset($downs['i_downloads'])) { 
            $conn->osc_dbExec("UPDATE %st_item_dg_downloads SET i_downloads = %d WHERE fk_i_file_id = %d", DB_TABLE_PREFIX, ($downs['i_downloads']+1), $download['pk_i_id']);
        } else {
            $conn->osc_dbExec("INSERT INTO %st_item_dg_downloads (`fk_i_file_id`, `i_downloads`) VALUES (%d, 1)", DB_TABLE_PREFIX, $download['pk_i_id']);
        }
        
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        ob_clean();
        flush();
        readfile($file);
        exit;
    }
?>