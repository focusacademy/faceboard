<?php
include_once('config.php');
if(isset($_SESSION['uid'])) header('location:index.php');
$result = array('status' => 0);
if (isset($_REQUEST['email']) && isset($_REQUEST['pswd'])  && isSafe(trim($_REQUEST['email']), 2) && isSafe(trim($_REQUEST['pswd']), 2)) 
{	
    $db = testDB(2);
    $result = array('status' => -1, 'msg' => 'Wrong email id or password', 'css' => 'alert alert-danger');
    $next_url = '';
    $s_q = "SELECT * FROM `users` 
	  WHERE `email`='" . mysql_real_escape_string($_REQUEST['email']) . "' OR `uid`='" . mysql_real_escape_string($_REQUEST['email']) . "' ";
    $s_res = mysql_query($s_q, $db);
    if (mysql_num_rows($s_res) > 0) 
    {
        $s_r = mysql_fetch_array($s_res);
        if ($s_r['status'] == '0')
            $result = array('status' => -1, 'msg' => 'Your account has been disabled.<br/>Please contact admin.', 'css' => 'alert alert-warning');
        else if ($s_r['verification_status'] == '1')
            $result = array('status' => -1, 'msg' => 'Please verify your account by clicking the verification link sent to your email', 'css' => 'alert alert-info');
        else 
        {

            $pswd = $_REQUEST['pswd'];
            if ($s_r['pswd'] == '') 
            {// PASSWORD NOT SET YET
                $s_r['pswd'] = crypt(generate_pswd($s_r['uid']));
                $pass_q = "UPDATE `users` SET `pswd`='" . $s_r['pswd'] . "' WHERE `uid`='" . $s_r['uid'] . "'";
                mysql_query($pass_q, $db);
            }
            if (crypt($pswd, substr($s_r['pswd'], 0, 12)) == $s_r['pswd']) 
            {
                $_SESSION['uid'] = $s_r['uid'];
                $_SESSION['usr'] = $s_r['usr'];                            
                $_SESSION['usr_type'] = $s_r['usr_type'];
                $_SESSION['batches'] = $s_r['batch_id'];  
                if($s_r['email'] && $s_r['usr'] )          
                $result = array('status' => 1,'welcome'=>0,'css'=>'alert alert-success','msg'=>'Successfully logged in! <br/> Loading FACEBoard... Please wait !');
            	else
				$result = array('status' => 1,'welcome'=>1,'css'=>'alert alert-success','msg'=>'Successfully logged in! <br/> Loading FACEBoard... Please wait !');
            }
        }
     }// USER EXXISTS
 }// SAFE INPUT 

?>
<!DOCTYPE HTML>
<html>
<head>
	<title>FACEBoard Login</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href='bootstrap/css/bootstrap.min.css' rel='stylesheet' type='text/css'>	
	<link href='common/js/rateit/src/rateit.css' rel='stylesheet' type='text/css'>		
	<link href='http://fonts.googleapis.com/css?family=Shadows+Into+Light+Two' rel='stylesheet' type='text/css'>
	<style type="text/css">
	body
	{
		background: url(common/images/bg-grey.png);
		border:none;
		font-family: 'Shadows Into Light Two', cursive;
		font-size: 2em;
		color: #fff;		
	}
	.board_logo
	{
		background: url(common/images/faceboard.png);
		width: 280px;
		height: 50px;
		margin-top: 10px;
	}
	</style>
</head>
<body>
<div class="container">
	<div class="row">
		<div class="col-md-3">
			<div class="board_logo"></div>
		</div>
	</div><!-- BANNER --><br/><br/><br/>
	<div class="panel panel-default row col-md-offset-4 col-md-4">
		<div class="panel-heading  clearfix">
			FACEBOARD Login
		</div>
		<div class="panel-body  clearfix">	
		<?php if($result['status']!=1)
		{
		?>		
		<form class="form clearfix" method="post">			
			<div class="input-group">			  
			  <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
			  <input type="text" class="form-control" placeholder="User ID / Email ID" name="email" required >
			</div><br/>
			<div class="input-group">
			  <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
			  <input type="password" class="form-control" placeholder="Password"  name="pswd" required>
			</div><br/>
			<input type="submit" class="btn btn-primary pull-right" value="Login" />
		</form>
		<?php } ?>

		<br/>
		<?php
			if($result['status']!=0)
			echo "<div class='".$result['css']."'>".$result['msg']."</div>";
		?>
		</div><!-- LOGIN BODY -->
	</div><!-- LOGIN PANEL -->
</div>
</body>
<script type="text/javascript">
<?php
if($result['status']==1)
{ 
	if($result['welcome']==1)
	echo "setTimeout(function(){ location.replace('welcome.php');},1000);";
	else
	echo "setTimeout(function(){ location.replace('index.php');},2000);";
}
?>
</script>
</html>