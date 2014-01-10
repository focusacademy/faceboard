
//GLOBALS
var g_session_id="";
var g_session_status="0";
var g_concept_id="";
var g_concept_status="0";
var pinger_start="0";
var assessment_timer;
var board_version=0;
var video_player;
// FETCH SESSIONS
function fetch_sessions()
{
	$.ajax(
		{
			url: "backend.php",
			dataType: "json",
			type:"GET",
			data: 
			{
				mode:10				
			},
			success: function( json ) 
			{				
				if(json.status==1)
				{
					var session_list=json.session_list;
					var usr_type=parseInt(json.usr_type);					

					$('#sessions_list').empty();
					$.each(session_list,function(k,v)
					{												
						var session_id=v['session_id'];	
						var session_code=v['session_code'];
						board_version=v['version'];
						if(session_id)
						{
							g_session_id=session_id;
							g_session_status=v['status'];
							var  cl=$('#session_list_clone>a').clone();
							cl.attr('data-session-code',session_code).attr('data-session-id',session_id);
							cl.find('.session_name').html('Session : ' + v['session_name']);
							cl.find('.session_details').empty();														

							if(usr_type<7)
							{ 																
								cl.find('.session_status,.att').addClass('hidden');
								cl.addClass('active');
								if(v['status']==3)// SESSION FEEDBACK
								{
									cl.removeClass('hidden').addClass('active');
									if(usr_type<7)
									session_feedback(session_id);								
								}	
								else
								fetch_concepts(session_id);

								setTimeout(function(){pinger();},5000);
							}
							else
							{								
								//cl.find('.session_details').html(v['college_name']+' , '+v['batch_name']+", "+v['batch_year']+'<br/>');
								//cl.find('.session_details').append(v['scheduled_time']);
								cl.addClass('hidden');															
								if(v['status']==1 || v['status']==3) // LIVE or Feedback
								{
								cl.removeClass('hidden').addClass('active');
								fetch_concepts(session_id);
								session_attendance(session_id);
								}								
							}							
							//else
							//fetch_concepts(session_id);
							cl.find('.session_status').find('[data-session-status='+v['status']+']').addClass('active').siblings().removeClass('active');																					
							$('#sessions_list').append(cl);

						}
						
					});
				}
			}
		});
}// FETCH SESSIONS

//SAVE SESSIONS
function save_session(session_id,session_status)
{
	$.ajax(
	{
		url: "backend.php",
		dataType: "json",
		type:"GET",
		data: 
		{
			mode:11,		
			session_id:session_id,
			session_status:session_status				
		},
		success: function( json ) 
		{				

		}
	});
}

//SHOW SESSIONS
function show_sessions()
{
	$('#sessions_list .session_item').toggleClass('hidden');
}

