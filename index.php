<?php
include_once('config.php');
if(!isset($_SESSION['uid'])) header('location:login.php');
?>
<!DOCTYPE HTML>
<html>
<head>
	<title>FACEBoard Testing 2</title>
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

	.list-group .list-group-item
	{
		background:none;
		border:none;
		border-bottom:#fff 1px dashed;
		padding: 5px 0 10px 0;
					
	}
	.list-group .list-group-item.active,.list-group .list-group-item.active:hover
	{
		background: none;		
		border-bottom:#fff 1px dashed;		
	}

	.list-group .list-group-item:hover
	{
		background:none;
		color:#fff;
	}		
	#sessions_list h1,.container h1,.container h2,.container h3,.container h4
	{
		color:#fff;
		font-family: 'Shadows Into Light Two', cursive;
	}
	.concept_table .active
	{
		color: #000;
	}
	.ques_no
	{
		font-family: Arial;	
		font-size: 1em;	
		border-radius: 50px;
		margin-right:10px;
		margin-bottom: 5px;
		display: inline-block;
	}
	#scores_table
	{
		color:#000;
	}
	.board_logo
	{
		background: url(common/images/faceboard.png);
		width: 280px;
		height: 50px;
		margin-top: 10px;
	}
	
	.options_table tr:hover
	{
		background:#666;
	}
	.ques_box .ques_ans
	{
		margin-bottom: 10px;
	}
	</style>
</head>
<body>
	
<div class="container board">
	<?php 
		//include_once('../common_menus.php');
		//top_banner($g_url,1,0); 
	?>	
	<div class="row">
		<div class="col-md-3">
			<div class="board_logo"></div>
		</div>
		<div class="pull-right" style="text-align:right;margin-top:1em;">
			<?php
				echo '<h4>				
				Welcome '.$_SESSION['usr'].' 
				<a href="logout.php"><button type="button" class="btn btn-default" data-value="1">  <i class="glyphicon glyphicon-off"></i> </button></a>        		
				</h3>';
			?>
		</div>
	</div><!-- BANNER -->
	
	
	<!-- BREADCRUMB -->	

	<br/>
	<div id="sessions_list" class="list-group"></div><!-- SESSIONS LIST -->	
	<?php if($_SESSION['usr_type']>7) { ?>	
	<button type="button" class="btn btn-default pull-right" onClick="show_sessions()">  <i class="glyphicon glyphicon-plus"></i> More </button><br/>
	<?php } ?>
	<div id="concepts_list" class="hidden" data-stage="0" >		
		<table class="table concept_table" data-session-id="">
			<thead>
				<tr><th></th><th>Concept</th><th>Feedback</th><th>Assessment</th><th>Status</th></tr>
			</thead>
			<tbody></tbody>
		</table>			 
	</div><!-- CONCEPTS BOX -->		

	<div id="concept_video" class="hidden" data-concept-id="" data-stage="1" >
		<video width="800" height="550" controls  autoplay id="concept_video_player" >
		  <source src="demo.mp4 " type="video/mp4" data-type="mp4">		  
		  Sorry, Your browser does not support the video tag.
		</video>
	</div><!-- CONCEPT CLASS -->
	<div id="concept_feedback" class="hidden" data-concept-id="" data-stage="2">
		<div class="content">
			<h3><span class="concept_name"></span> </h3>
			<hr/>
			<h3>Hope you liked it ! </h3>
	      	<div class="btn-group feedback_like push-right">
	       		<button type="button" class="btn btn-default " data-value="1">  <i class="glyphicon glyphicon-thumbs-up"></i>  Like </button>        		
	       	</div>

	    	<h3>Did you understand the topic ? </h3>
			<div class="btn-group feedback_understand">
			  <button type="button" class="btn btn-default" data-value="1">Yes</button>
			  <button type="button" class="btn btn-default" data-value="0" >No</button>
			  <button type="button" class="btn btn-default" data-value="2" >Yes, But with doubt</button>		  
			</div><!-- BUTTON GROUP -->
			<br/><br/>
			<button type="button" class="btn btn-primary" onClick="save_feedback()">Save</button>
			<hr/>
		</div><!-- CONTENT -->		
		<div class="status"></div>
	</div><!-- CONCEPT FEEDBACK -->

	<div id="assessment_box" class="hidden clearfix" data-stage="3">
		<div class="row">
			<div class="col-sm-9">
			<h1 style="margin-top:0px;"> Assessment - <span class="concept_name"></span></h1>
			</div>
			<div class="col-sm-3">				
				<button type="button" class="btn btn-danger" onClick="save_answers(1);">
					<i class="glyphicon glyphicon-off"></i> Save + Quit </button>
				<button type="button" class="btn btn-primary"><i class="glyphicon glyphicon-time"></i>
					<span id="assessment_timer">00:00</span></button>											
			</div>
		</div><!-- ASSESSMENT BANNER -->
		<hr/>
		<!-- QUESTION BOX -->		
		<div id="ques_box"></div>
	</div><!-- ASSESSMENT -->

	<div id="feedback_session" class="hidden clearfix" data-stage="5">
		<div class="content">
		<h2>			
			How would you rate this session <span class="active_session_name">....</span> ?			
		</h2>

		<div class="rateit bigstars" id="session_rating" data-rateit-value="0"
			data-rateit-step="1"
			data-rateit-resetable="false" data-rateit-min="0" data-rateit-max="5" 
			data-rateit-starwidth="32" data-rateit-starheight="32" 
		></div>
		<span class="lead desc"></span>		
		<hr/>
		<h2> Trainer Specific Comments </h2>
		<textarea style="width:100%;" rows=5></textarea><br/><br/>
		<button class="btn btn-primary" onClick="save_session_feedback()">Submit</button><br/><br/>
		</div><!-- CONTENT -->
		<div class="status"></div>
	</div><!-- SESSION FEEDBACK -->
	<?php if($_SESSION['usr_type']>7)
	{?>
	<br/><hr/>
	<a href="#" onClick="initiate_concepts()">
		<button type="button" class="btn btn-default">  <i class="glyphicon glyphicon-list"></i> Initiate Concepts </button>
	</a>
	<a href="users/list/?batch_id=1&usr_type=1" >
		<button type="button" class="btn btn-default">  <i class="glyphicon glyphicon-user"></i> Add Users </button>
	</a>
	
	<?php } ?>

