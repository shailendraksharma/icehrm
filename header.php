<?php
/*
This file is part of iCE Hrm.

iCE Hrm is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

iCE Hrm is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with iCE Hrm. If not, see <http://www.gnu.org/licenses/>.

------------------------------------------------------------------

Original work Copyright (c) 2012 [Gamonoid Media Pvt. Ltd]  
Developer: Thilina Hasantha (thilina.hasantha[at]gmail.com / facebook.com/thilinah)
 */

include 'includes.inc.php';
if(empty($user)){
	header("Location:".CLIENT_BASE_URL."login.php");
}

if($user->user_level == "Admin"){
	$homeLink = CLIENT_BASE_URL."?g=admin&n=company_structure&m=admin_Admin";
}else{
	$homeLink = CLIENT_BASE_URL."?g=modules&n=dashboard&m=module_Personal_Information";
}

//Check Module Permissions
$modulePermissions = $baseService->loadModulePermissions($_REQUEST['g'], $_REQUEST['n'],$user->user_level);


if(!in_array($user->user_level, $modulePermissions['user'])){
	echo "You are not allowed to access this page";
	exit();
}


$logoFileName = CLIENT_BASE_PATH."data/logo.png";
$logoFileUrl = CLIENT_BASE_URL."data/logo.png";
if(!file_exists($logoFileName)){
	$logoFileUrl = BASE_URL."images/logo.png";	
}

$companyName = $settingsManager->getSetting('Company: Name');

//Load meta info
$meta = json_decode(file_get_contents(MODULE_PATH."/meta.json"),true);