//FETCH ATTENDANCE()
function session_attendance(session_id)
{
	$.ajax(
	{
		url: "backend.php",
		dataType: "json",
		type:"GET",
		data: 
		{
			mode:13,
			session_id:session_id,
			r:Math.random()
		},
		success: function( json ) 
		{		
			if(json.status==1)
			{
				$('#sessions_list [data-session-id='+session_id+'] .att h1').html(json.att);
			}
		}

	}); //AJAX
}
//CONCEPTS
//INITIATE CONCEPTS
function initiate_concepts()
{
	var session_id=$('#sessions_list .active[data-session-id]').attr('data-session-id');
	$.getJSON('backend.php',{mode:22,session_id:session_id},function(json)
	{
		if(json.status==1)
		fetch_concepts(session_id);
	});
}
//FETCH CONCEPTS
function fetch_concepts(session_id)
{
	$.ajax(
		{
			url: "backend.php",
			dataType: "json",
			type:"GET",
			data: 
			{
				mode:20,				
				session_id:session_id				
			},
			success: function( json ) 
			{		
				$('#concepts_list').removeClass('hidden');
				if(json.status==1)
				{
					var usr_type=parseInt(json.usr_type);

					$('#concepts_list tbody').empty();					
					var concepts=json.concepts;
					$("[data-stage]").addClass("hidden");

					//CONCEPTS LIST
					$.each(concepts,function(k,v)
					{												
						var concept_id=v['concept_id'];		
						if(concept_id)
						{
							var concept_state=parseInt(v['status']);
							var  cl=$('#concept_list_clone tr').clone();
							cl.attr('data-concept-id',concept_id).data('video-link',v['concept_video']);
							cl.find('td:eq(1)').html(v['concept_name']);

							if(usr_type<7) // USERS
							{
								cl.find('.dropdown-toggle').removeAttr('data-toggle');
								cl.find('td:eq(2),td:eq(3),td:eq(4)').addClass('hidden');
							}
							
							else // ADMIN
							{
								if(v['feedback_score'])
								{
								var fs=(v['feedback_score']).split(',');								
								cl.find('td:eq(2)').html('<span class="">  <i class="glyphicon glyphicon-thumbs-up"></i>  '+fs[0]+'  </span> | <i class="glyphicon glyphicon-ok"></i>  '+fs[1]+' | <i class="glyphicon glyphicon-remove"></i> '+fs[2]+' | <i class="glyphicon glyphicon-question-sign"></i> '+fs[3]+' | <button class="btn btn-xs btn-info" onClick="aggregate_feedback('+concept_id+')"><i class="glyphicon glyphicon-refresh"></i></button>');
								}
								else
								cl.find('td:eq(2)').html('<button class="btn btn-xs btn-info" onClick="aggregate_feedback('+concept_id+')"><i class="glyphicon glyphicon-refresh"></i></button>');
								//ASSESSMENT SCORE
								if(cl.find('td:eq(3)').html()=='NA')
								cl.find('td:eq(3)').html('<button class="btn btn-sm btn-info" onClick="aggregate_assessment('+concept_id+')"><i class="glyphicon glyphicon-stats"></i> Analysis </button>');								

							}

							var dropdown=cl.find('.dropdown');
							dropdown.attr('data-state',concept_state);
							change_concept_state(dropdown);
							$('#concepts_list tbody').append(cl);
						}
						
					});// EACH CONCEPT DETAILS
					
					g_concept_id=parseInt(json.active_concepts.active_concept_id);
					g_concept_status=parseInt(json.active_concepts.active_concept_status);

					
					var concept_tr=$('#concepts_list tr[data-concept-id='+g_concept_id+']');
					concept_tr.addClass('active').removeClass('hidden').find('input:radio').attr('checked','checked');					
					//CONCEPT STATUS							
					// IF USER TYPE IS 1, GUIDE TO CORRESPONDING SCREEN
					if(usr_type<2) // NORMAL USERS
					{	
						$('#concepts_list tr input').attr('disabled','disabled');
						$('#concepts_list th:eq(2), #concepts_list th:eq(3), #concepts_list th:eq(4),#concepts_list tr:eq(0)').addClass('hidden');
						concept_tr.siblings().addClass('hidden');
						//console.log(concept_state);			
						switch(g_concept_status)
						{							
							case 1://SESSION IN PROGRESS
								if(board_version==0)								
									$("[data-stage=0]").removeClass("hidden");
								else
									show_video(concept_tr.data('video-link'),concept_tr.find('td:eq(1)').html());								
							break;	
							case 2://FEEDBACK								
								show_feedback(g_concept_id,concept_tr.find('td:eq(1)').html());									
							break;
							case 3://ASSESSMENT
								fetch_assessment(g_concept_id,concept_tr.find('td:eq(1)').html());								
							break;
							case 4://COMPLETED	
								$("[data-stage=0]").removeClass("hidden");	
							break;				
						}//SWITCH CONCEPT
						$("[data-stage="+g_concept_status+"]").removeClass("hidden");
						
						clearInterval(assessment_timer);						
					}
					else if(usr_type>7) // ADMIN
					{						
						switch(g_concept_status)
						{			

							case 3://ASSESSMENT >> AGGREGATE FEEDBACK
								if(concept_tr.find('td:eq(2)').html()=='NA')
								aggregate_feedback(g_concept_id);// AGGREGATE FEEDBACK											
							break;
							case 4://COMPLETED >> FETCH ASSESSMENT SCORE
								if(concept_tr.find('td:eq(3)').html()=='NA')
								aggregate_assessment(concept_tr.find('td:eq(3)'),g_concept_id);// AGGREGATE SCORES	
							break;
							
						}
						$("[data-stage=0]").removeClass("hidden");
					}					
				}// STATUS =1
			}// SUCCESS

		});// AJAX
}//FETCH CONCEPTS

