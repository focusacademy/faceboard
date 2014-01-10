// JavaScript Document


// GENERATE PASSWORD
	function generate_password(i)
	{ return (i*7919).toString(16).substr(0,4).toUpperCase();}
	
//GET USERS
function get_users(college_id, batch_id, usr_type)
{

    $('#list_table').removeClass().html('<div class="loading" ></div>');

    $.ajax(
            {
                type: "POST",
                url: 'backend.php',
                data: {mode: 1, college_id: college_id, batch_id: batch_id, usr_type: usr_type},
                dataType: 'json',
                success: function(json) {
                    if (json.status == 1)
                        list_users_gchart(json.users);
                    else
                        $('#list_table').removeClass().addClass(json.css).html(json.msg);
                }
            });

}

// LIST USERS
function list_users_gchart(users)
{
    var data = new google.visualization.DataTable();
    data.addColumn('number', 'UID');//0
    data.addColumn('string', 'Name');//1
    data.addColumn('string', 'College ID');	//2
    data.addColumn('string', 'Email');	//3
    data.addColumn('number', 'Mobile');	//4		
    data.addColumn('number', 'Course Code');//5	
    data.addColumn('string', 'Degree');//6
    data.addColumn('string', 'Department');//7
    data.addColumn('string', '');//8	
    data.addColumn('string', 'Default Password');//9
    data = process_row(data, users, [1, 2, 3, 6, 7]);
//	new google.visualization.Table(document.getElementById('users_table')).draw(data, {showRowNumber: true,allowHtml:true});

    var dashboard = new google.visualization.Dashboard(document.getElementById('users_dashboard'));
    var cssClassNames = {
        tableRow: "tableRowGoogle",
        headerCell: "headercellgoogle",
        rowNumberCell: "rowNumberCell",
        tableCell: "rowcellgoogle"
    };
    var users_table = new google.visualization.ChartWrapper({
        'chartType': 'Table',
        'containerId': 'users_table',
        'options': {
            showRowNumber: true,
            allowHtml: true,
            'cssClassNames': cssClassNames,
            'width': '100%'
        }
    });

    var categoryPicker = new google.visualization.ControlWrapper({
        'controlType': 'CategoryFilter',
        'containerId': 'cntrl_dept',
        'options': {
            'filterColumnLabel': 'Department',
            'ui': {
                'label': '',
                'labelStacking': 'horizontal',
                'caption': 'Choose a Dept',
                'selectedValuesLayout': 'belowStacked',
                'allowTyping': false,
                'allowMultiple': false
            }
        }
    });
    dashboard.bind(categoryPicker, users_table);
    var search_box = [['cntrl_uid', 'UID'], ['cntrl_name', 'Name'], ['cntrl_ucid', 'College ID'], ['cntrl_email', 'Email'], ['cntrl_mobile', 'Mobile']];
    var search_control = new Array();
    for (i = 0; i < search_box.length; i++)
    {
        search_control[i] = new google.visualization.ControlWrapper({
            'controlType': 'StringFilter',
            'containerId': search_box[i][0],
            'options': {
                'matchType': 'any',
                'filterColumnLabel': search_box[i][1],
                'ui': {}
            }
        });
        dashboard.bind(search_control[i], users_table);
        dashboard.bind(search_control[i], categoryPicker);
    }
    dashboard.draw(data);

}
// PROCESS ROWS
function process_row(data, row, strs)
{
    $.each(row, function(k, v)
    {
        var data_array = Array();
        var i = 0;
        $.each(v, function(k1, v1) {
            data_array[i] = ($.inArray(i, strs) > -1) ? v1 : ((!isNaN(v1) && v1) ? parseFloat((parseFloat(v1)).toFixed(2)) : 0);
            i++;
        });
        data_array[i] = '<a class="label label-primary edit_users" data-toggle="modal" data-target="#add_users_dialog" >Edit</a><a class="label label-danger remove_users" target="_blank" ><i class="glyphicon glyphicon-remove"></li></a>';
        data_array[i + 1] = generate_password(v[0]);
        data.addRow(data_array);
    });
    return data;
}

//ADD USERS

//ADD USERS DIALOG
function transfer_user(t)
{
    var uid = t.parents('tr').find('td:eq(1)').html();
    if ($('#temp_table tr[uid=' + uid + ']').length == 0)
    {
        var new_tr = t.parents('tr').find('td:gt(0)').map(function() {
            var data = "";
            if ($(this).closest('td').next().length) {
                data = '<td>' + $(this).html() + '</td>'
            } else
                data = "";
            return data;
        }).get().join('');
        new_tr = '<tr uid="' + uid + '">' + new_tr + '</tr>';
        $('#temp_table').append(new_tr);
    }
    add_user_dialog_init($('#temp_table tr[uid=' + uid + ']'));
    //$users_tab.tabs('select',1);
    $('a[href="#users_add"]').tab('show');
}
function add_user_dialog_init(t)
{
    var inp = t.find('td').map(function() {
        return $(this).html();
    }).get();
    var i = 0;
    $('#add_users_dialog input').each(function() {
        $(this).val(inp[i++]);
    });
    add_user_dialog(2, t.index());
}


