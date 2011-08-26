<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>S3 tutorial</title>
        <link href="style.css" rel="stylesheet" type="text/css">
    </head>

<body>
    	<?php
			//include the S3 class
			if (!class_exists('S3'))require_once('S3.php');
			
			//AWS access info
			if (!defined('awsAccessKey')) define('awsAccessKey', 'CHANGE THIS');
			if (!defined('awsSecretKey')) define('awsSecretKey', 'CHANGE THIS TOO');
			
			//instantiate the class
			$s3 = new S3(awsAccessKey, awsSecretKey);
			
			//check whether a form was submitted
			if(isset($_POST['Submit'])){
			
				//retreive post variables
				$fileName = $_FILES['theFile']['name'];
				$fileTempName = $_FILES['theFile']['tmp_name'];
				
				//create a new bucket
				$s3->putBucket("yourbucket", S3::ACL_PUBLIC_READ);
				
				//move the file
				if ($s3->putObjectFile($fileTempName, "yourbucket", $fileName, S3::ACL_PUBLIC_READ)) {
					echo "<strong>We successfully uploaded your file.</strong>";
				}else{
					echo "<strong>Something went wrong while uploading your file... sorry.</strong>";
				}
			}
		?>
<h1>Upload a file</h1>
<p>Please select a file by clicking the 'Browse' button and press 'Upload' to start uploading your file.</p>
   	<form action="" method="post" enctype="multipart/form-data" name="form1" id="form1">
      <input name="theFile" type="file" />
      <input name="Submit" type="submit" value="Upload">
	</form>
<h1>All uploaded files</h1>
<?php
	// Get the contents of our bucket
	$contents = $s3->getBucket("yourbucket");
	foreach ($contents as $file){
	
		$fname = $file['name'];
		$furl = "http://yourbucket.s3.amazonaws.com/".$fname;
		
		//output a link to the file
		echo "<a href=\"$furl\">$fname</a><br />";
	}
?>
</body>
</html>