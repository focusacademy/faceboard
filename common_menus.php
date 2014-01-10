<?php
//if(isset($_SESSION['redirect_url']))unset($_SESSION['redirect_url']);
$_SESSION['redirect_url']="http://". $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
function top_banner($g_url,$menu=0,$cart=0,$price=0)
{
$c_url = $_SESSION['redirect_url'];
$r_url = "http://www.facenow.in/v2/a/index.php";
//echo substr($c_url, 0, 36);
?>

        <div class="container">
        <div class="navbar-default navbar-static-top" id="nav1">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="facenow_logo" href="<?php if($menu!=2){ echo $g_url; } if(isset($_SESSION['uid']) && $menu!=2){echo ($_SESSION['usr_type'] >= 8)?'dashboard/index.php':'dashboard/users/index.php'; } ?>"></a>
          </div>
          <div class="navbar-collapse collapse pull-right">
          <?php  if($menu==0){?>
            <ul class="nav navbar-nav menu">
              <li><a href="<?php echo $g_url;?>index.php"><i class="glyphicon glyphicon-home"></i>&nbsp;Home</a></li>
              <?php  if($menu==1){?>
              <li><a href="<?php echo $g_url;?>checkout/pricing.php"><i class="glyphicon glyphicon-tasks icon-white"></i>&nbsp;Plans &amp; Pricing</a></li>
               <?php  }?> 
             <?php if(!isset($_SESSION['uid'])){?><li><a href="<?php echo $g_url;?>feature.php"><i class="glyphicon glyphicon-stats icon-white"></i>&nbsp;Features</a></li><?php } ?>
              <?php if($cart==1){?>
			  <li><a href="#cart" class="cart" onclick="return false"><i class="glyphicon glyphicon-info-sign icon-white"></i>&nbsp;Cart <span class="label label-warning no_items_cart"></span></a></li>
			  <?php }  ?>
             
            </ul>
            <?php }?>
            <ul class="nav pull-right navbar-nav">
				<?php if ($c_url != $r_url){?>
                <li><a href="<?php echo $g_url;?>webim/client.php?locale=en" target="_blank" onclick="if(navigator.userAgent.toLowerCase().indexOf('opera') != -1 &amp;&amp; window.event.preventDefault) window.event.preventDefault();this.newWindow = window.open('<?php echo $g_url;?>/webim/client.php?locale=en&amp;url='+escape(document.location.href)+'&amp;referrer='+escape(document.referrer), 'webim', 'toolbar=0,scrollbars=0,location=0,status=1,menubar=0,width=640,height=480,resizable=1');this.newWindow.focus();this.newWindow.opener=window;return false;">
            <i class="glyphicon glyphicon-earphone"></i>&nbsp;Live Support</a></li><?php } ?>
                <li class="dropdown" id="menuLogin">				
					<a class="dropdown-toggle" href="#" data-toggle="dropdown" id="navLogin">
						<i class="glyphicon glyphicon-user icon-white"></i>&nbsp;<?php echo (isset($_SESSION['uid']))?/*"Welcome &nbsp;".*/$_SESSION['usr']:'Login';?>
						<i class="caret"></i>
					</a>					
					<?php if(isset($_SESSION['uid'])){?>		
					<ul class="dropdown-menu">
                    <?php if ($menu != 2) { ?>
                                              <li><a href="<?php echo $g_url . 'index.php'; ?>" ><i class="glyphicon glyphicon-home"></i>&nbsp; Home </a></li>                                                                                                
                                              <li><a href="<?php echo $g_url;
                                  echo ($_SESSION['usr_type'] >= 8) ? 'dashboard/index.php' : 'dashboard/users/index.php'; ?>" ><i class="glyphicon glyphicon-th"></i>&nbsp; Dashboard </a></li>
                                              <?php if ($_SESSION['usr_type'] == 2) { ?><li><a href="<?php echo $g_url; ?>dashboard/test_status.php" ><i class="glyphicon glyphicon-stats"></i>&nbsp; Test Status - Live </a></li>
                                                        <li><a href="<?php echo $g_url; ?>modules/training/info/batches.php" ><i class="glyphicon glyphicon-list"></i>&nbsp; Batches</a></li>
                                                  <?php } ?>
                                              <?php if ($_SESSION['usr_type'] == 0 || $_SESSION['usr_type'] == 1) { ?>
                                                  <li><a href="<?php echo $g_url; ?>modules/analytics/candidates/progress.php" ><i class="glyphicon glyphicon-stats"></i>&nbsp; Progress Analysis </a></li>
                                           <?php } ?>
                                              <li role="presentation" class="divider"></li>					
                                              <li><a href="<?php echo $g_url; ?>users/profile/index.php" ><i class="glyphicon glyphicon-user"></i>&nbsp; Profile</a><li>					
                                             <?php } ?>
                    <li><a href="<?php echo $g_url;?>users/logout.php" ><i class="glyphicon glyphicon-off"></i>&nbsp; Logout</a><li>
					</ul>
					
					<?php } else {?>
	                <div class="dropdown-menu login_box" id="login_content">		
						   <div class="blocker_white hide" style=""></div>
						   <noscript>               
						   <div class="info_1" style="position:absolute;margin-top:0px;">Please enable javascript to login! <br/> Learn how to enable <a href="http://support.google.com/adsense/bin/answer.py?hl=en&answer=12654" class="link_1" target="_new"> click here &raquo;</a></div>
						   </noscript>
                            <form class="form-signin" action="<?php echo $g_url;?>users/users_backend.php">
							<input type="hidden" name="mode" value="1"/>
                              <div class="input-group login_input">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                <input type="text" class="form-control" placeholder="User ID / Email address" autofocus name="email" required />
                              </div> 
                              <div class="input-group login_input">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                                <input type="password" class="form-control" placeholder="Password" name="pswd" required />
                              </div>
                              <div class="login_input clearfix">
                                <button type="submit" class="btn btn-lg btn-danger btn-block" data-loading-text="Signing in...">Sign in</button>
                                <div rel="status" id="status" class="clearfix clear"></div> 
                                <a class="login_link pull-right" href="<?php echo $g_url;?>users/forgot_password.php" >Forgot Password?</a>
                                 </div>
                                           
                                        <span class="clearfix clear pull-left login_with">Login with</span>               
                                 <div class="text-center">
                                     <span> <a href="https://www.facebook.com/dialog/oauth?client_id=<?php echo FB_APP_ID; ?>&redirect_uri=<?php echo urlencode($g_url); ?>users%2Flogin_social.php%3Fsocial%3DFB&scope=email" class="social_rec_icon fb" target="_self"></a></span>
                                     <span> <a href="https://accounts.google.com/o/oauth2/auth?response_type=code&redirect_uri=<?php echo urlencode($g_url); ?>users%2Flogin_social.php%3Fsocial%3DGGL&client_id=<?php echo GOOGLE_CLIENT_ID; ?>&scope=https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile+https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email&access_type=offline&approval_prompt=force" class="social_rec_icon google" target="_self" ></a>                                      
                                     </span>                                         
                                 </div>	
                            </form>							
                    </div><!--  LOGIN DROP DOWN-->
					<?php } ?>					
          		</li>
				
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div><!--TOP BANNER-->

<?php
}// TOP BANNER
// REGISTRATION ACCOUNTS  accounts.php
function menu($g_url)
{
?>
<br/>
<nav class="navbar navbar-default" id="nav1" role="navigation" >
  <!-- Brand and toggle get grouped for better mobile display -->
  <div class="navbar-header">
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>
  </div>

  <!-- Collect the nav links, forms, and other content for toggling -->
  <div class="collapse navbar-collapse navbar-ex1-collapse" <?php /*?>style="width: 80%; margin: 0 auto;"<?php */?>>
    <ul class="nav navbar-nav col-md-12">
        <?php
			$menu=array(array('Home','Your Dashboard home!'),array('Test Management','Browse &amp; analyze tests'),
						array('Training Management','Your training needs'),array('Misc','Everything else'));

			$home=array(0=>'dashboard/users/index.php',1=>'dashboard/users/index.php',2=>'dashboard/users/index.php',
					    8=>'dashboard/index.php',9=>'dashboard/index.php');
			$usr_type=$_SESSION['usr_type'];
			$db1=testDB(2);
			$modules=array();
			$modules_q="SELECT * FROM 
`modules_users`,`modules`
WHERE 
(`modules_users`.`mid`=`modules`.`mid` AND `modules_users`.`uid`='".$_SESSION['uid']."')
OR
`module_rolebased` REGEXP ',".$usr_type.",' 
GROUP BY `modules`.`mid`   ORDER BY `module_menu`";
			//echo $modules_q;
			$modules_res=mysql_query($modules_q,$db1);
			while($modules_r=mysql_fetch_array($modules_res))
			{$modules[$modules_r['module_menu']][]=$modules_r;}
			echo '<li class="col-md-3"style="text-align:center;"><a href="'.$g_url.$home[$usr_type].'">';
				?>
                <span class="menu_icon f_l" style="background-position:-0px"></span>
                <span >Home</span><br/>
                <span style="margin-left:28px; word-wrap:break-word;">Your Dashboard!</span>
                <?php
				echo '</a></li>';
			foreach($menu as $k=>$v)
			{
				if($k != 0){
				echo '<li class="dropdown col-md-3" style="text-align:center;"><a class="dropdown-toggle menu " data-toggle="dropdown" href="'.$g_url.$home[$usr_type].'">';
				?>
                <span class="menu_icon f_l" style="background-position:-<?php echo $k*40;?>px"></span>
                <span > <?php echo $v[0];?></span><br/>
                <span style="margin-left:28px; word-wrap:break-word;"><?php echo $v[1];?></span>
                <?php
				echo ($k==0)?'</a></li>':'</a>';
				if(isset($modules[$k]))
				{
					echo '<ul class="dropdown-menu" style="min-width:100%;">';
					$i = 0;
					foreach($modules[$k] as $k1=>$v1)
					{
					  ?>
					  <li>
                      <a href="<?php echo $g_url.$v1['module_url'];?>" style=" line-height:3;" >
                      <span class="menu_icon f_l" style="background-position:-<?php echo ($v1['m_img']-1)*40;?>px"></span>
					  <?php echo $v1['module_name'];?><br/>
					  </a></li>
                      <?php
                      if(++$i < count($modules[$k])) {?>
                        <li class="divider"></li>
                      <?php }?>
                      
					<?php
					}
					echo '</ul></li>';
				}
				}
			}// EACH MENU
		?>
        </div>        
    </div><!-- DASHBOARD MENU-->
    </div><!-- /.navbar-collapse -->
</nav> 
    <?php
}

