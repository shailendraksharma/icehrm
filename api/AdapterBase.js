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


function AdapterBase(endPoint) {

}

this.moduleRelativeURL = null;
this.tableData = new Array();
this.sourceData = new Array();
this.filter = null;
this.origFilter = null;
this.orderBy = null;
this.currentElement = null;

AdapterBase.inherits(IceHRMBase);

AdapterBase.method('initAdapter' , function(endPoint,tab,filter,orderBy) {
	this.moduleRelativeURL = baseUrl;
	this.table = endPoint;
	if(tab == undefined || tab == null){
		this.tab = endPoint;
	}else{
		this.tab = tab;
	}
	
	if(filter == undefined || filter == null){
		this.filter = null;
	}else{
		this.filter = filter;
	}
	
	this.origFilter = this.filter;
	
	if(orderBy == undefined || orderBy == null){
		this.orderBy = null;
	}else{
		this.orderBy = orderBy;
	}
	
	this.trackEvent("initAdapter",tab);
	
});

AdapterBase.method('setFilter', function(filter) {
	this.filter = filter;
});

AdapterBase.method('getFilter', function() {
	return this.filter;
});

AdapterBase.method('setOrderBy', function(orderBy) {
	this.orderBy = orderBy;
});

AdapterBase.method('getOrderBy', function() {
	return this.orderBy;
});

AdapterBase.method('add', function(object,callBackData) {
	var that = this;
	$(object).attr('a','add');
	$(object).attr('t',this.table);
	$.post(this.moduleRelativeURL, object, function(data) {
		if(data.status == "SUCCESS"){
			that.addSuccessCallBack(callBackData,data.object);
		}else{
			that.addFailCallBack(callBackData,data.object);
		}
	},"json");
	this.trackEvent("add",this.tab,this.table);
});

AdapterBase.method('addSuccessCallBack', function(callBackData,serverData) {
	this.get(callBackData);
	this.initFieldMasterData();
	this.trackEvent("addSuccess",this.tab,this.table);
});

AdapterBase.method('addFailCallBack', function(callBackData,serverData) {
	this.showMessage("Error saving",serverData);
	this.trackEvent("addFailed",this.tab,this.table);
});

AdapterBase.method('deleteObj', function(id,callBackData) {
	var that = this;
	$.post(this.moduleRelativeURL, {'t':this.table,'a':'delete','id':id}, function(data) {
		if(data.status == "SUCCESS"){
			that.deleteSuccessCallBack(callBackData,data.object);
		}else{
			that.deleteFailCallBack(callBackData,data.object);
		}
	},"json");
	this.trackEvent("delete",this.tab,this.table);
});

AdapterBase.method('deleteSuccessCallBack', function(callBackData,serverData) {
	this.get(callBackData);
	this.clearDeleteParams();
});

AdapterBase.method('deleteFailCallBack', function(callBackData,serverData) {
	this.clearDeleteParams();
	this.showMessage("Error Occurred while Deleting Item",serverData);
});

AdapterBase.method('get', function(callBackData) {
	var that = this;
	
	if(this.getRemoteTable()){
		this.createTableServer(this.getTableName());
		$("#"+this.getTableName()+'Form').hide();
		$("#"+this.getTableName()).show();
		return;
	}
	
	var sourceMappingJson = JSON.stringify(this.getSourceMapping());
	
	var filterJson = "";
	if(this.getFilter() != null){
		filterJson = JSON.stringify(this.getFilter());
	}
	
	var orderBy = "";
	if(this.getOrderBy() != null){
		orderBy = this.getOrderBy();
	}
	
	sourceMappingJson = this.fixJSON(sourceMappingJson);
	filterJson = this.fixJSON(filterJson);
	
	$.post(this.moduleRelativeURL, {'t':this.table,'a':'get','sm':sourceMappingJson,'ft':filterJson,'ob':orderBy}, function(data) {
		if(data.status == "SUCCESS"){
			that.getSuccessCallBack(callBackData,data.object);
		}else{
			that.getFailCallBack(callBackData,data.object);
		}
	},"json");
	
	that.initFieldMasterData();
	
	this.trackEvent("get",this.tab,this.table);
	//var url = this.getDataUrl();
	//console.log(url);
});


AdapterBase.method('getDataUrl', function(columns) {
	var that = this;
	var sourceMappingJson = JSON.stringify(this.getSourceMapping());
	
	var columns = JSON.stringify(columns);
	
	var filterJson = "";
	if(this.getFilter() != null){
		filterJson = JSON.stringify(this.getFilter());
	}
	
	var orderBy = "";
	if(this.getOrderBy() != null){
		orderBy = this.getOrderBy();
	}
	
	var url = this.moduleRelativeURL.replace("service.php","data.php");
	url = url+"?"+"t="+this.table;
	url = url+"&"+"sm="+this.fixJSON(sourceMappingJson);
	url = url+"&"+"cl="+this.fixJSON(columns);
	url = url+"&"+"ft="+this.fixJSON(filterJson);
	url = url+"&"+"ob="+orderBy;
	
	if(this.isSubEmployeeTable()){
		url = url+"&"+"type=sub";
	}
	
	if(this.remoteTableSkipEmployeeRestriction()){
		url = url+"&"+"skip=1";
	}
	
	return url;
});

AdapterBase.method('isSubEmployeeTable', function() {
	return false;
});

