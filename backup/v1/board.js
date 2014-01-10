
//GLOBALS
var g_session_id="";
var g_session_status="";
var g_concept_id="";
var g_concept_status="";
var pinger_start="0";
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
					$('#sessions_list').empty();
					$.each(session_list,function(k,v)
					{												
						var session_id=v['session_id'];	
						var session_code=v['session_code'];
						if(session_id)
						{
							var  cl=$('#session_list_clone>a').clone();
							cl.attr('data-session-code',session_code).attr('data-session-id',session_id).attr('href','#/session/'+session_id);
							cl.find('.session_name').html(v['session_name']);
							cl.find('.session_details').empty();
							g_session_id=session_id;
							g_session_status=v['status'];
							if(parseInt(json.usr_type)<7 && pinger_start==0)
							{ pinger(); pinger_start=1; }
							{
								cl.find('.session_details').html(v['college_name']+'<br/>'+v['batch_name']+", "+v['batch_year']+'<br/>');
								if(v['status']==1) // LIVE 
								{
									cl.addClass('active');
									fetch_concepts(session_id);
								}
								else if(v['status']==3)// SESSION FEEDBACK
								{
									cl.addClass('active');		
									if(parseInt(json.usr_type)<7)
									session_feedback(session_id);
								}
							}
							//else
							//fetch_concepts(session_id);							

							cl.find('.session_details').append(v['scheduled_time']);
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
					$('#concepts_list tbody').empty();
					var concepts=json.concepts;
					g_concept_id='';
					$.each(concepts,function(k,v)
					{												
						var concept_id=v['concept_id'];		
						if(concept_id)
						{

							var  cl=$('#concept_list_clone tr').clone();
							cl.attr('data-concept-id',concept_id);
							cl.find('td:eq(0)').html(v['concept_name']);
							if(parseInt(json.usr_type)<2)
							cl.find('.dropdown-toggle').removeAttr('data-toggle');
							else if(v['feedback_score'])
							{
								var fs=(v['feedback_score']).split(',');								
								cl.find('td:eq(1)').html('<button class="btn btn-xs btn-info pull-right" onClick="aggregate_feedback('+concept_id+')"><i class="glyphicon glyphicon-refresh"></i></button><span class="">  <i class="glyphicon glyphicon-heart"></i>'+fs[0]+'  </span> | <i class="glyphicon glyphicon-thumbs-up"></i> '+fs[1]+' | <i class="glyphicon glyphicon-thumbs-down"></i> '+fs[2]+' | <i class="glyphicon glyphicon-question-sign"></i> '+fs[3]+'');
							}
							$('#concepts_list tbody').append(cl);

							//CONCEPT STATUS							
							var concept_state=parseInt(v['status']);							
							if(concept_state>=0)
							{
								var concept_tr=$('#concepts_list tr[data-concept-id='+v['concept_id']+']');
								var dropdown=concept_tr.find('.dropdown');
								dropdown.attr('data-state',concept_state);
								change_concept_state(dropdown);
								var usr_type=parseInt(json.usr_type);
								
								// IF USER TYPE IS 1, GUIDE TO CORRESPONDING SCREEN
								if(usr_type<2)
								{				
									//console.log(concept_state);			
									switch(concept_state)
									{
										case 1://SESSION IN PROGRESS
											concept_tr.addClass('info');
											g_concept_id=v['concept_id'];
											g_concept_status=concept_state;											
										break;	
										case 2://FEEDBACK
											g_concept_id=v['concept_id'];
											g_concept_status=concept_state;
											$('#concepts_list').addClass('hidden');											
											show_feedback(v['concept_id'],concept_tr.find('td:eq(0)'));	
											return;
										break;
										case 3://ASSESSMENT
											g_concept_id=v['concept_id'];
											g_concept_status=concept_state;
											$('#concepts_list').addClass('hidden');											
											fetch_assessment(v['concept_id']);
											return;
										break;						
									}//SWITCH CONCEPT
								}
								else if(usr_type>7)
								{
									//if(concept_state<4 && concept_state>0)
									//concept_tr.addClass('info');
									switch(concept_state)
									{									
										case 3://ASSESSMENT >> AGGREGATE FEEDBACK
											if(concept_tr.find('td:eq(1)').html()=='NA')
											aggregate_feedback(v['concept_id']);// AGGREGATE FEEDBACK											
										break;
										case 4://COMPLETED >> FETCH ASSESSMENT SCORE
											if(concept_tr.find('td:eq(2)').html()=='NA')
											aggregate_assessment(concept_tr.find('td:eq(2)'),v['concept_id']);// AGGREGATE SCORES	
										break;
										
									}
								}
							}
						}
						
					});// CONCEPT DETAILS

				}
			}
		});
}//FETCH CONCEPTS

