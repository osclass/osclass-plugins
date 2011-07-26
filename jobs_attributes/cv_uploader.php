<?php

/**
 * Handle file uploads via XMLHttpRequest
 */
class qqUploadedFileXhr {
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) {    
        $input = fopen("php://input", "r");
        $temp = tmpfile();
        $realSize = stream_copy_to_stream($input, $temp);
        fclose($input);
        
        if ($realSize != $this->getSize()){            
            return false;
        }
        
        $target = fopen($path, "w");        
        fseek($temp, 0, SEEK_SET);
        stream_copy_to_stream($temp, $target);
        fclose($target);
        
        return true;
    }
    function getName() {
        return $_GET['qqfile'];
    }
    function getSize() {
        if (isset($_SERVER["CONTENT_LENGTH"])){
            return (int)$_SERVER["CONTENT_LENGTH"];            
        } else {
            throw new Exception('Getting content length is not supported.');
        }      
    }   
}

/**
 * Handle file uploads via regular form post (uses the $_FILES array)
 */
class qqUploadedFileForm {  
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) {
        if(!move_uploaded_file($_FILES['qqfile']['tmp_name'], $path)){
            return false;
        }
        return true;
    }
    function getName() {
        return $_FILES['qqfile']['name'];
    }
    function getSize() {
        return $_FILES['qqfile']['size'];
    }
}

class qqFileUploader {
    private $allowedExtensions = array();
    private $sizeLimit = 10485760;
    private $file;

    function __construct(array $allowedExtensions = array(), $sizeLimit = 10485760){        
        $allowedExtensions = array_map("strtolower", $allowedExtensions);
            
        $this->allowedExtensions = $allowedExtensions;        
        $this->sizeLimit = $sizeLimit;
        
        $this->checkServerSettings();       

        if (isset($_GET['qqfile'])) {
            $this->file = new qqUploadedFileXhr();
        } elseif (isset($_FILES['qqfile'])) {
            $this->file = new qqUploadedFileForm();
        } else {
            $this->file = false; 
        }
    }
    
    private function checkServerSettings(){        
        $postSize = $this->toBytes(ini_get('post_max_size'));
        $uploadSize = $this->toBytes(ini_get('upload_max_filesize'));        
        
        if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit){
            $size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';             
            die("{'error':'increase post_max_size and upload_max_filesize to $size'}");    
        }        
    }
    
    private function toBytes($str){
        $val = trim($str);
        $last = strtolower($str[strlen($str)-1]);
        switch($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;        
        }
        return $val;
    }
    
    /**
     * Returns array('success'=>true) or array('error'=>'error message')
     */
    function handleUpload($uploadDirectory, $replaceOldFile = FALSE){
        if (!is_writable($uploadDirectory)){
            return array('error' => __("Server error. Upload directory isn't writable", "jobs_attributes"));
        }
        
        if (!$this->file){
            return array('error' => __('No files were uploaded', "jobs_attributes"));
        }
        
        $size = $this->file->getSize();
        
        if ($size == 0) {
            return array('error' => __('File is empty', "jobs_attributes"));
        }
        
        if ($size > $this->sizeLimit) {
            return array('error' => __('File is too large', "jobs_attributes"));
        }
        
        $pathinfo = pathinfo($this->file->getName());
        $filename = date("YmdHis") . "_" . $pathinfo['filename'];
        $ext = $pathinfo['extension'];

        if($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)){
            return array('error' => __('File has an invalid extension', "jobs_attributes"));
        }
        
        if ($this->file->save($uploadDirectory . $filename . '.' . $ext)){
            $params = array();
            $params['subject'] = sprintf(__('Someone sent you his/her resume. ( %s )', 'jobs_attributes'), osc_item_title());
            $params['body'] = sprintf(__('Someone sent you his/her resume. You could find it attached on this email, you could find the job offer here: %s', 'jobs_attributes'), osc_item_url());
            $params['alt_body'] = $params['body'];
            $params['attachment'] = $uploadDirectory . $filename . '.' . $ext;

            if(osc_get_preference('send_me_cv', 'jobs_plugin')) {
                $params['to'] = osc_get_preference('cv_email', 'jobs_plugin');
            } else {
                $params['to'] = osc_item_contact_email();
            }

            if(@osc_sendMail($params)) {
                $error = 0;
            } else {
                $error = 1;
            }

            @unlink($uploadDirectory . $filename . '.' . $ext);
            if($error==0) {
                return array('success' => true);
            } else {
                return array('error'=> __('Could not save uploaded file. The upload was cancelled, or server error encountered', 'jobs_attributes'));
            }
        } else {
            return array('error'=> __('Could not save uploaded file. The upload was cancelled, or server error encountered', 'jobs_attributes'));
        }
        
    }    
}

if(osc_get_preference('allow_cv_upload', 'jobs_plugin')=='1' && ((osc_get_preference('allow_cv_unreg', 'jobs_plugin')=='1' && !osc_is_web_user_logged_in()) || osc_is_web_user_logged_in())) {
    View::newInstance()->_exportVariableToView('item', Item::newInstance()->findByPrimaryKey(Params::getParam('id')));

    // list of valid extensions, ex. array("jpeg", "xml", "bmp")
    $allowedExtensions = array();
    // max file size in bytes
    $sizeLimit = 2 * 1024 * 1024;

    $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
    $result = $uploader->handleUpload(CONTENT_PATH . 'uploads/');
    // to pass data through iframe you will need to encode all html tags
    echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
} else { 
    echo htmlspecialchars(json_encode(array('error'=> __('Could not save uploaded file. The upload was cancelled, or server error encountered', 'jobs_attributes'))), ENT_NOQUOTES);
}

?>