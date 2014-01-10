<?php
include_once('../config.php');
perm_redirect($g_url,0);
?>
<!DOCTYPE HTML>
<html>
<head>
	<title>FACEBoard</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href='bootstrap/css/bootstrap.min.css' rel='stylesheet' type='text/css'>	
	<link href='common/js/rateit/src/rateit.css' rel='stylesheet' type='text/css'>	
	<link href="../common/css/common.css" rel="stylesheet" type="text/css">
	<style type="text/css">
	.table .info
	{
		background: #428BCA;
		color:#FFF;
		border:2px solid #357ebd;
	}
	.table .info:hover
	{
		background: #5CA0DB;
		color:#000;
	}
	</style>
</head>
<body>
	
<div class="container">
	<?php 
		include_once('../common_menus.php');
		top_banner($g_url,1,0); 
	?>	
	<br/>
	<!-- BREADCRUMB -->	

	<br/>
	<div id="sessions_list" class="list-group" data-stage="1" ></div><!-- SESSIONS LIST -->		
	<div id="concepts_list" class="hidden">
		<h3>Concepts</h3>
		<table class="table table-bordered table-hover concept_table" data-session-id="">
			<thead>
				<tr><th>Concept</th><th>Feedback</th><th>Assessment</th><th>State</th></tr>
			</thead>
			<tbody></tbody>
		</table>			 
	</div><!-- CONCEPTS BOX -->		

	<div id="assessment_box" class="hidden clearfix">
		<div class="row">
			<div class="col-sm-10">
			<h1 style="margin-top:0px;">Assessment </h1>
			</div>
			<div class="col-sm-2">				
				<button type="button" class="btn btn-primary"><i class="glyphicon glyphicon-time"></i>
					<span id="assessment_timer">00:00</span></button>							
				<button type="button" class="btn btn-danger" onClick="save_answers(1);">
					<i class="glyphicon glyphicon-off"></i> Quit</button>
			</div>
		</div><!-- ASSESSMENT BANNER -->
		
		<!-- QUESTION BOX -->		
		<div id="ques_box"></div>
	</div><!-- ASSESSMENT -->

	<div id="feedback_session" class="hidden clearfix">
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
	</div><!-- SESSION FEEDBACK -->

</div><!-- MAIN CONTAINER -->


<!-- SESSION CLONE -->
<div id="session_list_clone" class="hidden">
	<a href="#" class="list-group-item session_item at" data-session-code="" data-session-id="">
		<div class="btn-group pull-right session_status">
		  <button type="button" class="btn btn-primary active" data-session-status="0">Ready</button>
		  <button type="button" class="btn btn-primary" data-session-status="1" >Live</button>
		  <button type="button" class="btn btn-primary" data-session-status="2" >Pause</button>
		  <button type="button" class="btn btn-primary" data-session-status="3">Feedback</button>
		  <button type="button" class="btn btn-primary" data-session-status="4">Completed</button>
		</div>
	    <h4 class="list-group-item-heading session_name">Session Name</h4>		     
	    <p class="list-group-item-text session_details">
	    	College Name<br/>
    		Bath Name + year<br/>
    		Scheduled Time
	    </p>
	 </a>
</div>

<!-- CONCEPT CLONE -->
<div id="concept_list_clone" class="hidden">
	<table>
	<tr data-concept-id="">
		<td>Concept Title</td>
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

<!-- FEEDBACK Modal -->
<div class="modal fade" id="feedback_modal" tabindex="-1" role="dialog" aria-labelledby="feedback_modalLabel" aria-hidden="true"
data-concept-id="">
  <div class="modal-dialog">
  	
    <div class="modal-content">
    	<div class="modal-header">        	        	
        	<h1>You just learnt <span class="concept_name"></span> </h1>
      	</div>
      	<div class="modal-status"></div>
    	<div class="modal-body">
       		<h3>Do you like it ? </h3>
	      	<div class="btn-group feedback_like">
	       		<button type="button" class="btn btn-default" data-value="1">  <i class="glyphicon glyphicon-thumbs-up"></i>  Yes</button>        		
	       	</div>

	    	<h3>Did you understand the topic ? </h3>
			<div class="btn-group feedback_understand">
			  <button type="button" class="btn btn-default" data-value="1">Yes</button>
			  <button type="button" class="btn btn-default" data-value="0" >No</button>
			  <button type="button" class="btn btn-default" data-value="2" >Yes, But with doubt</button>		  
			</div><!-- BUTTON GROUP -->
			<div class="alert alert-danger" style="margin-top:10px;"> Please fill whether you understood the topic or not </div>
      	</div><!-- MODAL BODY -->
    	<div class="modal-footer">        
        	<button type="button" class="btn btn-primary" onClick="save_feedback()">Save</button>
      	</div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!--QUESTION BOX CLONE -->
<div id="ques_box_clone" class="hidden">
	<div class="ques_box panel panel-default" data-ques-id="">
		<div class="panel-heading  clearfix">
			<h3 class="ques_no col-sm-1" style="margin-top:2px;"><!-- QUESTION  NO--></h3>				
			<div class="ques col-sm-11"><!-- QUESTION --></div>
		</div>
		<div class="panel-body row clearfix">			
			<div class="col-sm-12">
			<table class="options_table table table-striped table-hover table-compact">
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
      <div class="modal-header">
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
<!-- 
<script type='text/javascript' src='https://www.google.com/jsapi'></script>
    <script type='text/javascript'>
    google.load('visualization', '1', {packages:['corechart','table']});
    </script>
 -->
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
	$('#concepts_list').delegate('.dropdown-menu a','click',function()
		{ 
			var new_state=$(this).find('span').attr('data-state');
			$(this).parents('.dropdown').attr('data-state',new_state); 
			change_concept_state($(this).parents('.dropdown'));
			//save_concept(concept_id, status);
			save_concept($(this).parents('tr').attr('data-concept-id'),new_state);
		});

	//FEEDBACK
	$('#feedback_modal').delegate('.btn-group button','click',function(){
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