</div><!-- MAIN CONTAINER -->


<!-- SESSION CLONE -->
<div id="session_list_clone" class="hidden">
	<a href="#" class="list-group-item session_item at" data-session-code="" data-session-id="">
		<div class="pull-right label label-success att"><h1>-</h1>	</div>
	    <h1 class="list-group-item-heading session_name">Session Name</h1>		     
	    <p class="list-group-item-text session_details">
	    </p>
	    <div class="btn-group session_status">
			  <button type="button" class="btn btn-primary active" data-session-status="0">Ready</button>
			  <button type="button" class="btn btn-primary" data-session-status="1" >Live</button>
			  <button type="button" class="btn btn-primary" data-session-status="2" >Pause</button>
			  <button type="button" class="btn btn-primary" data-session-status="3">Feedback</button>
			  <button type="button" class="btn btn-primary" data-session-status="4">Completed</button>
		</div>		
	 </a>
</div>

<!-- CONCEPT CLONE -->
<div id="concept_list_clone" class="hidden">
	<table>
	<tr data-concept-id="">
		<td><input type="radio" name="active_concept"/></td>		
		<td class="concept_name">Concept Title</td>
		<td>NA</td><td>NA</td>
		<td>
			<div class="dropdown btn-group" data-state="0">
			  <button type="button" class="btn dropdown-toggle" data-toggle="dropdown" ><span data-state="0" ><i class="glyphicon glyphicon-home"></i>&nbsp;Ready</span><i class="caret"></i></button>			  
			  <ul class="dropdown-menu" role="menu">
				<li><a href="#"><span data-state="0" ><i class="glyphicon glyphicon-home"></i>&nbsp; Ready </span></a></li>    
                <li><a href="#"><span data-state="1" ><i class="glyphicon glyphicon-facetime-video"></i>&nbsp; Session </span></a></li>    
				<li><a href="#"><span data-state="2"><i class="glyphicon glyphicon-comment"></i>&nbsp; Feedback </span></a></li> 
				<li><a href="#"><span data-state="3"><i class="glyphicon glyphicon-list-alt"></i>&nbsp; Assessment </span></a></li>    
				<li><a href="#"><span data-state="4"><i class="glyphicon glyphicon-ok-sign"></i>&nbsp; Completed </span></a></li>  
			  </ul>
			</div>			
  		</li>
		</td>
	</tr>
	</table>
</div>


<!--QUESTION BOX CLONE -->
<div id="ques_box_clone" class="hidden">
	<div class="ques_box " data-ques-id="">
		<div class="panel-heading  clearfix">
			<div class="label label-default ques_no"><!-- QUESTION  NO--></div>				
			<span class="ques"><!-- QUESTION --></span>
		</div>
		<div class="panel-body row clearfix">			
			<div class="col-sm-12">
			<input type="text" name="ans" class="ques_ans" />
			<table class="options_table table  table-compact">
				<tr><td>
					<input type="radio"  id="opt_1" class="col-xs-1" value="1" > 
					<label for="opt_1" style="display:inline-block;" class="col-xs-11"> Option 1</label> 
				</td></tr>
				<tr><td>
					<input type="radio"  id="opt_2" class="col-xs-1" value="2" > 
					<label for="opt_2" style="display:block;" class="col-xs-11"> Option 2</label> 
				</td></tr>
				<tr><td>
					<input type="radio"   id="opt_3" class="col-xs-1" value="3" > 
					<label for="opt_3" style="display:inline-block;" class="col-xs-11"> Option 3</label> 
				</td></tr>
				<tr><td>
					<input type="radio"  id="opt_4" class="col-xs-1" value="4" > 
					<label for="opt_4" style="display:inline-block;" class="col-xs-11"> Option 4</label> 
				</td></tr>
			</table>

			<div class="nav">
				<button type="button" class="btn btn-primary" onClick="nav_question(-1)">
					<i class="glyphicon glyphicon-chevron-left"></i> Prev</button>
				<button type="button" class="ques_clear btn btn-primary" ><i class="glyphicon glyphicon-remove"></i> Clear</button>
				<button type="button" class="btn btn-primary" onClick="nav_question(1)">
					<i class="glyphicon glyphicon-chevron-right"></i> Next</button>
			</div><!-- NAVIGATION -->	
			
			</div><!-- OPTIONS AND NAVIGATION -->			
		</div><!-- PANEL BODY-->
	</div><!-- QUESTION BOX -->