function footer($g_url, $reg = 0) {
    ?>
    <div class="clear"></div>
    <div id="footer">
        <?php
        if ($reg != 1) {
            if (!isset($_SESSION['uid'])) {
                ?>
                <div class="footer_signup">
                <div class="container">
                    <h3>Get started with us today and get a free test&nbsp;
                    <a class="btn btn-danger" href="<?php echo $g_url; ?>users/register.php">Sign Up</a></h3>
                </div>
                </div>
            <?php }
        }
        ?>
        <br/>
        <div class="container" style="margin-top:10px;">	
            <div class="row-fluid">
              <div class="col-xs-12 col-sm-12 col-md-12">
                  <div class="col-xs-6 col-sm-3 col-md-3 footer_line" >
                      <ul class="unstyled">
                          <h5>FACENOW</h5>
                          <li><i class="glyphicon glyphicon-home"></i>&nbsp;<a href="<?php echo $g_url; ?>">Home</a></li>
                          <li><i class="glyphicon glyphicon-info-sign"></i>&nbsp;<a href="<?php echo $g_url; ?>info/about_us.php">About us</a></li>
                          <li><i class="glyphicon glyphicon-question-sign"></i>&nbsp;<a href="<?php echo $g_url; ?>info/contact_us.php">Contact Us</a></li>                                            
                          <li><i class="glyphicon glyphicon-tasks"></i>&nbsp;<a href="<?php echo $g_url; ?>checkout/pricing.php">Plans & Pricing</a></li>
                          <li><i class="glyphicon glyphicon-stats"></i>&nbsp;<a href="<?php echo $g_url; ?>feature.php">Features</a></li>                          
                      </ul>
                  </div>
                  <div class="col-xs-6 col-sm-3 col-md-3 footer_line" >
                      <ul class="unstyled">
                          <h5 class="footer_head">Support</h5>
                          <li><span class="glyphicon glyphicon-envelope"></span> <a href="mailto:support@facenow.in">support@facenow.in</a></li>    
                          <br/>					  
                          <li><i class="glyphicon glyphicon-retweet"></i>&nbsp;<a href="<?php echo $g_url; ?>info/policies.php#cancellation">Cancellation & Returns</a></li>
                          <li><i class="glyphicon glyphicon-question-sign"></i>&nbsp;<a href="<?php echo $g_url; ?>info/faq.php">FAQ's</a></li>							
                          <li> <a href="<?php echo $g_url; ?>board"><img src="<?php echo $g_url; ?>board/common/images/faceboard.png" alt="FACEBoard" /></a></li>
                      </ul>
                  </div>
                  <div class="col-xs-12 col-sm-2 col-md-3 footer_line" >
                      <ul class="unstyled">
                          <h5 class="footer_head">Follow Us on</h5>
<!--                          <a href="#g" class="google-roll social-roll"></a>-->
                          <a href="#f" class="facebook-roll social-roll"></a>
                          <a href="#t" class="twitter-roll social-roll"></a>
                      </ul>
                  </div>
                  <div class="col-xs-10 col-sm-4 col-md-3" >
                      <ul class="unstyled">                     
                          <li>
                          	<span class="glyphicon glyphicon-map-marker footer_icon_size"></span>					
                            <b class="footer_font_size">FACENOW</b>
                            <span class="footer_address">
    	                        <p class="footer_address">12,Lakshmi Nagar,<br/>
                                    Avinashi Road-NH4,<br/>
                                    Coimbatore, TamilNadu,<br/>India - 641014.</p>
                            </span>
                          </li>
                      </ul>
                  </div>						
              </div>
          </div>
          <div class="row-fluid">
              <div class="col-xs-12 col-sm-12 col-md-12">
                  <div class="col-xs-6 col-sm-6 col-md-4 unstyled">
                      <a href="<?php echo $g_url; ?>info/terms_of_use.php">Terms of Service</a>&nbsp;|    
                      <a href="<?php echo $g_url; ?>info/privacy.php">Privacy</a>&nbsp;|    
                      <a href="<?php echo $g_url; ?>info/policies.php#security">Security</a>&nbsp;
                  </div>
                  <div class="col-xs-6 col-sm-5 col-md-4 pull-right">
                      <p class="muted pull-right">&copy; 2013 FACE. All rights reserved</p>
                  </div>
              </div>
          </div>
       </div><!-- FOOTER -->
    <?php
}
?>