//SAVE CONCEPTS
function save_concept(concept_id, status,active_concept)
{
	//console.log(concept_id+':'+status);
	$.ajax(
	{
		url: "backend.php",
		dataType: "json",
		type:"GET",
		data: 
		{
			mode:21,
			session_id:	$('#sessions_list .active[data-session-id]').attr('data-session-id'),
			concept_id:concept_id,					
			status:status,
			active_concept:active_concept		
		},
		success: function( json ) 
		{				

		}
	});
}

//SHOW VIDEO

function show_video(src,concept_name)
{	
	video_player=document.getElementById("concept_video_player");	
	//console.log(video_player);
	$('#concept_video source').attr('src',src);
	video_player.load();
}
//FEEDBACK
//SHOW FEEDBACK
function show_feedback(concept_id,concept_name)
{	
	var concept_name="You just learnt "+ " "+concept_name;
	$('#concept_feedback').find('.concept_name').html(concept_name);
	$('#concept_feedback .content').removeClass('hidden');
	$('#concept_feedback')
	.removeClass('hidden')
	.attr('data-concept-id',concept_id)
	.find('.btn-primary').addClass('btn-default').removeClass('btn-primary');		
	$('#concept_feedback .alert').addClass('hidden');
	
}

//SAVE FEEDBACK
function save_feedback()
{
	var feedback_like=$('#concept_feedback .feedback_like .btn-primary');
	var feedback_understand=$('#concept_feedback .feedback_understand .btn-primary');	

	if(feedback_understand.length>0)
	$.ajax(
		{
			url: "backend.php",
			dataType: "json",
			type:"GET",
			data: 
			{
				mode:30,				
				concept_id:$('#concept_feedback').attr('data-concept-id'),				
				feedback_like:feedback_like.length>0?feedback_like.attr('data-value'):'0',
				feedback_understand:feedback_understand.attr('data-value')
			},
			success: function( json ) 
			{		
				$('#concept_feedback .content').addClass('hidden');
				$('#concept_feedback .status').removeClass('hidden').html("<div class='"+json.css+"'>"+json.msg+"</div>");
				setTimeout(function(){ $('#concept_feedback').addClass('hidden'); $('#concepts_list').removeClass('hidden');},2000);
			}
		});
	else
	{
		$('#concept_feedback .status').removeClass('hidden').html("<div class='alert alert-danger'> Please answer the questions above ! </div>");
	}
}

