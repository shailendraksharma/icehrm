/**
 * Author: Thilina Hasantha
 */

function UserAdapter(endPoint) {
	this.initAdapter(endPoint);
}

UserAdapter.inherits(AdapterBase);


UserAdapter.method('getDataMapping', function() {
	return [
	        "id",
	        "username",
	        "email",
	        "employee",
	        "user_level"
	];
});

UserAdapter.method('getHeaders', function() {
	return [
			{ "sTitle": "ID" },
			{ "sTitle": "User Name" },
			{ "sTitle": "Authentication Email" },
			{ "sTitle": "Employee"},
			{ "sTitle": "User Level"}
	];
});

UserAdapter.method('getFormFields', function() {
	return [
	        [ "id", {"label":"ID","type":"hidden","validation":""}],
	        [ "username", {"label":"User Name","type":"text","validation":"username"}],
	        [ "email", {"label":"Email","type":"text","validation":"email"}],
	        [ "employee", {"label":"Employee","type":"select","allow-null":true,"remote-source":["Employee","id","first_name+last_name"]}],
	        [ "user_level", {"label":"User Level","type":"select","source":[["Admin","Admin"],["Manager","Manager"],["Employee","Employee"]]}]
	];
});

UserAdapter.method('changePassword', function() {
	$('#adminUsersModel').modal('show');
	$('#adminUsersChangePwd #newpwd').val('');
	$('#adminUsersChangePwd #conpwd').val('');
});

UserAdapter.method('changePasswordConfirm', function() {
	$('#adminUsersChangePwd_error').hide();
	
	var passwordValidation =  function (str) {  
		var val = /^[a-zA-Z0-9]\w{6,}$/;  
		return str != null && val.test(str);  
	};
	
	var password = $('#adminUsersChangePwd #newpwd').val();
	
	if(!passwordValidation(password)){
		$('#adminUsersChangePwd_error').html("Password may contain only letters, numbers and should be longer than 6 characters");
		$('#adminUsersChangePwd_error').show();
		return;
	}
	
	var conPassword = $('#adminUsersChangePwd #conpwd').val();
	
	if(conPassword != password){
		$('#adminUsersChangePwd_error').html("Passwords don't match");
		$('#adminUsersChangePwd_error').show();
		return;
	}
	
	var req = {"id":this.currentId,"pwd":conPassword};
	var reqJson = JSON.stringify(req);
	
	var callBackData = [];
	callBackData['callBackData'] = [];
	callBackData['callBackSuccess'] = 'changePasswordSuccessCallBack';
	callBackData['callBackFail'] = 'changePasswordFailCallBack';
	
	this.customAction('changePassword','admin=users',reqJson,callBackData);
	
});

UserAdapter.method('closeChangePassword', function() {
	$('#adminUsersModel').modal('hide');
});

UserAdapter.method('changePasswordSuccessCallBack', function(callBackData,serverData) {
	this.closeChangePassword();
	this.showMessage("Password Change","Password changed successfully");
});

UserAdapter.method('changePasswordFailCallBack', function(callBackData,serverData) {
	this.closeChangePassword();
	this.showMessage("Error",callBackData);
});

UserAdapter.method('getHelpLink', function () {
	return 'http://blog.icehrm.com/?page_id=132';
});



