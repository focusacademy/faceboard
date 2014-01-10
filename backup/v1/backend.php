<?php
include_once("../config.php");
perm_redirect($g_url,0);

$result=array('status'=>0,'msg'=>'<strong>Failed!</strong> Sorry, please try again.','css'=>'alert-danger');
if(isset($_REQUEST['mode']))
{
	$mode=$_REQUEST['mode'];
	$db=testDB(1);
	if(isset($_SESSION['usr_type']))
	switch ($mode) {
		case 10: // FETCH SESSIONS

			$uid=$_SESSION['uid'];
			$where=array();
			//IF STUDENT 
			if($_SESSION['usr_type']<2 )
			{
				//FETCH BATCH OF THE STUDENT
				$batch_q="SELECT `batch_id` FROM `users` WHERE `uid`='".$_SESSION['uid']."'";	
				$batch_res=mysql_query($batch_q,$db);				
				$batch_where=" 1=1 ";
				while($batch_r=mysql_fetch_array($batch_res))
				{
					$batch_where="`board_map_sessions`.`batch_id`='".$batch_r['batch_id']."' "; 
				}
					//LIVE SESSIONS BELONGING TO USER BATCH or USER
					$where[]="(".$batch_where." OR `uids` REGEXP ',".$uid.",' ) AND `board_map_sessions`.`status` IN (1,3) ";
			}
			else if($_SESSION['usr_type']>7)
			$where[]="`board_map_sessions`.`trainer`='".$_SESSION['uid']."' ";
				
			//FETCH SESSIONS		
			$s_q="	SELECT
					`session_id`,
					`board_ref_sessions`.`session_code`,
					`board_map_sessions`.`status`,
					`session_name`,
					`college`.`college_name`,
					`batches`.`batch_name`,
					`batches`.`batch_year`,
					DATE_FORMAT(`scheduled_time`,'%D %b %Y, %l:%i %p')as `scheduled_time`
					FROM 

					`board_map_sessions`,
					`board_ref_sessions`,
					`batches`,`college`

					WHERE 
					`board_map_sessions`.`session_code`=`board_ref_sessions`.`session_code`
					AND 
					`board_map_sessions`.`batch_id`=`batches`.`batch_id`
					AND
					`batches`.`college_id`=`college`.`college_id`
					AND	".implode(' AND ',$where);	
					//echo $s_q;	
			$sessions=array();
			$s_res=mysql_query($s_q,$db);
			while($s_r=mysql_fetch_array($s_res))
			{
				$sessions[$s_r['session_id']]=$s_r;				
			}
			$result=array('status'=>0,'msg'=>'<strong>Yet to start!</strong> No sessions available.','css'=>'alert-info');
			if(count($sessions)>0)
			$result=array('status'=>1,'session_list'=>$sessions,
				'usr_type'=>$_SESSION['usr_type'],
				'msg'=>'<strong>Success!</strong> Sessions fetched.','css'=>'alert-success');			
		break;
		
		case 11:
		/*
		SAVE SESSION
		INPUT: session_id,session_status
		OUTPUT: SAVE SESSION
		*/
			if($_REQUEST['session_status']==1)
			$session_q="
					  UPDATE `board_map_sessions` SET `status`= CASE 
						WHEN `session_id` IN ('".$_REQUEST['session_id']."') THEN 1
						WHEN `status` IN (1) AND `session_id` NOT IN ('".$_REQUEST['session_id']."') THEN 2
						END
						WHERE `trainer`='".$_SESSION['uid']."'
						";
			else
			$session_q="UPDATE `board_map_sessions` SET `status`= '".$_REQUEST['session_status']."'
			WHERE `session_id`='".$_REQUEST['session_id']."'";

			mysql_query($session_q,$db);
			//echo $session_q;
			$result=array('status'=>1);
		break;

		case 12://SAVE SESSION FEEDBACK

			$session_q="
			INSERT INTO `board_log_sessions`(`session_id`,`uid`,`rating_star`,`feedback`)
			VALUES ('".$_REQUEST['session_id']."','".$_SESSION['uid']."','".$_REQUEST['rating_star']."','".$_REQUEST['feedback']."') 
			ON DUPLICATE KEY UPDATE `rating_star`=VALUES(`rating_star`),  `feedback`=VALUES(`feedback`)";
			//echo $sessions_q;
			mysql_query($session_q,$db);			
			$result=array('status'=>1,'css'=>'alert alert-success','msg'=>'<strong>Thanks!!</strong> for sharing your feedback.');


		break;

		case 20: 
		/*
		FETCH CONCEPTS LIST 
		INPUT: session_code,session_id
		OUTPUT: list of concepts associated with this session_id
		*/
		if( isset($_REQUEST['session_id']))
		{			
			$concepts_q="SELECT 
			`board_ref_concepts`.`concept_name`,
			`board_ref_concepts`.`concept_code`,
			`board_map_concepts`.`concept_id`,
			`board_map_concepts`.`feedback_score`,
			`board_map_concepts`.`assessment_score`,
			`board_map_concepts`.`status`
			FROM `board_ref_concepts`,
			`board_map_concepts` WHERE 
			`board_map_concepts`.`concept_code`=`board_ref_concepts`.`concept_code` AND
			`board_map_concepts`.`session_id`='".$_REQUEST['session_id']."'";
			$concepts_res=mysql_query($concepts_q,$db);
			$concepts=array();
			while($concepts_r=mysql_fetch_array($concepts_res))
			{
				$concepts[]=$concepts_r;
			}

			$result=array('status'=>1,'usr_type'=>$_SESSION['usr_type'],'concepts'=>$concepts);

		}			
		break;
		case 21: 
		/*
		SAVE CONCEPTS 
		INPUT: session_id,concept_code,status		
		*/
		if(isset($_REQUEST['concept_id']) && isset($_REQUEST['status']) )
		{			
			$status_q="
			UPDATE `board_map_concepts` SET `status`='".$_REQUEST['status']."' 
			WHERE `concept_id`='".$_REQUEST['concept_id']."' ";
			echo $status_q;
			mysql_query($status_q,$db);			
			$result=array('status'=>1);

		}			
		break;	
		

		/******** SAVE FEEDBACK **/	
		case 30: 
		/*
		//SAVE USER FEEDBACK
		INPUT: SESSION ID, CONCEPT CODE, like, understand		
		*/
		if(isset($_REQUEST['concept_id']) && isset($_REQUEST['feedback_like']) && isset($_REQUEST['feedback_understand']) )
		{			
			$concepts_q="
			INSERT INTO `board_log_concepts`(`concept_id`,`uid`,`feedback_like`,`feedback_understand`)
			VALUES ('".$_REQUEST['concept_id']."','".$_SESSION['uid']."','".$_REQUEST['feedback_like']."','".$_REQUEST['feedback_understand']."') 
			ON DUPLICATE KEY UPDATE `feedback_like`=VALUES(`feedback_like`),  `feedback_understand`=VALUES(`feedback_understand`)";
			//echo $concepts_q;
			mysql_query($concepts_q,$db);			
			$result=array('status'=>1,'css'=>'alert alert-success','msg'=>'<strong>Thanks!!</strong> for sharing your feedback.');
		}
		break;

		case 31:
		if(isset($_REQUEST['concept_id']) )
		{			
		/*
			AGGREGATE FEEDBACKS
			INPUT:concept_id
		*/
			$feedback_q="SELECT
						SUM(`feedback_like`='1') as`feedback_like`,						
						SUM(`feedback_understand`='1') as `feedback_understand_yes`,
						SUM(`feedback_understand`='0') as `feedback_understand_no`,						
						SUM(`feedback_understand`='2') as `feedback_understand_yes_doubt`
						FROM `board_log_concepts`
						WHERE `concept_id`='".$_REQUEST['concept_id']."'
						GROUP BY `concept_id`";
			$feedback_res=mysql_query($feedback_q,$db);		
			//echo $feedback_q;	
			if(mysql_num_rows($feedback_res)>0)
			{
				$feedback=mysql_fetch_row($feedback_res);
				
				$update_q="UPDATE `board_map_concepts` SET `feedback_score`='".implode(',',$feedback)."'
						   WHERE `concept_id`='".$_REQUEST['concept_id']."' ";

				mysql_query($update_q);
			}
			$result=array('status'=>1,'feedback_score'=>implode(',',$feedback));
		}
		break;

		case 40://FETCH QUES
			//FETCH QUES SET, DURATION
			//If available {TEST RESPONSE, START TIME} 
			$test_q="
					SELECT T1.`test_ques` as `test_ques`,T1.`test_duration`,T2.`test_ans`,T2.`test_start_time`
					FROM
						(					
						SELECT					
						`test_ques`,`test_duration`,`concept_id`
						FROM 
						`board_map_concepts` LEFT JOIN `board_ref_concepts` 
						ON 
						`board_map_concepts`.`concept_code`=`board_ref_concepts`.`concept_code`
						WHERE
						`board_map_concepts`.`concept_id`='".$_REQUEST['concept_id']."'
						) AS T1
					LEFT JOIN 
						(
						SELECT `concept_id`,`test_ans`,`test_start_time`
						FROM `board_log_concepts`
						WHERE `concept_id`='".$_REQUEST['concept_id']."'
						AND `uid`='".$_SESSION['uid']."'
						) AS T2
					ON T1.`concept_id`=T2.`concept_id`";
			//echo $test_q;
			$test_res=mysql_query($test_q,$db);
			$test=array();
			while($test_r=mysql_fetch_array($test_res))
			{ $test=$test_r; }
			
			$time_left=intval($test['test_duration'])*60-( strtotime($test['test_start_time'])? time()-strtotime($test['test_start_time']) : 0 );
		
			//FETCH QUESTIONS
			$ques_q="SELECT * FROM `board_test_ques` WHERE`ques_id` IN(".$test['test_ques'].")";
			//echo $ques_q;
			$ques=array();
			$ques_res=mysql_query($ques_q,$db);
			while($ques_r=mysql_fetch_array($ques_res))
			{ $ques[]=$ques_r; }
			
			$result= array('status' =>1 ,'test'=>$test,'ques'=>$ques ,'time_left'=>$time_left);
		break;

		case 41: //SAVE START TIME
			$ans_q="UPDATE `board_log_concepts` 
					SET `test_start_time`='".date("Y-m-d H:i", time())."'
					WHERE `concept_id`='".$_REQUEST['concept_id']."'
					AND `uid`='".$_SESSION['uid']."' 
					AND (`test_start_time`='' OR `test_start_time`='RESET')";
			mysql_query($ans_q,$db);

			if(mysql_affected_rows($db)>0)	$result=array('status'=>1);
		break;

		case 42://SAVE ANSWERS

			$ans_q="UPDATE `board_log_concepts` 
					SET `test_ans`='".mysql_real_escape_string($_REQUEST['test_ans'])."' ,
						`test_last_time`='".date("Y-m-d H:i", time()) ."' 
					WHERE `concept_id`='".$_REQUEST['concept_id']."'
					AND `uid`='".$_SESSION['uid']."' ";
			mysql_query($ans_q,$db);

			$result=array('status'=>1,'result'=>$_REQUEST['result']);
		break;

		case 43://GENERATE RESULTS

			$ans_q="SELECT `test_ans` FROM `board_log_concepts` 
					WHERE `concept_id`='".$_REQUEST['concept_id']."'
					AND `uid`='".$_SESSION['uid']."' ";
			$ans=array();
			$ans_res=mysql_query($ans_q,$db);
			while($ans_r=mysql_fetch_array($ans_res))
			{
				$ans=explode(';',$ans_r['test_ans']);
			}

			//QUESTIONS
			$ques_q="SELECT `board_ref_concepts`.`test_ques`
						FROM `board_ref_concepts`,`board_map_concepts`
						WHERE `board_map_concepts`.`concept_code` = `board_ref_concepts`.`concept_code`
						AND `board_map_concepts`.`concept_id`='".$_REQUEST['concept_id']."'";
			$ques_res=mysql_query($ques_q,$db);
			$ques_res=mysql_query($ques_q,$db);
			while($ques_r=mysql_fetch_array($ques_res))
			{
				$ques=$ques_r['test_ques'];
			}


			$key_q="SELECT `ques_id`,`ans` FROM `board_test_ques`
					WHERE `ques_id` IN (".$ques.")";
			//echo $key_q;
			$key_res=mysql_query($key_q,$db);
			$key_res=mysql_query($key_q,$db);
			while($key_r=mysql_fetch_array($key_res))
			{
				$key[$key_r['ques_id']]=$key_r['ans'];
			}

			$evaluation=array(0,0,0);// CORRECT WRONG UNAMSWERED

			foreach($ans as $k=>$v)
			{

				$response=explode(',',$v);			
				//print_r($response);
				if($response[1]=='')//UNANSWERED
				$evaluation[2]++;				
				else if($response[1]==$key[$response[0]])//CORRECT
				$evaluation[0]++;
				else //WRONG
				$evaluation[1]++;
			}

			//UPDATE SCORE
			$up_q="UPDATE `board_log_concepts` 
					SET `assessment_score`='".implode(',',$evaluation)."' 						
					WHERE `concept_id`='".$_REQUEST['concept_id']."'
					AND `uid`='".$_SESSION['uid']."' ";
			mysql_query($up_q,$db);

			$result=array('status'=>1,'css'=>'alert alert-success','msg'=>'Successfully saved your responses!');

		break; 

		case 44:
		/*
			AGGREGATE SCORES
		*/
			$scores_q="SELECT `users`.`usr`,`users`.`uid`,`test_ans`,`assessment_score`
						FROM `board_log_concepts`,`users` 
						WHERE 
						`board_log_concepts`.`uid`=`users`.`uid`
						AND 
						`concept_id`='".$_REQUEST['concept_id']."'";
			$scores=array();
			$scores_res=mysql_query($scores_q,$db);
			while($scores_r=mysql_fetch_array($scores_res))
			{ $scores[]=$scores_r; }

			$result=array('status'=>1,'scores'=>$scores);

		break;

		case 90:// PINGER

			$where=array();
			$where[]=" `board_map_sessions`.`session_id` = `board_map_concepts`.`session_id` ";
			$where[]=" `board_map_sessions`.`session_id`='".$_REQUEST['session_id']."' ";
			(isset($_REQUEST['concept_id']) && $_REQUEST['concept_id'] )?$where[]="`board_map_concepts`.`concept_id`='".$_REQUEST['concept_id']."' ":"" ;
			$ping_q="SELECT  
					`board_map_sessions`.`session_id`,
					`board_map_sessions`.`status` as session_status,
					`concept_id`,
					`board_map_concepts`.`status` as concept_status
					FROM `board_map_concepts`,`board_map_sessions`
					WHERE ".implode(' AND ',$where);
				
			//echo $ping_q;
			$ping_res=mysql_query($ping_q,$db);			
			$ping=mysql_fetch_array($ping_res);			

			if(mysql_num_rows($ping_res)>0)
			$result=array('status'=>1,'ping'=>$ping);

		break;
	}	

}//MODE SET
echo json_encode($result);


?>