//AGGREGATE FEEDBACK
function aggregate_feedback(concept_id)
{
	
	$.ajax(
		{
			url: "backend.php",
			dataType: "json",
			type:"GET",
			data: 
			{
				mode:31,				
				concept_id:concept_id				
			},
			success: function( json ) 
			{	
				if(json.status==1)
				{					
				var fs=(json.feedback_score).split(',');	
				if(json.feedback_score)							
				$('#concepts_list .concept_table tr[data-concept-id='+concept_id+']').find('td:eq(2)').html('<span class="">  <i class="glyphicon glyphicon-thumbs-up"></i>  '+fs[0]+'  </span> | <i class="glyphicon glyphicon-ok"></i>  '+fs[1]+' | <i class="glyphicon glyphicon-remove"></i> '+fs[2]+' | <i class="glyphicon glyphicon-question-sign"></i> '+fs[3]+' |  <button class="btn btn-xs btn-info" onClick="aggregate_feedback('+concept_id+')"><i class="glyphicon glyphicon-refresh"></i></button>');
				else
					$('#concepts_list .concept_table tr[data-concept-id='+concept_id+']').find('td:eq(2)').html('<button class="btn btn-xs btn-info" onClick="aggregate_feedback('+concept_id+')"><i class="glyphicon glyphicon-refresh"></i></button> Not yet generated');
				}
			}

		});
}

//ASSESSMENT
var ques=new Array();
var test_ans=new Array();
var g_time_left=0;
function fetch_assessment(concept_id,concept_name)
{
	$('#assessment_box').removeClass('hidden').find('.concept_name').html(concept_name);
	clearInterval(assessment_timer);
	$.ajax(
		{
			url: "backend.php",
			dataType: "json",
			type:"GET",
			data: 
			{
				mode:40,				
				concept_id:concept_id				
			},
			success: function( json ) 
			{					
				if(json.status==1)
				{
					$('#ques_box').empty();
					// CLEAR PREVIOUS QUESTIONS
					ques.length=0;
					test_ans.length=0;
					g_time_left=0;
					//PROCESS QUESTIONS
					var i=-1;
					var ques_ids=(json.ques_ids).split(',');
					var json_ques=json.ques;
					//console.log(ques_ids);
					$.each(ques_ids,function(k,ques_id)
					{
						//console.log(ques_id);
						v=json_ques[ques_id];						
						if(typeof v !== 'undefined' && v['desc_id']!='DESC')
						{
							//console.log('Question');
							ques[++i]=v;
							test_ans[ques_id]=[ques_id,'',0]; //ques_id, ans_option / answer , time_taken
						}
					});					

					//PROCESS ANSWERS
					if(json.test.test_ans)
					{
						var i=-1;
						var test_ans_array=(json.test.test_ans).split('[:S2:]');
						$.each(test_ans_array,function(k,v)
						{
							//ques_id[:S1:]selected_ans[:S1:]time_taken[:S2:]
							if(v)						
							{
								var v2=v.split('[:S1:]');
								if(v2.length==3)
								test_ans[v2[0]]=[v2[0],v2[1],v2[2]];						
							}
						});					
					}
					//console.log(test_ans);
					if(show_question(0))					
					{	
						g_time_left=parseInt(json.time_left);
						if(g_time_left<0)
						save_answers(1);
						else
						{
							$('#assessment_box').removeClass('hidden');
							assessment_timer=setInterval(function(){ start_timer();},1000);
							setTimeout(function()
								{$.getJSON('backend.php',{mode:41,concept_id:g_concept_id},function(){})}, 2000);
						}
					}
					
				}
			}
		});
}

//SHOW QUESTION
function show_question(ques_no)
{
	if(typeof ques[ques_no] !== 'undefined'  && $('#ques_box .ques_box[data-ques-no='+ques_no+']').length<1)
	{		
		$('#ques_box .ques_box').addClass('hidden');
		var ques_box_clone=$('#ques_box_clone .ques_box').clone();
		ques_box_clone.attr('data-ques-no',ques_no);
		ques_box_clone.find('.ques_no').html(ques_no+1);
		ques_box_clone.find('.ques').html(ques[ques_no]['ques']);	
		if(ques[ques_no]['option_1'])
		{
			for(i=1;i<5;i++)	
			{
			var opt_label='opt_'+i+'_'+ques_no;			
			ques_box_clone.find('input[value='+i+']').attr('id',opt_label).attr('name','ques_opt_'+ques_no);
			ques_box_clone.find('label[for=opt_'+i+']').attr('for',opt_label).html(ques[ques_no]['option_'+i]);						
			}
			ques_box_clone.find('.ques_ans').remove();			
		}
		else
		{
			ques_box_clone.find('.options_table').remove();				
		}

		var ques_id=ques[ques_no]['ques_id'];		
		if(test_ans[ques_id][1])
		{
			//console.log(test_ans[ques_id][1]);
			if(ques[ques_no]['option_1'])
				ques_box_clone.find('input[value='+test_ans[ques_id][1]+']').attr('checked','checked');					
			else
				ques_box_clone.find('input').val(test_ans[ques_id][1]);					
		}
		$('#ques_box').append(ques_box_clone);
		return 1;
	}	
	else
	{
		$('#ques_box .ques_box').addClass('hidden');
		$('#ques_box .ques_box[data-ques-no='+ques_no+']').removeClass('hidden');
		return 1;
	}
	return 0;
}