?><!DOCTYPE html>
<html>
    <head>
	    <meta charset="utf-8">
	    <title>IceHRM</title>
	    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	    <meta name="description" content="">
	    <meta name="author" content="">
	
	    <link href="<?=BASE_URL?>themecss/bootstrap.min.css" rel="stylesheet">
	    <link href="<?=BASE_URL?>themecss/font-awesome.min.css" rel="stylesheet">
	    <link href="<?=BASE_URL?>themecss/ionicons.min.css" rel="stylesheet">
	    
	    
	    
		
		<script type="text/javascript" src="<?=BASE_URL?>js/jquery2.0.2.min.js"></script>
		
	    <script src="<?=BASE_URL?>themejs/bootstrap.js"></script>
		<script src="<?=BASE_URL?>js/jquery.placeholder.js"></script>
		
		
		<script src="<?=BASE_URL?>js/bootstrap-datepicker.js"></script>
		<script src="<?=BASE_URL?>js/jquery.timepicker.js"></script>
		<script src="<?=BASE_URL?>js/bootstrap-datetimepicker.js"></script>
		<script src="<?=BASE_URL?>js/fullcalendar.min.js"></script>
		<script src="<?=BASE_URL?>js/select2/select2.min.js"></script>
		
	   

	    <link href="<?=BASE_URL?>themecss/datatables/dataTables.bootstrap.css" rel="stylesheet">
	    <link href="<?=BASE_URL?>css/jquery.timepicker.css" rel="stylesheet">
	    <link href="<?=BASE_URL?>css/datepicker.css" rel="stylesheet">
	    <link href="<?=BASE_URL?>css/bootstrap-datetimepicker.min.css" rel="stylesheet">
	    <link href="<?=BASE_URL?>css/fullcalendar.css" rel="stylesheet">
	    <link href="<?=BASE_URL?>js/select2/select2.css" rel="stylesheet">
	    
	    <link href="<?=BASE_URL?>themecss/AdminLTE.css" rel="stylesheet">
	    
	    <script src="<?=BASE_URL?>themejs/plugins/datatables/jquery.dataTables.js"></script>
		<script src="<?=BASE_URL?>themejs/plugins/datatables/dataTables.bootstrap.js"></script>
		<script src="<?=BASE_URL?>themejs/AdminLTE/app.js"></script>
	    
	    
	    <link href="<?=BASE_URL?>css/style.css?v=<?=$cssVersion?>" rel="stylesheet">
	    
	    
	    <script type="text/javascript" src="<?=BASE_URL?>js/date.js"></script>
		<script type="text/javascript" src="<?=BASE_URL?>js/json2.js"></script>
		<script type="text/javascript" src="<?=BASE_URL?>js/CrockfordInheritance.v0.1.js"></script>
	
		<script type="text/javascript" src="<?=BASE_URL?>api/Base.js?v=<?=$jsVersion?>"></script>
		<script type="text/javascript" src="<?=BASE_URL?>api/AdapterBase.js?v=<?=$jsVersion?>"></script>
		<script type="text/javascript" src="<?=BASE_URL?>api/FormValidation.js?v=<?=$jsVersion?>"></script>
		<script type="text/javascript" src="<?=BASE_URL?>api/Notifications.js?v=<?=$jsVersion?>"></script>
		<script type="text/javascript" src="<?=BASE_URL?>api/TimeUtils.js?v=<?=$jsVersion?>"></script>
		<?php include 'modulejslibs.inc.php';?>
	
	
	    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	    <!--[if lt IE 9]>
	    	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	    	<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
	    <![endif]-->
		<script>
				var baseUrl = '<?=CLIENT_BASE_URL?>service.php';
		</script>
		
	
  	</head>
    <body class="skin-blue">
    	<script>
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
		
		  ga('create', '<?=$baseService->getGAKey()?>', 'icehrm.com');
		  ga('send', 'pageview');
	
	  	</script>
	  	<script type="text/javascript">
	  			
			var uploadId="";
			var uploadAttr="";
			var popupUpload = null;
			
			function showUploadDialog(id,msg,group,user,postUploadId,postUploadAttr,postUploadResultAttr,fileType){
				var ts = Math.round((new Date()).getTime() / 1000);
				uploadId = postUploadId;
				uploadAttr = postUploadAttr;
				uploadResultAttr = postUploadResultAttr;
				var html='<div><iframe src="<?=CLIENT_BASE_URL?>fileupload_page.php?id=_id_&msg=_msg_&file_group=_file_group_&file_type=_file_type_&user=_user_" frameborder="0" scrolling="no" width="300px" height="55px"></iframe></div>';
				var html = html.replace(/_id_/g,id);
				var html = html.replace(/_msg_/g,msg);
				var html = html.replace(/_file_group_/g,group);
				var html = html.replace(/_user_/g,user);
				var html = html.replace(/_file_type_/g,fileType);
	
				modJs.renderModel('upload',"Upload File",html);
				$('#uploadModel').modal('show');
				
			}
		
			function closeUploadDialog(success,error,data){
				var arr = data.split("|");
				var file = arr[0];
				var fileBaseName = arr[1];
				var fileId = arr[2];
				
				if(success == 1){
					//popupUpload.close();
					$('#uploadModel').modal('hide');
					if(uploadResultAttr == "url"){
						if(uploadAttr == "val"){
							$('#'+uploadId).val(file);
						}else if(uploadAttr == "html"){
							$('#'+uploadId).html(file);
						}else{
							$('#'+uploadId).attr(uploadAttr,file);
						}
						
					}else if(uploadResultAttr == "name"){
						if(uploadAttr == "val"){
							$('#'+uploadId).val(fileBaseName);
						}else if(uploadAttr == "html"){
							$('#'+uploadId).html(fileBaseName);
							$('#'+uploadId).attr("val",fileBaseName);
						}else{
							$('#'+uploadId).attr(uploadAttr,fileBaseName);
						}	
						$('#'+uploadId).show();	
						$('#'+uploadId+"_download").show();	
					}else if(uploadResultAttr == "id"){
						if(uploadAttr == "val"){
							$('#'+uploadId).attr(uploadAttr,fileId);
						}else if(uploadAttr == "html"){
							$('#'+uploadId).html(fileBaseName);
							$('#'+uploadId).attr("val",fileId);
						}else{
							$('#'+uploadId).attr(uploadAttr,fileId);
						}
						$('#'+uploadId).show();	
						$('#'+uploadId+"_download").show();	
					}
					
					
				}else{
					//popupUpload.close();
					$('#uploadModel').modal('hide');
				}
				
			}
	
			function download(name, closeCallback, closeCallbackData){
	
				var successCallback = function(data){	

					var link;
					var fileParts;
					var viewableImages = ["png","jpg","gif","bmp","jpge"]; 
					
					if(data['filename'].indexOf("https:") == 0){

						fileParts = data['filename'].split("?");
						fileParts = fileParts[0].split(".");
						
						link = '<a href="'+data['filename']+'" target="_blank">Download File <i class="icon-download-alt"></i> </a>';
						if(jQuery.inArray(fileParts[fileParts.length - 1], viewableImages ) >= 0) {
							link += '<br/><br/><img style="max-width:545px;max-height:350px;" src="'+data['filename']+'"/>';
						}	
					}else{
						fileParts = data['filename'].split(".");
						link = '<a href="'+modJs.getCustomActionUrl("download",{'file':data['filename']})+'" target="_blank">Download File <i class="icon-download-alt"></i> </a>';
						if(jQuery.inArray(fileParts[fileParts.length - 1], viewableImages ) >= 0) {
							link += '<br/><br/><img style="max-width:545px;max-height:350px;" src="'+modJs.getClientDataUrl()+data['filename']+'"/>';
						}
					}
					
					modJs.showMessage("Download File Attachment",link,closeCallback,closeCallbackData);		
				};
				
				var failCallback = function(data){
					modJs.showMessage("Error Downloading File","File not found");	
				};
				
				modJs.sendCustomRequest("file",{'name':name},successCallback,failCallback);
			}
		
			function randomString(length){
				var chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz'.split('');
			    
			    if (! length) {
			        length = Math.floor(Math.random() * chars.length);
			    }
			    
			    var str = '';
			    for (var i = 0; i < length; i++) {
			        str += chars[Math.floor(Math.random() * chars.length)];
			    }
			    return str;	
			}
		</script>
		
        <header id="delegationDiv" class="header">
            <a href="<?=$homeLink?>" class="logo" style="font-family: 'Source Sans Pro', sans-serif;">
             Ice Hrm   
            </a>
            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->
                <a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
                <div class="navbar-right">
                    	<ul class="nav navbar-nav">
                    	
                    	<li class="dropdown messages-menu" id="notifications" style="display: none;"></li>
	                    	
                    	<?php if($user->user_level == "Admin"){?>
                        <li class="user user-menu">
                            <a href="#" onclick="$('#employeeSwitchModal').modal();return false;" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="glyphicon glyphicon-new-window"></i>
                                <span>Switch Employee</span>
                            </a>
                        </li>
                        <?php }?>
                        <?php if(!empty($employeeCurrent)){?>
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="glyphicon glyphicon-user"></i>
                                <span><?=$employeeCurrent->first_name?> <i class="caret"></i></span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- User image -->
                                <li class="user-header bg-light-blue">
                                    <img src="<?=$employeeCurrent->image?>" class="img-circle" alt="User Image" />
                                    <p>
                                        <?=$employeeCurrent->first_name." ".$employeeCurrent->last_name?>
                                        <!--  
                                        <small>Member since Nov. 2012</small>
                                        -->
                                    </p>
                                </li>
                                <!-- Menu Body -->
                                <!--  
                                <li class="user-body">
                                    <div class="col-xs-4 text-center">
                                        <a href="#">Followers</a>
                                    </div>
                                    <div class="col-xs-4 text-center">
                                        <a href="#">Sales</a>
                                    </div>
                                    <div class="col-xs-4 text-center">
                                        <a href="#">Friends</a>
                                    </div>
                                </li>
                                -->
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="pull-left">
                                        <a href="<?=CLIENT_BASE_URL?>?g=modules&n=employees" class="btn btn-default btn-flat">Profile</a>
                                    </div>
                                    <div class="pull-right">
                                        <a href="<?=CLIENT_BASE_URL?>logout.php" class="btn btn-default btn-flat">Sign out</a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <?php }else{?>
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="glyphicon glyphicon-user"></i>
                                <span><?=$user->username?> <i class="caret"></i></span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- User image -->
                                <li class="user-header bg-light-blue">
                                    <img src="<?=BASE_URL?>images/user_male.png" class="img-circle" alt="User Image" />
                                    <p>
                                        <?=$user->username?>
                                    </p>
                                </li>
                                
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                	<?php if(!empty($employeeCurrent) || !empty($employeeSwitched)){?>
                                    <div class="pull-left">
                                        <a href="<?=CLIENT_BASE_URL?>?g=modules&n=employees" class="btn btn-default btn-flat">Profile</a>
                                    </div>
                                    <?php }?>
                                    <div class="pull-right">
                                        <a href="<?=CLIENT_BASE_URL?>logout.php" class="btn btn-default btn-flat">Sign out</a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <?php }?>
                        <?php if($user->user_level == "Admin"){?>
                        	<li class="dropdown messages-menu">
	                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
	                                <i class="glyphicon glyphicon-question-sign"></i>
	                                <span>Help</span>
	                            </a>
	                            <ul class="dropdown-menu">
	                                    <li>
	                                    	<a target="_bloank" href="http://blog.icehrm.com/"><h5>Administrators' Guide</h5></a>
	                                    	<a target="_bloank" href="https://bitbucket.org/thilina/icehrm-opensource/issues?status=new&status=open"><h5>Support/Bug Reporting</h5></a>
	                                    	<a href="#" onclick="modJs.showMessage('About','<p>iCE Hrm - Human Resource Management<br/>Version 7.2<br/>Release Date: 2014/12/04</p>')"><h5>About</h5></a>
	                                    </li>
	                             </ul>
	                        </li>
	                    <?php }?>
                    </ul>
                </div>
            </nav>
        </header>
        <div class="wrapper row-offcanvas row-offcanvas-left">
            <!-- Left side column. contains the logo and sidebar -->
            <aside class="left-side sidebar-offcanvas">                
                <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar">
                    <!-- Sidebar user panel -->
                    <?php if(!empty($employeeCurrent) && !empty($employeeSwitched)){?>
                    <div class="user-panel">
                        <div class="pull-left image">
                            <img src="<?=$employeeCurrent->image?>" class="img-circle" alt="User Image" />
                        </div>
                        <div class="pull-left info">
                            <p><?=$employeeCurrent->first_name." ".$employeeCurrent->last_name?></p>

                            <a href="#"><i class="fa fa-circle text-success"></i> Logged In</a>
                        </div>
                    </div>
                    <div class="user-panel">
                    	<button type="button" onclick="modJs.setAdminEmployee('-1');return false;"><li class="fa fa-times"/></button>
                        <div class="pull-left image">
                            <img src="<?=$employeeSwitched->image?>" class="img-circle" alt="User Image" />
                        </div>
                        <div class="pull-left info">
                            <p><?=$employeeSwitched->first_name." ".$employeeSwitched->last_name?></p>

                            <a href="#"><i class="fa fa-circle text-warning"></i> Updating </a>
                        </div>
                    </div>
                    <?php } else if(!empty($employeeCurrent)){?>
                    <div class="user-panel">
                        <div class="pull-left image">
                            <img src="<?=$employeeCurrent->image?>" class="img-circle" alt="User Image" />
                        </div>
                        <div class="pull-left info">
                            <p><?=$employeeCurrent->first_name." ".$employeeCurrent->last_name?></p>

                            <a href="#"><i class="fa fa-circle text-success"></i> Logged In</a>
                        </div>
                    </div>
                    <?php } else if(!empty($employeeSwitched)){?>
                    <div class="user-panel">
                        <div class="pull-left image">
                            <img src="<?=BASE_URL?>images/user_male.png" class="img-circle" alt="User Image" />
                        </div>
                        <div class="pull-left info">
                            <p><?=$user->username?></p>

                            <a href="#"><i class="fa fa-circle text-success"></i> Logged In</a>
                        </div>
                    </div>
                    <div class="user-panel">
                    	<button type="button" onclick="modJs.setAdminEmployee('-1');return false;"><li class="fa fa-times"/></button>
                        <div class="pull-left image">
                            <img src="<?=$employeeSwitched->image?>" class="img-circle" alt="User Image" />
                        </div>
                        <div class="pull-left info">
                            <p><?=$employeeSwitched->first_name." ".$employeeSwitched->last_name?></p>

                            <a href="#"><i class="fa fa-circle text-warning"></i> Updating</a>
                        </div>
                    </div>
                    <?php } else {?>
                    <div class="user-panel">
                        <div class="pull-left image">
                            <img src="<?=BASE_URL?>images/user_male.png" class="img-circle" alt="User Image" />
                        </div>
                        <div class="pull-left info">
                            <p><?=$user->username?></p>

                            <a href="#"><i class="fa fa-circle text-success"></i> Logged In</a>
                        </div>
                    </div>
                    <?php }?>
                  
                    <ul class="sidebar-menu">
                    	
                        
                        <?php if($user->user_level == 'Admin' || $user->user_level == 'Manager'){?>
			            
			            <?php foreach($adminModules as $menu){?>
			            	<?php if(count($menu['menu']) == 0){continue;}?>
			            	<li  class="treeview" ref="<?="admin_".str_replace(" ", "_", $menu['name'])?>">			       
			            		<a href="#">
                                	<i class="fa fa-th"></i></i> <span><?=$menu['name']?></span>
                                	<i class="fa fa-angle-left pull-right"></i>
                            	</a>
			            	
				            	<ul class="treeview-menu" id="<?="admin_".str_replace(" ", "_", $menu['name'])?>">
				            	<?php foreach ($menu['menu'] as $item){?>
					            		<li>
					            			<a href="<?=CLIENT_BASE_URL?>?g=admin&n=<?=$item['name']?>&m=<?="admin_".str_replace(" ", "_", $menu['name'])?>">
					            			<i class="fa fa-angle-double-right"></i> <?=$item['label']?>
					            			</a>
					            		</li>
				            	<?php }?>
				            	</ul>
			            	</li>
			            <?php }?>
			            
			            <?php }?>
			            
			            <?php if(!empty($employeeCurrent) || !empty($employeeSwitched)){?>
			          
			            <?php foreach($userModules as $menu){?>
			            	
			            	<?php if(count($menu['menu']) == 0){continue;}?>
			            	<li  class="treeview" ref="<?="module_".str_replace(" ", "_", $menu['name'])?>">			       
			            		<a href="#">
                                	<i class="fa fa-th"></i></i> <span><?=$menu['name']?></span>
                                	<i class="fa fa-angle-left pull-right"></i>
                            	</a>
			            	
				            	<ul class="treeview-menu" id="<?="module_".str_replace(" ", "_", $menu['name'])?>">
				            	<?php foreach ($menu['menu'] as $item){?>
				            		<li>
				            			<a href="<?=CLIENT_BASE_URL?>?g=modules&n=<?=$item['name']?>&m=<?="module_".str_replace(" ", "_", $menu['name'])?>">
				            			<i class="fa fa-angle-double-right"></i> <?=$item['label']?>
				            			</a>
				            		</li>
				            	<?php }?>
				            	</ul>
			            	</li>
			            <?php }?>
			            
			            <?php }?>
                        
                        
                        
                    </ul>
                </section>
                <!-- /.sidebar -->
            </aside>

            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side">                
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        <?=$meta['label']?>
                        <small>
                        	<?=$meta['menu']?>&nbsp;&nbsp;
                        	<a href="#" class="helpLink" target="_blank" style="display:none;"><i class="glyphicon glyphicon-question-sign"></i></a>
                        </small>
                        
                    </h1>
                </section>

                <!-- Main content -->
                <section class="content">
                 

                