</div><!-- QUESTION BOX CLONE -->

<!-- GENERAL MODAL -->
<div class="modal fade" id="general_modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="color:#000;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">FACEBoard</h4>
      </div>
      <div class="modal-body clearfix">
      	<div class="status"></div>
		<button type="button" class="btn btn-primary pull-right" data-dismiss="modal">Ok</button>
      </div>      
                      
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

</body>
<script type="text/javascript" src="bootstrap/js/jquery-2.0.2.min.js"></script>
<script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="common/js/rateit/src/jquery.rateit.min.js"></script>
<script type="text/javascript" src="board.js"></script>

<script type='text/javascript' src='https://www.google.com/jsapi'></script>
    <script type='text/javascript'>
    google.load('visualization', '1', {packages:['corechart','table']});
    </script>

<script type="text/javascript">
$(function()
{
	fetch_sessions();	
	//FETCH CONCEPTS ON CLICKING SESSION NAME
	$('#sessions_list').delegate('.session_item .session_name','click',function(){ 
		var session_item=$(this).parents('.session_item');
		session_item.addClass('active').siblings().removeClass('active');
		$('#concepts_list table').attr('data-session-id',session_item.attr('data-session-id'));
		fetch_concepts(session_item.attr('data-session-code'),session_item.attr('data-session-id'));
	})
	.delegate('.session_item .att','click',function(){
		var session_item=$(this).parents('.session_item');
		session_attendance(session_item.attr('data-session-id'));		
	});

	//CHANGE SESSION STATE
	$('#sessions_list').delegate('.session_status button','click',function()
		{
			var session_id=$(this).parents('.session_item').attr('data-session-id');
			var session_status=$(this).attr('data-session-status');
			$(this).addClass('active').siblings().removeClass('active');
			save_session(session_id,session_status);
		});

	// CONCEPT CHANGE STATES
	$('#concepts_list').delegate('.dropdown-menu a, input','click',function()
		{ 
			var tr=$(this).parents('tr[data-concept-id]');
			if($(this).parents('.dropdown').length>0) // CHANGING STATE
			{
				var new_state=$(this).find('span').attr('data-state');
				tr.find('.dropdown').attr('data-state',new_state); 
			}
			change_concept_state(tr.find('.dropdown'));
			var  status=tr.find('.dropdown').attr('data-state');
			var active_concept=tr.find('input').is(':checked');
			if(active_concept)
				tr.addClass('active').siblings().removeClass('active');
			//save_concept(concept_id, status);
			save_concept(tr.attr('data-concept-id'),status,active_concept);
		});

	//FEEDBACK
	$('#concept_feedback').delegate('.btn-group button','click',function(){
		$(this).toggleClass('btn-primary').siblings().removeClass('btn-primary');
		$(this).parents('.btn-group').next('.alert').hide();
	});

	//OPTION CLEAR
	$('#assessment_box').delegate('.ques_clear','click',function(){
		$(this).parents('.panel-body').find('input').removeAttr('checked');
	});

	//STAR RATING
	var tooltipvalues = ['No Rating', 'Poor', 'Below Average', 'Average', 'Above Average','Good'];
    $("#session_rating").bind('hover', function (event, value) 
    { 
    	if(typeof tooltipvalues[value] !== 'undefined')
    	$(this).next('.desc').html('&nbsp;'+tooltipvalues[value]); 
    })
    .bind('mouseleave', function (event, value) {  
		var i= parseInt($("#session_rating").rateit('value'));
		if(typeof tooltipvalues[i] !== 'undefined')
    	$(this).next('.desc').html(tooltipvalues[i]);   });

});

function change_concept_state(dropdown)
{	
	var new_state=dropdown.attr('data-state');	
	var new_state_clone=dropdown.find('.dropdown-menu span[data-state='+new_state+']').clone();
	dropdown.find('button span').replaceWith(new_state_clone);
}
</script>
</html>