function add_user_dialog(mode, remove_index) {
    //console.log(mode,remove_index);
    $('.save_user_data').attr('mode', mode);
    $('.save_user_data').attr('remove_index', remove_index);
    if (mode != 2)
        $('#add_users_dialog input').val('');
    //$('#add_users_dialog').removeClass('hide')
//	.dialog({
//			modal: true,
//			width: 500,
//			buttons: {
//				'OK': function() {
//					
//				},
//				"Cancel": function() {
//					$( this ).dialog( "close" );
//				}
//			}
//		});
}
//UPLOAD USERS DIALOG
//function users_upload_dialog()
//{
//    $('#users_upload_dialog')
//            .removeClass('hide')
//            .dialog(
//            {
//                modal: true,
//                width: 700,
//                buttons: {
//                    "Upload": function() {
//                        $('#users_upload_dialog form').submit();
//                        $('#users_add [rel=status]').html('<div class="loading" ></div>');
//                        $(this).dialog("close");
//                    },
//                    "Cancel": function() {
//                        $(this).dialog("close");
//                    }
//                }
//            });
//}
//UPLOAD USERS
function user_upload_complete(res)
{
    $('#users_add [rel=status]').html('');
    var table = $('<div/>').append(res);
    $('#temp_table').append(table.find('tr'));
    $('#users_upload_dialog').modal('hide');
}

//SAVE USERS
function save_users()
{
    $('#users_add [rel=status]').removeClass().html('<div class="loading">Please wait...</div>');
    var users = $('#temp_table tr:gt(0)').map(function() {
        return $(this).find('td:lt(6)').map(function() {
            return "'" + $(this).html() + "'";
        }).get().join(',');
    }).get().join(';');

    var batch_id = $('#users_table').attr('batch_id');
    var college_id = $('#users_table').attr('college_id');
    var usr_type = $('#users_table').attr('usr_type');

    $.ajax({
        type: "POST",
        url: 'backend.php',
        data: {mode: 2, users: users, batch_id: batch_id, college_id: college_id, usr_type: usr_type},
        dataType: 'json',
        success: function(json) {
            if (json.status == 1) {
                $('#temp_table tr:gt(0)').remove();
                get_users(college_id, batch_id, usr_type);
            }
            $('#users_add [rel=status]').removeClass().addClass(json.css).html(json.msg);
        }
    });

}

//PRINT USERS
function print_users(t){
    var header = t.find('tr').map(function() {
        return $(this).find('td.headercellgoogle').map(function() {
            return $(this).html();
        }).get().join('FACE:2');
    }).get().join('FACE:1');
    
    var content = t.find('tr').map(function() {
        return $(this).find('td.rowcellgoogle').map(function() {
            return $(this).html();
        }).get().join('FACE:2');
    }).get().join('FACE:1');
    
    $('#print_form [name=header]').val(header);
    $('#print_form [name=content]').val(content);    
    $('#print_form').submit();
}

// EXPORT EXCEL USERS
function export_xls(t)
{
    var content = t.find('tr').map(function() {
        return $(this).find('td').map(function() {
            return $(this).html();
        }).get().join('FACE:2');
    }).get().join('FACE:1');
    //console.log(content);
    $('#export_window [name=content]').val(content);
    $('#export_window form').submit();
}


/****  AJAX IFRAME METHOD (AIM)**/
AIM = {

    frame : function(c) {

        var n = 'f' + Math.floor(Math.random() * 99999);
        var d = document.createElement('DIV');
        d.innerHTML = '<iframe style="display:none" src="about:blank" id="'+n+'" name="'+n+'" onload="AIM.loaded(\''+n+'\')"></iframe>';
        document.body.appendChild(d);

        var i = document.getElementById(n);
        if (c && typeof(c.onComplete) == 'function') {
            i.onComplete = c.onComplete;
        }

        return n;
    },

    form : function(f, name) {
        f.setAttribute('target', name);
    },

    submit : function(f, c) {
        AIM.form(f, AIM.frame(c));
        if (c && typeof(c.onStart) == 'function') {
            return c.onStart();
        } else {
            return true;
        }
    },

    loaded : function(id) {
        var i = document.getElementById(id);
        if (i.contentDocument) {
            var d = i.contentDocument;
        } else if (i.contentWindow) {
            var d = i.contentWindow.document;
        } else {
            var d = window.frames[id].document;
        }
        if (d.location.href == "about:blank") {
            return;
        }

        if (typeof(i.onComplete) == 'function') {
            i.onComplete(d.body.innerHTML);
        }
    }

}
