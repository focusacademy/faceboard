<?php
include_once('../../config.php');
?>
<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="utf-8">
        <title>Training Mananagement - Users</title>
    <link href='../../bootstrap/css/bootstrap.min.css' rel='stylesheet' type='text/css'>  
    <link href='../../common/js/rateit/src/rateit.css' rel='stylesheet' type='text/css'>  
		
        <style type="text/css">
            .table thead{
                background: #bc2024;
                color:#fff;
            }
            #cntrl_uid input,#cntrl_ucid input
            {
                width:100px;
            }
            ul{
                margin: 0;
            }
            #users_dashboard  a{
                margin:7px;
            }
            #users_add a{
                margin:7px;
            }
        </style>
        <style>
            .google-visualization-table-tr-head-nonstrict {
                max-width:100%;
                background-color:transparent;
                border:1px solid #dddddd;
            }
            .tableRowGoogle {
                border: 1px solid #EEE;
            }
            .tableRowGoogle:hover {
                background: #f5f5f5;
            }

            .headercellgoogle {
                vertical-align:middle  !important;
                text-align: left;  
                background: #bc2024 !important;
                color:#fff !important;                
            }
            .rowcellgoogle {
                /*padding:8px  !important;*/
                line-height:1.428571429;
            }
            .rowNumberCell {
                text-align: center;
            }

            .google-visualization-table-table * {
                margin: 0;
                vertical-align: middle;
                padding: 5px !important;
            }
            span span{
                display: inline-block !important;
                margin-bottom: 5px;
            }
            .google-visualization-table-table {
                border: 2px solid #F0EEEE !important;
            }
            #users_dashboard .remove_users .glyphicon{
                top:-2px;
            }
            .remove_users,.edit_users{
                cursor: pointer;
            }
            .remove_users:hover,.edit_users:hover{
                color:#fff;
            }
        </style>
    </head>

    <body>
        
        <div id="wrap">
            <div class="container">
                <div class="label_1 box_1">
                    <?php
                    $db1 = testDB(2);
                    $college_id = '';
                    $batch_id = '';
                    $usr_type = 0;
                    $college_name = "";
                    $batch_name = "";
                    $batch_year = "";
                    $college = array('college_id' => '', 'college_code' => '', 'college_name' => '', 'college_address' => '', 'college_phone' => '', 'college_email' => '');
                    //echo '<table class="label_2" style="font-size:0.6em;">';
                    if (isset($_REQUEST['usr_type'])) {
                        $usr_type = $_REQUEST['usr_type'];
                        $usr_type_array = array(0 => 'Normal Users', 1 => 'College Students', 2 => 'Placement Officers', 8 => 'FACE Staff', 9 => 'admin');
                        echo '<h5>User Type : ' . $usr_type_array[$_REQUEST['usr_type']] . '</h5>';
                    }
                    if (isset($_REQUEST['batch_id'])) {
                        $batch_id = $_REQUEST['batch_id'];
                        
                    }
                    echo '</table>';
                    ?>       
                </div><!-- BOX-->
                <div class="clear"></div><br/>
                <div id="g_loading" class="loading">Loading..</div>
                <div id="users_tab" class="hide">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#users_dashboard" data-toggle="tab">User List</a></li>
                        <li class=""><a href="#users_add" data-toggle="tab">Add Users</a></li>      
                    </ul>  
                    <div id="myTabContent" class="tab-content" ><br/>
                        <div class="tab-pane fade active in" id="users_dashboard">    
                            <div class="row">
                            <!--<button class="btn btn-danger pull-right" data-toggle="modal" data-target="#print_dialog">Print</button> 
                            <button class="btn btn-danger pull-right" style="margin-right: 10px;" onClick="export_xls($('#users_table table'))">Export xls</button> -->
                                <div class="col-sm-3 col-md-3">
                                    <span><span id="cntrl_uid"></span></span>
                                </div>
                                <div class="col-sm-3 col-md-3">
                                    <span><span id="cntrl_name"></span></span>
                                </div>
                                <div class="col-sm-3 col-md-3">
                                    <span><span id="cntrl_ucid"></span></span>
                                </div>
                                <div class="col-sm-3 col-md-3">
                                    <span><span id="cntrl_email"></span></span>
                                </div>
                                <div class="col-sm-3 col-md-3">
                                    <span><span id="cntrl_mobile"></span></span>
                                </div>
                                <div class="col-sm-3 col-md-3">
                                    <span><span id="cntrl_dept"></span></span>
                                </div>
                            </div>
                            <div id="users_table" class="clearfix"
                                 college_id="<?php echo $college_id; ?>" batch_id="<?php echo $batch_id; ?>" usr_type="<?php echo $usr_type; ?>" >
                                <div class="loading" >Loading...</div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="users_add">
                            <a class="btn btn-danger pull-right" onClick="add_user_dialog(1, '')"  data-toggle="modal" data-target="#add_users_dialog" >Add User</a>         
                            <a class="btn btn-danger pull-right" data-toggle="modal" data-target="#users_upload_dialog" >Upload Users</a>        
                            <a class="btn btn-danger pull-right" onClick="save_users()" >Save Users</a>
                            <div rel="status" ></div>
                            <table id="temp_table" width="100%" class="table table-hover table-condensed" >
                                <thead><tr><th>UID</th><th>Name</th><th>College ID</th><th>Email</th><th>Phone</th><th>Course Code</th><th>Degree</th><th>Department</th><th></th></tr></thead>
                            </table>
                        </div><!-- ADD USERS -->
                    </div>
                </div><!-- USERS  -->

            </div><!-- CONTAINER -->

            <!-- ADD USER DIALOG -->
            <!-- Modal Add/Edit Users -->
            <div class="modal fade" id="add_users_dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" index="">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="myModalLabel">Add/Edit Users</h4>
                        </div>
                        <div class="modal-body">
                            <form class="form-horizontal" role="form">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-4 control-label">Name</label>
                                    <div class="col-sm-8">
                                        <input name="uid" type="hidden" maxlength="255" class="form-control" value=""/>
                                        <input name="usr" type="text" maxlength="255" class="form-control" required/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-4 control-label">College ID</label>
                                    <div class="col-sm-8">
                                        <input name="usr" type="text" class="form-control"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-4 control-label">Email</label>
                                    <div class="col-sm-8">
                                        <input name="email" type="text" class="form-control"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-4 control-label">Mobile No</label>
                                    <div class="col-sm-8">
                                        <input name="mobile" type="text" class="form-control"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-4 control-label">Course code</label>
                                    <div class="col-sm-8">
                                        <input name="course_code" type="text" class="form-control"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-4 control-label">Degree</label>
                                    <div class="col-sm-8">
                                        <input name="degree" type="text" class="form-control"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-4 control-label">Department</label>
                                    <div class="col-sm-8">
                                        <input name="dept" type="text" class="form-control"/>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-danger save_user_data" mode="" remove_index="">Save changes</button>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
            <!--Model Upload Users -->
            <div class="modal fade" id="users_upload_dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" index="">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form class="form-horizontal" role="form" action="uploadxls.php" method="post" enctype="multipart/form-data" onSubmit="return AIM.submit(this, {onComplete: user_upload_complete});">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title" id="myModalLabel">Upload Users</h4>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label class="help-block">Format</label>
                                    <img src="../../common/images/formats/users_upload.png" class="img-rounded img-responsive img-thumbnail" alt="Format for uploading users"/>
                                    <input type="hidden" name="college_id" value="<?php echo $college['college_id']; ?>" />
                                    <input type="hidden" name="batch_id" value="<?php echo $batch_id; ?>" />
                                    <input type="hidden" name="usr_type" value="<?php echo $usr_type; ?>" />
                                </div>
                                <div class="form-group">
                                    <label for="user_file">File input</label>
                                    <input type="file" id="user_file" name="user_file">
                                </div>                               
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-danger save_user_data" mode="" remove_index="">Upload</button>
                                <a class="btn btn-danger" href="../../modules/training/info/ref/courses.php"  target="_blank" >Course Codes</a>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>                            
                            </div>
                        </form>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->


            <!--PRINT FORM--> 
            <!-- Modal -->
            <div class="modal fade" id="print_dialog" tabindex="-1" role="dialog" aria-labelledby="print_dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form class="form-horizontal" id="print_form" role="form"  action="users_pdf.php" method="post" target="_blank">

                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title" id="myModalLabel">Print</h4>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Title</label>
                                    <div class="col-sm-8">
                                        <input type="hidden" name="content" value=""/>
                                        <input type="hidden" name="header" value=""/>
                                        <input type="hidden" name="mode" value="userlist" />
                                        <input type="text" class="form-control" name="title" placeholder="Title for the list" value="<?php echo $college_name . " - " . $batch_name . " ", $batch_year; ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger print_btn">Print</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <input type="hidden" name="title_1" value=""/>
                                <input type="hidden" name="content" value=""/>
                                <input type="hidden" class="form-control" name="no" placeholder="No of candidates"  value="10">
                            </div>
                        </form>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->

            <div id="export_window">
                <form target="_blank" method="post" action="../../modules/analytics/download.php?remove_edit=1">
                    <input type="hidden" name="content"/>  
                </form>
            </div>
        </div>
        
    </body>
    <script type="text/javascript" src="../../bootstrap/js/jquery-2.0.2.min.js"></script>
    <script type="text/javascript" src="../../bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="users.js"></script>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
                                google.load("visualization", "1", {packages: ["corechart", "table", "controls"]});
                                //var $users_tab;
                                $(function() {
                                    $('#g_loading').remove();
                                    $('#users_tab').removeClass('hide');
                                    //$users_tab = $('#users_tab').tabs();

                                    google.setOnLoadCallback(function() {
                                        get_users('<?php echo $college_id; ?>', '<?php echo $batch_id; ?>', '<?php echo $usr_type; ?>');
                                    });
                                    $('#users_table').delegate('.remove_users', 'click', function() {
                                        disable_users($(this));
                                    })
                                    $('#temp_table').delegate('.remove_users', 'click', function() {
                                        $(this).parents('tr').remove();
                                    })
                                    $('#users_table').delegate('.edit_users', 'click', function() {
                                        transfer_user($(this));
                                    });
                                    $('#add_users_dialog').delegate('.save_user_data', 'click', function() {
                                        var remove_index = parseInt($(this).attr('remove_index')) + 1;
                                        var mode = $(this).attr('mode');
                                        var new_tr = $('#add_users_dialog input').map(function() {
                                            return '<td>' + $(this).val() + '</td>';
                                        }).get().join('');
                                        new_tr = '<tr>' + new_tr + '<td><a class="label label-primary edit_users" data-toggle="modal" data-target="#add_users_dialog" >Edit</a><a class="label label-danger remove_users" target="_blank" ><i class="glyphicon glyphicon-remove"></li></a></td></tr>';
                                        if (mode == 2)
                                            $('#temp_table tr:eq(' + remove_index + ')').replaceWith(new_tr);
                                        else
                                            $('#temp_table').append(new_tr);
                                        $('#add_users_dialog').modal('hide');
                                    });
                                    $('#temp_table').delegate('.edit_users', 'click', function() {
                                        add_user_dialog_init($(this).parents('tr'));
                                    });
                                    function disable_users(t)
                                    {
                                        var uid = t.parents('tr').find('td:eq(1)').html();
                                        $.getJSON('backend.php', {mode: 3, uid: uid},
                                        function(json)
                                        {
                                            if (json.status == 1)
                                                t.parents('tr').remove();
                                        });
                                    }
                                });
                                
                                $('body').delegate('.print_btn', 'click', function() {
                                    print_users($('#users_table table'));
                                })

    </script>
</html>