//SAVE CONCEPTS
function save_concept(concept_id, status)
{
	$.ajax(
	{
		url: "backend.php",
		dataType: "json",
		type:"GET",
		data: 
		{
			mode:21,			
			concept_id:concept_id,
			status:status				
		},
		success: function( json ) 
		{				

		}
	});
}

//FEEDBACK
//SHOW FEEDBACK
function show_feedback(concept_id,concept_name)
{	
	$('#feedback_modal .modal-body').show().find('.btn-primary').addClass('btn-default').removeClass('btn-primary');
	$('#feedback_modal .modal-footer').show();
	$('#feedback_modal .modal-status').empty();
	$('#feedback_modal .alert').hide();
	$('#feedback_modal')
	.attr('data-concept-id',concept_id)		
	.modal(
		{
			backdrop:'static',
			keyboard:false
		})
	.find('.concept_name').html(concept_name);
}

//SAVE FEEDBACK
function save_feedback()
{
	var feedback_like=$('#feedback_modal .feedback_like .btn-primary');
	var feedback_understand=$('#feedback_modal .feedback_understand .btn-primary');	

	if(feedback_understand.length>0)
	$.ajax(
		{
			url: "backend.php",
			dataType: "json",
			type:"GET",
			data: 
			{
				mode:30,				
				concept_id:$('#feedback_modal').attr('data-concept-id'),				
				feedback_like:feedback_like.length>0?feedback_like.attr('data-value'):'0',
				feedback_understand:feedback_understand.attr('data-value')
			},
			success: function( json ) 
			{		
				$('#feedback_modal .modal-body,#feedback_modal .modal-footer').hide();
				$('#feedback_modal .modal-status').show().html("<div class='"+json.css+"'>"+json.msg+"</div>");
				setTimeout(function(){ $('#feedback_modal').modal('hide');},2000);
			}
		});
	else
	{
		$('#feedback_modal .alert').show();
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
				$('#concepts_list .concept_table tr[data-concept-id='+concept_id+']').find('td:eq(1)').html('<button class="btn btn-xs btn-info pull-right" onClick="aggregate_feedback('+concept_id+')"><i class="glyphicon glyphicon-refresh"></i></button>  <span class=""><i class="glyphicon glyphicon-heart"></i>'+fs[0]+'</span> | <i class="glyphicon glyphicon-thumbs-up"></i> '+fs[1]+' | <i class="glyphicon glyphicon-thumbs-down"></i> '+fs[2]+' | <i class="glyphicon glyphicon-question-sign"></i> '+fs[3]);
				}
			}

		});
}

