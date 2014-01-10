<?php
if(!session_id())session_start();
if(function_exists('date_default_timezone_set')) date_default_timezone_set("Asia/Kolkata");
$usr_type=array(0=>'Normal Users',1=>'College Students',2=>'TPO', 8=>'Staff',9=>'Admin');


//$g_url='http://localhost/facenow/';
$g_url='http://www.facenow.in/board/';
$g_cloud_front='http://d3cdw5wllrvc9g.cloudfront.net/';

  
function testDB($db_no=0)// ADMIN DATABASE
{
$db_no=0;
$dbname = array("facenow");
$dbhost = array("localhost");
$dbuser = array("root");
$dbpass = array("");


/*$dbname = array("facenow");
$dbhost = array("facenow.cr6hgkwixisc.ap-southeast-1.rds.amazonaws.com");
$dbuser = array("facenow");
$dbpass = array("FACEworks100");*/

$db1=mysql_connect($dbhost[$db_no],$dbuser[$db_no],$dbpass[$db_no]) ;
mysql_select_db($dbname[$db_no],$db1);
return $db1;
}// ADMIN DATABASE



//IS SAFE
function isSafe($data,$mode)
{
$regex='/\w*((\%27)|(\'))((\%6F)|o|(\%4F))((\%72)|r|(\%52))/ix';
if(!preg_match($regex,$data))
{
	if($mode==1)
	return 1;
	else if($mode==2 && $data!="")
	return 1;
	else
	return 0;
}
else
return 0;
}// is Safe

//GET IP
function getIP()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
      $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
      $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}// getIP



function perm_redirect($g_url,$mode)
{
	switch($mode)
	{
		case 0:// VALID USER UID
		if(!isset($_SESSION['uid'])) { header('location:'.$g_url); exit();}
		break;
		
		case 1: // ADMIN REDIRECTOR
		if(!isset($_SESSION['usr_type']) || $_SESSION['usr_type']!=9) 
		{ header('location:'.$g_url.'403.html'); exit();}
		break;
		
		case 2: // ADMIN OR STAFF REDIRECTOR
		if(!isset($_SESSION['usr_type']) || ($_SESSION['usr_type']!=9 && $_SESSION['usr_type']!=8) ) 
		{ header('location:'.$g_url.'403.html'); exit();}
		break;
				
		default:
		header('location:'.$g_url.'403.html');
		break;
				
	}
}

function perm_check($mode)
{
	switch($mode)
	{	
	  case 0:// USER
	  if(!isset($_SESSION['uid'])) return 0;
	  break;

	  case 1:// ADMIN
	  if(!isset($_SESSION['usr_type']) || $_SESSION['usr_type']!=9) return 0;
	  break;
	  
	  case 2:// FACE STAFF
	  if(!isset($_SESSION['usr_type']) || $_SESSION['usr_type']!=8) return 0;
	  break;
	  
	  case 3://TPO
	  if(!isset($_SESSION['usr_type']) || $_SESSION['usr_type']!=2) return 0;
	  break;	
	  
	  case 4://College Users
	  if(!isset($_SESSION['usr_type']) || $_SESSION['usr_type']!=1) return 0;
	  break;	
	  	    
	  case 5://Normal Users
	  if(!isset($_SESSION['usr_type']) || $_SESSION['usr_type']!=0) return 0;
	  break;		  
	}
	
	return 1;
}