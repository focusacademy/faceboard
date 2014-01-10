<?php
include_once('config.php');
$db=testDB(2);
$over=0;
$msg='';

	$s_q="SELECT * FROM `users` WHERE `uid`='".$_SESSION['uid']."' ";
	$s_res=mysql_query($s_q,$db);
	$user=array();
	while($s_r=mysql_fetch_array($s_res))
	{ $user=$s_r;}

	$email=isset($_REQUEST['email'])?$_REQUEST['email']:'';
	$mobile=isset($_REQUEST['mobile'])?$_REQUEST['mobile']:'';
	$usr=isset($_REQUEST['usr'])?$_REQUEST['usr']:'';

			
	if(isset($_REQUEST['clicked']))
	if(isSafe($email,1) && isSafe($mobile,1) && isSafe($usr,1) )
	{			
		$msg='<center><h5>Sorry some error occured! Please try again</h5></center><br/>'; $css='alert alert-danger';
						
		  $up=array();

		  if(isset($_REQUEST['email'])) 		  
		  { $up[]=" `email`='".$_REQUEST['email']."' "; $_SESSION['verified_usr']=1;}
		  if(isset($_REQUEST['mobile']))
		  $up[]=" `mobile`='".$_REQUEST['mobile']."' ";
		  if(isset($_REQUEST['usr']))
		  $up[]=" `usr`='".$_REQUEST['usr']."' ";
		  
		  //UPDATE DETAILS
		  $up_q="UPDATE `users` SET ".implode(',',$up)." WHERE `uid`='".$_SESSION['uid']."' ";
		  mysql_query($up_q,$db);
		  
		  $_SESSION['usr']=$_REQUEST['usr'];
		  // REDIRECTION
		  $next=$_SESSION['usr_type']<8?'index.php':'index.php';
		  $msg='<center><h5>Successfully updated your details. You will be redirected in few seconds else <a href="'.$next.'" class="link_1"> click here </a></h5></center>'; 
		  $css='alert alert-success';
		  $over=1;


	}// IS SAFE

?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>Welcome to FACEBoard</title>
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
<body>

	<div class="container">    	
		<div class="row">
			<div class="col-md-3">
				<div class="board_logo"></div>
			</div>
		</div>    	
		<?php if($msg){
		echo "<div class='".$css."'> ";
		echo $msg;
		echo "</div>";
		 } else {?>

		<div class="panel panel-default row col-md-offset-4 col-md-4">
		<div class="panel-heading  clearfix">
			FACEBOARD Login
		</div>
		<div class="panel-body  clearfix">	
			<div class="alert alert-info"><h5>Please enter your details below ! </h5></div>			
		<?php } if(!$over) {?>
        <form action="" id="verification_form" method="post" class="row">
            <input type="hidden" name="clicked" value="1" />
			<div class="input-group">
                <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                <input type="text" class="form-control" placeholder="Name" value="<?php echo $user['usr']; ?>"  name="usr" required/>
            </div><br/>            
            <div class="input-group">
                <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                <input type="email" class="form-control" placeholder="Email address" value="<?php echo $user['email']; ?>"  name="email" required/>
            </div><br/>
            <div class="input-group ">
                <span class="input-group-addon"><i class="glyphicon glyphicon-earphone"></i></span>
                <input type="number" class="form-control" placeholder="10 digit Mobile No" maxlength=10 value="<?php echo $user['mobile']; ?>" name="mobile" required/>
            </div><br/>
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-md-offset-3">
                <center><button type="submit" class="btn btn-danger btn-lg" value="Enter">Enter</button></center>
            </div>
        </form>
		<?php
		}
		if(isset($err))
		echo "<div class='alert alert-danger'>".$err."</div>";
		?>
	</div><!-- VERIFY BOX 2 -->

</body>
<script type="text/javascript">
<?php 
if($over==1)
{
?>
setTimeout(function(){location.replace("<?php echo $next;?>");},3000);
<?php 
}
?>

</script>
</html>