//ASSESSMENT
var ques=new Array();
var test_ans=new Array();
var g_time_left=0;
function fetch_assessment(concept_id)
{
	$('#assessment_box').removeClass('hidden');
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
					//PROCESS QUESTIONS
					var i=-1;
					$.each(json.ques,function(k,v)
					{
						if(v['desc_id']!='DESC')
						{
							ques[++i]=v;
							test_ans[v['ques_id']]=[v['ques_id'],'',0]; //ques_id, ans_option, time_taken
						}
					});					

					//PROCESS ANSWERS
					var i=-1;
					var test_ans_array=(json.test.test_ans).split(';');
					$.each(test_ans_array,function(k,v)
					{
						//ques_id[:S1:]selected_ans[:S1:]time_taken[:S2:]
						if(v)						
						{
							var v2=v.split(',');
							if(v2.length==3)
							test_ans[v2[0]]=[v2[0],v2[1],v2[2]];						
						}
					});					
					//console.log(test_ans);
					if(show_question(0))					
					{	
						g_time_left=parseInt(json.time_left);
						if(g_time_left<0)
						save_answers(1);
						else
						{
							$('#assessment_box').removeClass('hidden');
							setInterval(function(){ start_timer();},1000);
							$.getJSON('backend.php',{mode:41,concept_id:g_concept_id},function(){})
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
		$('#ques_box .ques_box').hide();
		var ques_box_clone=$('#ques_box_clone .ques_box').clone();
		ques_box_clone.attr('data-ques-no',ques_no);
		ques_box_clone.find('.ques_no').html(ques_no+1);
		ques_box_clone.find('.ques').html(ques[ques_no]['ques']);			
		for(i=1;i<5;i++)	
		{
			var opt_label='opt_'+i+'_'+ques_no;			
			ques_box_clone.find('input[value='+i+']').attr('id',opt_label).attr('name','ques_opt_'+ques_no);
			ques_box_clone.find('label[for=opt_'+i+']').attr('for',opt_label).html(ques[ques_no]['option_'+i]);						
		}

		var ques_id=ques[ques_no]['ques_id'];		
		if(test_ans[ques_id][1])
		{
			//console.log(test_ans[ques_id][1]);
			ques_box_clone.find('input[value='+test_ans[ques_id][1]+']').attr('checked','checked');					
		}
		$('#ques_box').append(ques_box_clone);
		return 1;
	}	
	else
	{
		$('#ques_box .ques_box').hide();
		$('#ques_box .ques_box[data-ques-no='+ques_no+']').show();
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
	test_ans[ques_id][1]=$('#assessment_box .ques_box:visible input:checked').val();	
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
		save_test_ans.push(v.join(','));
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
				test_ans:save_test_ans.join(';'),
				result:result
			},
			success: function( json ) 
			{
				if(result==1)//GENERATE RESULTS
				{
					$('#assessment_box').hide();
					$('#general_modal').modal().find('.status').html('Generating results.. Please wait !!');
					generate_results(g_concept_id);
				}
				
			}
		});
}

//START start_time
function start_timer()
{
	var time_sec=left_pad(g_time_left%60,2,'0');
	var time_min=left_pad(Math.floor(g_time_left/60),2,'0');
	$('#assessment_timer').html(time_min+':'+time_sec);	
	g_time_left--;
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
function aggregate_assessment(concept_td,concept_id)
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
					concept_td
					.html('<button class="btn btn-sm btn-info" onClick="asssement_analysis('+concept_id+')"><i class="glyphicon glyphicon-list-alt"></i> Analysis </button>')
					.data('scores',json.scores);				
					asssement_analysis(concept_id);
				}
			}

		});
}

//ASSESSMENT ANALYSIS
function asssement_analysis(concept_id)
{

	$('#general_modal').modal().find('.modal-body').empty().append($('<div/>').attr('id','scores_table'))
	.append($('<div/>').attr('id','scores_piechart').addClass('panel').addClass('panel-body'));
	var  concept_tr=$('#concepts_list tr[data-concept-id='+concept_id+'] td:eq(2)');	
	var scores=concept_tr.data('scores');
	var data = new google.visualization.DataTable();	
    data.addColumn('string', 'Name');
	data.addColumn('number', 'Total Score');     
    data.addColumn('number', 'Correct');
    data.addColumn('number', 'Wrong');
    data.addColumn('number', 'Unanswered');    

    $.each(scores,function(k,v){
    	var a_score=(v['assessment_score']).split(',');
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

	$('#feedback_session').removeClass('hidden');
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
				{
					$('#feedback_session').addClass('hidden');
					alert('Thanks for sharing your feedback');
				}
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
				session_id:g_session_id,
				concept_id:g_concept_id
			},
			success: function( json ) 
			{	
				if(json.status==1)
				{
					var ping=json.ping;
					if(g_session_id!=ping['session_id'] || g_session_status!=ping['session_status'])
						fetch_sessions();
					else if (g_concept_id && (g_concept_id!=ping['concept_id'] || g_concept_status!=ping['concept_status']) )
						fetch_concepts(g_session_id);					
				}

				setTimeout(function(){pinger();},1000);
			}
		});
}