//QUESTION NAVIGATION
function nav_question(dir)
{

	var ques_no=parseInt($('#assessment_box .ques_box:visible').attr('data-ques-no'));
	ques_no=(dir==1)?ques_no+1:ques_no-1;	
	if(ques_no>-1 && ques_no<ques.length)
	{
		record_answer();
		show_question(ques_no);
	}	
	return;
}
//RECORD ANSWERS
function record_answer()
{
	var  ques_no=$('#assessment_box .ques_box:visible').attr('data-ques-no');
	var  ques_id=ques[ques_no]['ques_id'];
	if($('#assessment_box .options_table:visible').length>0)
		test_ans[ques_id][1]=$('#assessment_box .ques_box:visible input:checked').val();	
	else
		test_ans[ques_id][1]=$('#assessment_box .ques_ans').val();	
	return 1;
}

//SAVE ANSWERS
function save_answers(result)
{	
	//console.log(test_ans);
	if(result) record_answer();
	var save_test_ans=new Array();
	$.each(test_ans,function(k,v)
	{ 
		if(typeof(v)!=='undefined' && v.length==3)
		save_test_ans.push(v.join('[:S1:]'));
	});	
	//console.log(save_test_ans);
	$.ajax(
		{
			url: "backend.php",
			dataType: "json",
			type:"GET",
			data: 
			{
				mode:42,				
				concept_id:g_concept_id,
				test_ans:save_test_ans.join('[:S2:]'),
				result:result
			},
			success: function( json ) 
			{
				if(result==1)//GENERATE RESULTS
				{
					$('#assessment_box').addClass('hidden');
					$('#general_modal').modal().find('.status').html('Generating results.. Please wait !!');
					generate_results(g_concept_id);
					$('#concepts_list').removeClass('hidden');
				}
				
			}
		});
}

//START start_time
function start_timer()
{
	var time_sec=left_pad(g_time_left%60,2,'0');
	var time_min=left_pad(Math.floor(g_time_left/60),2,'0');
	if(g_time_left<=0)
	{
		save_answers(1);
		clearInterval(assessment_timer);
	}
	else
	{
		$('#assessment_timer').html(time_min+':'+time_sec);	
		g_time_left--;
	}
}
function left_pad(t,l,r)
{
	t=t+'';
	if(t.length<l)
		return r+t;
	return t;
}

//QUIT EXAM
function generate_results(concept_id)
{
	
	$.ajax(
		{
			url: "backend.php",
			dataType: "json",
			type:"GET",
			data: 
			{
				mode:43,				
				concept_id:g_concept_id				
			},
			success: function( json ) 
			{
				if(json.status==1)					
				$('#general_modal').modal().find('.status').html("<div class='"+json.css+"'>"+json.msg+"</div>");
			}
		});
}

//AGGREGATE EXAM
function aggregate_assessment(concept_id)
{
	
	$.ajax(
		{
			url: "backend.php",
			dataType: "json",
			type:"GET",
			data: 
			{
				mode:44,				
				concept_id:concept_id				
			},
			success: function( json ) 
			{	
				if(json.status==1)
				{											
					asssement_analysis(concept_id,json.scores);
				}
			}

		});
}

