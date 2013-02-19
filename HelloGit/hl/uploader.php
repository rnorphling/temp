<?php
$UploadDirectory	= 'pdf_reports/'; //Upload Directory, ends with slash & make sure folder exist

// replace with your mysql database details

$MySql_username 	= "root"; //mysql username
$MySql_password 	= "root"; //mysql password
$MySql_hostname 	= "localhost"; //hostname
$MySql_databasename = 'mlin'; //databasename


if (!@file_exists($UploadDirectory)) {
	//destination folder does not exist
	die("Make sure Upload directory exist!");
}
if($_POST){	
	if(!isset($_POST['patient_id']) || strlen($_POST['patient_id'])<1){
		//required variables are empty
		die("Patient ID is empty!");
	}
	if(!isset($_POST['uuid']) || strlen($_POST['uuid'])<1){
		//required variables are empty
		die("UUID is empty!");
	}
	if(!isset($_POST['modality_code']) || strlen($_POST['modality_code'])<1){
		//required variables are empty
		die("Modality Code is empty!");
	}
	if(!isset($_FILES['pdf_report'])){
		//required variables are empty
		die("File is empty!");
	}
	if($_FILES['pdf_report']['error']){
		//File upload error encountered
		die(upload_errors($_FILES['pdf_report']['error']));
	}

	$patient_id = $_POST['patient_id'];  
	$action = $_POST['action'];  
	$uuid = $_POST['uuid'];  
	$modality_code = $_POST['modality_code'];
	$exam_date = $_POST['exam_date'];  
	$study_date = $_POST['study_date']; 

	$file_name			= strtolower($_FILES['pdf_report']['name']); //uploaded file name
	$file_tmp_name		= strtolower($_FILES['pdf_report']['tmp_name']);
	//$FileTitle			= mysql_real_escape_string($_POST['mName']); // file title
	//$ImageExt			= substr($file_name, strrpos($file_name, '.')); //file extension
	$file_type			= $_FILES['pdf_report']['type']; //file type
	$file_size			= $_FILES['pdf_report']["size"]; //file size
	$RandNumber   		= rand(0, 9999999999); //Random number to make each filename unique.
	$uploaded_date		= date("Y-m-d H:i:s");
	$uploaded_date2		= date("YmdHis");
	$fp 				= fopen($file_tmp_name, 'r');
	$content			= fread($fp,filesize($file_tmp_name));
	$content			= addslashes($content);
	fclose($fp);
	
	switch(strtolower($file_type))
	{
		//allowed file types
		case 'image/png': //png file
		case 'image/gif': //gif file 
		case 'image/jpeg': //jpeg file
		case 'application/pdf': //PDF file
		case 'application/msword': //ms word file
		case 'application/vnd.ms-excel': //ms excel file
		case 'application/x-zip-compressed': //zip file
		case 'text/plain': //text file
		case 'text/html': //html file
		break;
		default:
			die('Unsupported File!'); //output error
		}


	//File Title will be used as new File name
		$new_file_name = $patient_id.'_'.$uploaded_date2.'.pdf';
   //Rename and save uploded file to destination folder.
		if(move_uploaded_file($_FILES['pdf_report']["tmp_name"], $UploadDirectory . $new_file_name ))
		{
		//connect & insert file record in database
			$mysqli = new mysqli($MySql_hostname, $MySql_username, $MySql_password, $MySql_databasename);
			if (mysqli_connect_errno()) {
				die('Could not connect: ' . mysqli_connect_error());
			}

			$query=" INSERT INTO ha_tbl ( patient_id, action, uuid, modality_code, exam_date, study_date, pdf_blob )  VALUES ( '$patient_id', '$action', '$uuid', '$modality_code', '$exam_date', '$study_date' ,'$content') ";

			if($mysqli->query($query)){
				echo "Row $mysqli->insert_id is inserted successfully!";
			}
			else{ 
				echo "An error occurred! ". mysqli_error(); 
			}
			$mysql->close();

			die('Success! File Uploaded.');

		}else{
			die('error uploading File!');
		}
	}

//function outputs upload error messages, http://www.php.net/manual/en/features.file-upload.errors.php#90522
	function upload_errors($err_code) {
		switch ($err_code) { 
			case UPLOAD_ERR_INI_SIZE: 
			return 'The uploaded file exceeds the upload_max_filesize directive in php.ini'; 
			case UPLOAD_ERR_FORM_SIZE: 
			return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form'; 
			case UPLOAD_ERR_PARTIAL: 
			return 'The uploaded file was only partially uploaded'; 
			case UPLOAD_ERR_NO_FILE: 
			return 'No file was uploaded'; 
			case UPLOAD_ERR_NO_TMP_DIR: 
			return 'Missing a temporary folder'; 
			case UPLOAD_ERR_CANT_WRITE: 
			return 'Failed to write file to disk'; 
			case UPLOAD_ERR_EXTENSION: 
			return 'File upload stopped by extension'; 
			default: 
			return 'Unknown upload error'; 
		} 
	} 
	?>