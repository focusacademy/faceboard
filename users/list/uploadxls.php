<?php
include_once('../../config.php');
error_reporting(E_ALL);
if(isset($_FILES["user_file"]) && $_FILES["user_file"]["size"]< 8388608 ) // LIMIT 8 MB
{	
	$newFileName=(isset($_REQUEST['college_id'])?$_REQUEST['college_id']:'');
	$newFileName.=(isset($_REQUEST['usr_type'])?$_REQUEST['usr_type']:'');
	$newFileName.=(isset($_REQUEST['batch_id'])?$_REQUEST['batch_id']:'');		
	
	if($newFileName=='') $newFileName='RAND_'.rand();
		
	$upload_file=$_FILES["user_file"]["name"];
	$ext=substr($upload_file,strripos($upload_file,'.'));
	$inputFileName="uploads/".$newFileName.$ext;
	move_uploaded_file($_FILES["user_file"]["tmp_name"],$inputFileName);
//	$inputFileName="kiot.xlsx";
	/** PHPExcel_IOFactory */
	require_once '../../common/libs/PHPExcel 1.7.6/Classes/PHPExcel/IOFactory.php';				
	$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
	$objReader = PHPExcel_IOFactory::createReader($inputFileType);	
	$objPHPExcel = $objReader->load($inputFileName);
	$ins_q="INSERT INTO `users`(`usr`,`college_id`,`course_id`,`batch_id`,`pswd`,`usr_type`,`status`) VALUES";
	$a=array();
	$sheets=$objPHPExcel->getSheetNames();
	foreach($sheets as $sheet_no=>$sheet_name)
	{
		 $objPHPExcel->setActiveSheetIndex($sheet_no);
		 $objsheet=$objPHPExcel->getActiveSheet();
		 $maxRow=$objsheet->getHighestRow();
		 $maxCol=8;
		 if($maxRow>2)// IF SHEET IS NOT EMPTY
		 {
			echo '<table>';
	 		//UID	NAME	COLLEGE ID	EMAIL	MOBILE	DEGREE	DEPT	 
			 for($i=2;$i<=$maxRow;$i++)
			 {			 	
				$name=trim(strip_tags($objsheet->getCellByColumnAndRow(1,$i)->getValue()));
				$course_id=trim(strip_tags($objsheet->getCellByColumnAndRow(5,$i)->getValue()));
				//$pswd="$1$wv0.3a5.$NvtzpzmZ10Tdv4/m7BiTa.";
			 	if($name)// CLUBBED
				{
					$a[]="('".$name."','8','".$course_id."','".($sheet_no+10)."','".crypt('1234')."','1','1')";
					echo '<tr>';
					for($j=0;$j<$maxCol;$j++)
					echo '<td>'.trim(strip_tags($objsheet->getCellByColumnAndRow($j,$i)->getValue())).'</td>';
					echo '<td><a class="label label-primary edit_users" data-toggle="modal" data-target="#add_users_dialog" >Edit</a><a class="label label-danger remove_users" target="_blank" ><i class="glyphicon glyphicon-remove"></li></a></td>';
					echo '</tr>';
				}	
				
			 }// FOR EACH ROWS
			echo '</table>';			 
			 
		 }// NUM OF ROWS > 1 first row is LABELS	 
		 
	}// EACH SHEET DESCRIBES EACH SECTION
	
	//echo $ins_q.implode(',',$a);
	
}// FILE EXISTS


?>