AdapterBase.method('remoteTableSkipEmployeeRestriction', function() {
	return false;
});

AdapterBase.method('preProcessTableData', function(row) {
	return row;
});

AdapterBase.method('getSuccessCallBack', function(callBackData,serverData) {
	var data = [];
	var mapping = this.getDataMapping();
	for(var i=0;i<serverData.length;i++){
		var row = [];
		for(var j=0;j<mapping.length;j++){
			row[j] = serverData[i][mapping[j]];
		}
		data.push(this.preProcessTableData(row));
	}
	this.sourceData = serverData;
	if(callBackData['callBack']!= undefined && callBackData['callBack'] != null){
		if(callBackData['callBackData'] == undefined || callBackData['callBackData'] == null){
			callBackData['callBackData'] = new Array();
		}
		callBackData['callBackData'].push(serverData);
		callBackData['callBackData'].push(data);
		this.callFunction(callBackData['callBack'],callBackData['callBackData']);
	}
	
	this.tableData = data;
	
	if(callBackData['noRender']!= undefined && callBackData['noRender'] != null && callBackData['noRender'] == true){
		
	}else{
		this.createTable(this.getTableName());
		$("#"+this.getTableName()+'Form').hide();
		$("#"+this.getTableName()).show();
	}
	
});

AdapterBase.method('getFailCallBack', function(callBackData,serverData) {
	
});


AdapterBase.method('getElement', function(id,callBackData) {
	var that = this;
	var sourceMappingJson = JSON.stringify(this.getSourceMapping());
	sourceMappingJson = this.fixJSON(sourceMappingJson);
	$.post(this.moduleRelativeURL, {'t':this.table,'a':'getElement','id':id,'sm':sourceMappingJson}, function(data) {
		if(data.status == "SUCCESS"){
			that.getElementSuccessCallBack(callBackData,data.object);
		}else{
			that.getElementFailCallBack(callBackData,data.object);
		}
	},"json");
	this.trackEvent("getElement",this.tab,this.table);
});

AdapterBase.method('getElementSuccessCallBack', function(callBackData,serverData) {
	if(callBackData['callBack']!= undefined && callBackData['callBack'] != null){
		if(callBackData['callBackData'] == undefined || callBackData['callBackData'] == null){
			callBackData['callBackData'] = new Array();
		}
		callBackData['callBackData'].push(serverData);
		this.callFunction(callBackData['callBack'],callBackData['callBackData']);
	}
	this.currentElement = serverData;
	if(callBackData['noRender']!= undefined && callBackData['noRender'] != null && callBackData['noRender'] == true){
		
	}else{
		this.renderForm(serverData);
	}
});

AdapterBase.method('getElementFailCallBack', function(callBackData,serverData) {
	
});


AdapterBase.method('getTableData', function() {
	return this.tableData;
});

AdapterBase.method('getTableName', function() {
	return this.tab;
});

AdapterBase.method('getFieldValues', function(fieldMaster,callBackData) {
	var that = this;
	$.post(this.moduleRelativeURL, {'t':fieldMaster[0],'key':fieldMaster[1],'value':fieldMaster[2],'a':'getFieldValues'}, function(data) {
		if(data.status == "SUCCESS"){
			callBackData['callBackData'].push(data.data);
			if(callBackData['callBackSuccess'] != null && callBackData['callBackSuccess'] != undefined){
				callBackData['callBackData'].push(callBackData['callBackSuccess']);
			}
			that.callFunction(callBackData['callBack'],callBackData['callBackData']);
		}
	},"json");
});

AdapterBase.method('setAdminEmployee', function(empId) {
	var that = this;
	$.post(this.moduleRelativeURL, {'a':'setAdminEmp','empid':empId}, function(data) {
		top.location.href = clientUrl;
	},"json");
});

AdapterBase.method('customAction', function(subAction,module,request,callBackData) {
	var that = this;
	request = this.fixJSON(request);
	$.getJSON(this.moduleRelativeURL, {'t':this.table,'a':'ca','sa':subAction,'mod':module,'req':request}, function(data) {
		if(data.status == "SUCCESS"){
			callBackData['callBackData'].push(data.data);
			that.callFunction(callBackData['callBackSuccess'],callBackData['callBackData']);
		}else{
			callBackData['callBackData'].push(data.data);
			that.callFunction(callBackData['callBackFail'],callBackData['callBackData']);
		}
	});
});


AdapterBase.method('sendCustomRequest', function(action,params,successCallback,failCallback) {
	var that = this;
	params['a'] = action;
	$.post(this.moduleRelativeURL, params, function(data) {
		if(data.status == "SUCCESS"){
			successCallback(data['data']);
		}else{
			failCallback(data['data']);
		}
	},"json");
});


AdapterBase.method('getCustomActionUrl', function(action,params) {
	
	params['a'] = action;
	var str = "";
	for(var key in params){
		if(params.hasOwnProperty(key)){
			if(str != ""){
				str += "&";
			}
            str += key + "=" + params[key];
        }
    }
	return this.moduleRelativeURL+"?"+str;
});


AdapterBase.method('getClientDataUrl', function() {
	return this.moduleRelativeURL.replace("service.php","")+"data/";
});

AdapterBase.method('getCustomUrl', function(str) {
	return this.moduleRelativeURL.replace("service.php",str);
});