//ASSESSMENT ANALYSIS
function asssement_analysis(concept_id,scores)
{

	$('#general_modal').modal().find('.modal-body').empty().append($('<div/>').attr('id','scores_table'))
	.append($('<div/>').attr('id','scores_piechart').addClass('panel').addClass('panel-body'));
	var  concept_tr=$('#concepts_list tr[data-concept-id='+concept_id+'] td:eq(3)');		
	var data = new google.visualization.DataTable();	
    data.addColumn('string', 'Name');
	data.addColumn('number', 'Total Score');     
    data.addColumn('number', 'Correct');
    data.addColumn('number', 'Wrong');
    data.addColumn('number', 'Unanswered');    

    $.each(scores,function(k,v){
    	if(v['assessment_score'])
    	a_score=(v['assessment_score']).split(',');
    	else
    	a_score=[0,0,0];
    	var t_score=parseInt(a_score[0])*3+parseInt(a_score[1])*-1;
    	data.addRow( new Array({v:v['uid'] , f: v['usr']},t_score,parseInt(a_score[0]),parseInt(a_score[1]),parseInt(a_score[2])) );
    });

     var table = new google.visualization.Table(document.getElementById('scores_table'));
     table.draw(data, {showRowNumber:true});

    /** PIE CHART **/
	var grouped_data = google.visualization.data.group(data,[1],
  		[{'column': 1, 'aggregation': google.visualization.data.count, 'type': 'number'}]
	);    
	var view= new google.visualization.DataView(grouped_data);	
	view.setColumns( [{type:'string',label:'Marks',calc:function(dataTable, rowNum){ return dataTable.getValue(rowNum,0)+" Marks "; } },1] );
    
    var chart = new google.visualization.PieChart(document.getElementById('scores_piechart'));
    chart.draw(view,{pieSliceText: 'label',pieHole: 0.3,width:500,height:450});

}

//SESSION FEEDBACK
function session_feedback(session_id)
{
	$('[data-stage]').addClass('hidden');
	$('#feedback_session, #feedback_session .content').removeClass('hidden').find('.status').html('');
	$('#feedback_session #session_rating').rateit('value',0);
	$('#feedback_session textarea').val('');

	//alert(session_id);
}

// SAVE SESSION FEEDBACK
function save_session_feedback()
{
		$.ajax(
		{
			url: "backend.php",
			dataType: "json",
			type:"GET",
			data: 
			{
				mode:12	,
				session_id:$('#sessions_list [data-session-id]').attr('data-session-id'),
				rating_star:$("#session_rating").rateit('value'),
				feedback:$('#feedback_session textarea').val()
			},
			success: function( json ) 
			{				
				if(json.status==1)				
				$('#feedback_session .content').addClass('hidden')
				$('#feedback_session .status').html('<div class="alert alert-success">Thanks for sharing your feedback</div>');				
			}
		});
}

//PINGER
function pinger()
{
	$.ajax(
		{
			url: "backend.php",
			dataType: "json",
			type:"GET",
			data: 
			{
				mode:90,
				session_id:g_session_id	,
				r:Math.random()			
			},
			success: function( json ) 
			{	
				if(json.status==1)
				{
					var ping=json.ping;
					if(g_session_status!=ping['session_status'])
					{ reset_stage(); 	fetch_sessions(); }
					else if ( ping['session_status']==1 && ( g_concept_id!=ping['active_concept_id'] || g_concept_status!=ping['active_concept_status']  ) )					
					{ reset_stage(); fetch_concepts(g_session_id);	}

				}
				pinger_start=1; 

				setTimeout(function(){pinger();},5000);
			}
		});
}

function reset_stage()
{
	video_player=document.getElementById("concept_video_player");
	video_player.pause();
}