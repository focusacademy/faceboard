<?php
include_once('../../config.php');
$db1=testDB(2);
$result=array('status'=>0,'css'=>'fail','msg'=>'Sorry some error occured. Please try again');
switch($_REQUEST['mode'])
{
	case 1: //LIST OF USERS
	if(isset($_REQUEST['usr_type']))
	{
		$where=array();
		if($_REQUEST['usr_type']==0 || $_REQUEST['usr_type']==1 )
		{

		$where[]=isset($_REQUEST['batch_id'])?"`users`.`batch_id`='".$_REQUEST['batch_id']."'":'1=1';
		}
		$where[]=isset($_REQUEST['batch_id'])&& $_REQUEST['batch_id']?" `users`.`batch_id` IN (".$_REQUEST['batch_id'].")":'1=1';
		$where[]=isset($_REQUEST['usr_type'])&& $_REQUEST['usr_type']?" `users`.`usr_type`='".$_REQUEST['usr_type']."'":'1=1';
		$where[]=isset($_REQUEST['college_id'])&&$_REQUEST['college_id']?" `users`.`college_id`='".$_REQUEST['college_id']."'":'1=1';	
		$where[]=isset($_SESSION['usr_type']) && $_SESSION['usr_type']<2?"`users`.`uid`='".$_SESSION['uid']."'":'1=1';

		if($_REQUEST['usr_type']==0 || $_REQUEST['usr_type']==1 )				
		$users_q="SELECT `users`.`uid` , `users`.`usr` , `users`.`ucid`,
				`users`.`email` , `users`.`mobile` ,`users`.`course_id`,'',''
				 FROM `users` 
				 WHERE ".implode(' AND ',$where)." ORDER BY `usr` ASC ";
		else
		$users_q="SELECT `users`.`uid` , `users`.`usr` , `users`.`ucid`,
				`users`.`email` , `users`.`mobile` , '','',''
				 FROM `users`  
				 WHERE ".implode(' AND ',$where)." ORDER BY `usr` ASC ";
		
		//echo $users_q;		 
		$users_res=mysql_query($users_q,$db1);		
		$users=array();
		while($users_r=mysql_fetch_row($users_res))
		$users[]=$users_r;
		
		$result=array('status'=>1,'users'=>$users);
	}	
	break;
	
	case 2: /// INSERT USERS
		$users=explode(';',preg_replace("/&.{0,}?;/",'',$_REQUEST['users']));
		$batch_id=(isset($_REQUEST['batch_id'])?$_REQUEST['batch_id']:'');
		$college_id=(isset($_REQUEST['college_id'])?$_REQUEST['college_id']:'');
		$usr_type=(isset($_REQUEST['usr_type'])?$_REQUEST['usr_type']:'0');		
		//echo $_REQUEST['users'];
		$ins_q="INSERT INTO `users`
				(`uid`,`usr`,`ucid`,`email`,`mobile`,`course_id`,`college_id`,`batch_id`,`pswd`,`usr_type`,`status`,`create_time`) VALUES ";
		$ins_array=array();
		foreach($users as $k=>$v)
		{
			//echo $v.'<hr/>';
			$ins_array[]="(".stripslashes($v).",'".$college_id."','".$batch_id."','','".$usr_type."','1','".time()."')";
		}

		$ins_q.=implode(' , ',$ins_array). 
		"ON DUPLICATE KEY UPDATE `usr`=VALUES(`usr`),`ucid`=VALUES(`ucid`),
		 `email`=VALUES(`email`),`mobile`=VALUES(`mobile`),
		 `course_id`=VALUES(`course_id`),`college_id`=VALUES(`college_id`),`batch_id`=VALUES(`batch_id`)";
		//echo $ins_q;
		mysql_query($ins_q,$db1);
		$uid=mysql_insert_id($db1);
		$no_users=mysql_affected_rows($db1);
				
		$result=array('status'=>1,'css'=>'alert alert-success','msg'=>'Successfully users added!','uid'=>$uid);
	break;
        	case 3: // DISABLE USER
	if(isset($_REQUEST['uid']) && $_SESSION['usr_type']==9)
	{
		$dis_q="UPDATE `users` SET `status`=0 WHERE `uid`='".$_REQUEST['uid']."'";
		mysql_query($dis_q,$db1);
		$result=array('status'=>1,'css'=>'success','msg'=>'Successfully disabled user');
	}
	break;
}// SWITCH


echo json_encode($result);

?>