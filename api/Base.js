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

function IceHRMBase() {
	this.deleteParams = {};
	this.createRemoteTable = false;
	this.instanceId = "None";
	this.ga = [];
	this.showEdit = true;
	this.showDelete = true;
	this.showSave = true;
	this.showCancel = true;
	this.showFormOnPopup = false;
	this.filtersAlreadySet = false;
	this.currentFilterString = "";
}

this.fieldTemplates = null;
this.templates = null;
this.customTemplates = null;
this.emailTemplates = null;
this.fieldMasterData = null;
this.sourceMapping = null;
this.currentId = null;
this.user = null;7
this.currentEmployee = null;
this.permissions = {};



this.baseUrl = null;


IceHRMBase.method('init' , function(appName, currentView, dataUrl, permissions) {
	
});

IceHRMBase.method('setNoJSONRequests' , function(val) {
	this.noJSONRequests = val;
});


IceHRMBase.method('setPermissions' , function(permissions) {
	this.permissions = permissions;
});

IceHRMBase.method('checkPermission' , function(permission) {
	if(this.permissions[permission] == undefined || this.permissions[permission] == null || this.permissions[permission] == "Yes"){
		return "Yes";
	}else{
		return this.permissions[permission];
	}
});

IceHRMBase.method('setBaseUrl' , function(url) {
	this.baseUrl = url;
});

IceHRMBase.method('setUser' , function(user) {
	this.user = user;
});

IceHRMBase.method('getUser' , function() {
	return this.user;
});

IceHRMBase.method('setInstanceId' , function(id) {
	this.instanceId = id;
});

IceHRMBase.method('setGoogleAnalytics' , function(ga) {
	this.ga = ga;
});

IceHRMBase.method('showActionButtons' , function() {
	return true;
});

IceHRMBase.method('trackEvent' , function(action, label, value) {
	try{
		if(label == undefined || label == null){
			this.ga.push(['_trackEvent', this.instanceId, action]);
		}else if(value == undefined || value == null){
			this.ga.push(['_trackEvent', this.instanceId, action, label]);
		}else{
			this.ga.push(['_trackEvent', this.instanceId, action, label, value]);
		}
	}catch(e){
		
	}
	
	
});

IceHRMBase.method('setCurrentEmployee' , function(currentEmployee) {
	this.currentEmployee = currentEmployee;
});

IceHRMBase.method('getCurrentEmployee' , function() {
	return this.currentEmployee;
});


IceHRMBase.method('initFieldMasterData' , function(callback) {
	var values;
	if(this.showAddNew == undefined || this.showAddNew == null){
		this.showAddNew = true;
	}
	this.fieldMasterData = {};
	this.sourceMapping = {};
	var fields = this.getFormFields();
	var filterFields = this.getFilters();
	
	if(filterFields != null){
		for(var j=0;j<filterFields.length;j++){
			values = this.getMetaFieldValues(filterFields[j][0],fields);
			if(values == null || (values['type']!= "select" && values['type']!= "select2")){
				fields.push(filterFields[j]);
			}
		}
	}
	
	for(var i=0;i<fields.length;i++){
		var field = fields[i];
		if(field[1]['remote-source'] != undefined && field[1]['remote-source'] != null){
			var key = field[1]['remote-source'][0]+"_"+field[1]['remote-source'][1]+"_"+field[1]['remote-source'][2];
			
			this.sourceMapping[field[0]] = field[1]['remote-source'];
			
			var callBackData = {};
			callBackData['callBack'] = 'initFieldMasterDataResponse';
			callBackData['callBackData'] = [key];
			if(callback != null && callback != undefined){
				callBackData['callBackSuccess'] = callback;
			}
			this.getFieldValues(field[1]['remote-source'],callBackData);
		}
	}
});

IceHRMBase.method('setShowFormOnPopup' , function(val) {
	this.showFormOnPopup = val;
});

IceHRMBase.method('setRemoteTable' , function(val) {
	this.createRemoteTable = val;
});

IceHRMBase.method('getRemoteTable' , function() {
	return this.createRemoteTable;
});


IceHRMBase.method('initFieldMasterDataResponse' , function(key,data, callback) {
	this.fieldMasterData[key] = data;
	if(callback != undefined && callback != null){
		callback();
	}
	
});

IceHRMBase.method('getMetaFieldValues' , function(key, fields) {
	for(var i=0;i<fields.length;i++){
		if(key == fields[i][0]){
			return fields[i][1];
		}
	}
	return null;
});

IceHRMBase.method('getSourceMapping' , function() {
	return this.sourceMapping ;
});

IceHRMBase.method('setTesting' , function(testing) {
	this.testing = testing;
});

IceHRMBase.method('consoleLog' , function(message) {
	if(this.testing) {
		console.log(message);
	}
});

IceHRMBase.method('setClientMessages', function(msgList) {
	this.msgList = msgList;
});

IceHRMBase.method('setTemplates', function(templates) {
	this.templates = templates;
});


IceHRMBase.method('getWSProperty', function(array, key) {
	if(array.hasOwnProperty(key)) {
		return array[key];
	}
	return null;
});


IceHRMBase.method('getClientMessage', function(key) {
	return this.getWSProperty(this.msgList,key);
});



IceHRMBase.method('getTemplate', function(key) {
	return this.getWSProperty(this.templates, key);
});

IceHRMBase.method('setGoogleAnalytics', function (gaq) {
	this.gaq = gaq;
});


IceHRMBase.method('showView', function(view) {
	if(this.currentView != null) {
		this.previousView = this.currentView;
		$("#" + this.currentView).hide();
	}
	$('#' + view).show();
	this.currentView = view;
	this.moveToTop();
});

IceHRMBase.method('showPreviousView', function() {
	this.showView(this.previousView);	
});


IceHRMBase.method('moveToTop', function () {
	
});


IceHRMBase.method('callFunction', function (callback, cbParams) {
	if($.isFunction(callback)) {
		try{
			callback.apply(document, cbParams);
		} catch(e) {
		}
	} else {
		f = this[callback];
		if($.isFunction(f)) {
			try{
				f.apply(this, cbParams);
			} catch(e) {
			}
		} 
	}
	return ;
});

IceHRMBase.method('getTableTopButtonHtml', function() {
	var html = "";
	if(this.getShowAddNew()){
		html = '<button onclick="modJs.renderForm();return false;" class="btn btn-small btn-primary">Add New <i class="fa fa-plus"></i></button>';
	}
	
	if(this.getFilters() != null){
		if(html != ""){
			html += "&nbsp;&nbsp;";
		}
		html+='<button onclick="modJs.showFilters();return false;" class="btn btn-small btn-primary">Fillter <i class="fa fa-filter"></i></button>';
		html += "&nbsp;&nbsp;";
		if(this.filtersAlreadySet){
			html+='<button id="__id___resetFilters" onclick="modJs.resetFilters();return false;" class="btn btn-small btn-default">__filterString__ <i class="fa fa-times"></i></button>';
		}else{
			html+='<button id="__id___resetFilters" onclick="modJs.resetFilters();return false;" class="btn btn-small btn-default" style="display:none;">__filterString__ <i class="fa fa-times"></i></button>';
		}
		
	}
	
	html = html.replace(/__id__/g, this.getTableName());
	
	if(this.currentFilterString != "" && this.currentFilterString != null){
		html = html.replace(/__filterString__/g, this.currentFilterString);
	}else{
		html = html.replace(/__filterString__/g, 'Reset Filters');
	}
	
	if(html != ""){
		html = '<div class="row"><div class="col-xs-12">'+html+'</div></div>';
	}
	
	return html;
});

IceHRMBase.method('createTable', function(elementId) {
	
	if(this.getRemoteTable()){
		this.createTableServer(elementId);
		return;
	}
	
	
	var headers = this.getHeaders();
	var data = this.getTableData();
	
	if(this.showActionButtons()){
		headers.push({ "sTitle": "", "sClass": "center" });
	}
	
	
	if(this.showActionButtons()){
		for(var i=0;i<data.length;i++){
			data[i].push(this.getActionButtonsHtml(data[i][0],data[i]));
		}
	}
	
	var html = "";
	html = this.getTableTopButtonHtml()+'<div class="box-body table-responsive"><table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped" id="grid"></table></div>';
	/*
	if(this.getShowAddNew()){
		html = this.getTableTopButtonHtml()+'<div class="box-body table-responsive"><table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped" id="grid"></table></div>';
	}else{
		html = '<div class="box-body table-responsive"><table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped" id="grid"></table></div>';
	}
	*/
	//Find current page
	var activePage = $('#'+elementId +" .dataTables_paginate .active a").html();
	var start = 0;
	if(activePage != undefined && activePage != null){
		start = parseInt(activePage, 10)*15 - 15;
	}

	$('#'+elementId).html(html);
	
	var dataTableParams = {
			"oLanguage": {
				"sLengthMenu": "_MENU_ records per page"
			},
			"aaData": data,
			"aoColumns": headers,
			"bSort": false,
			"iDisplayLength": 15,
			"iDisplayStart": start
		};
	
	/*
	 "fnInitComplete": function(oSettings, json) {
				if(activePage != undefined && activePage != null){
					$('#'+elementId+" .dataTables_paginate a:contains('"+activePage+"')").click();
				}
			}
	 */
	
	var customTableParams = this.getCustomTableParams();
	
	$.extend(dataTableParams, customTableParams);
	
	$('#'+elementId+' #grid').dataTable( dataTableParams );
	
	$(".dataTables_paginate ul").addClass("pagination");
	$(".dataTables_length").hide();
	$(".dataTables_filter input").addClass("form-control");
	$(".dataTables_filter input").attr("placeholder","Search");
	$(".dataTables_filter label").contents().filter(function(){
	    return (this.nodeType == 3);
	}).remove();
	$('.tableActionButton').tooltip();
});

IceHRMBase.method('createTableServer', function(elementId) {
	var that = this;
	var headers = this.getHeaders();
	
	headers.push({ "sTitle": "", "sClass": "center" });
	
	var html = "";
	html = this.getTableTopButtonHtml()+'<div class="box-body table-responsive"><table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped" id="grid"></table></div>';
	/*
	if(this.getShowAddNew()){
		html = this.getTableTopButtonHtml()+'<div class="box-body table-responsive"><table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped" id="grid"></table></div>';
	}else{
		html = '<div class="box-body table-responsive"><table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped" id="grid"></table></div>';
	}
	*/
	
	//Find current page
	var activePage = $('#'+elementId +" .dataTables_paginate .active a").html();
	var start = 0;
	if(activePage != undefined && activePage != null){
		start = parseInt(activePage, 10)*15 - 15;
	}
	
	
	$('#'+elementId).html(html);
	
	var dataTableParams = {
			"oLanguage": {
				"sLengthMenu": "_MENU_ records per page"
			},
			"bProcessing": true,
		    "bServerSide": true,
		    "sAjaxSource": that.getDataUrl(that.getDataMapping()),
			"aoColumns": headers,
			"bSort": false,
			"parent":that,
			"iDisplayLength": 15,
			"iDisplayStart": start
		};
	
	if(this.showActionButtons()){
		dataTableParams["aoColumnDefs"] = [ 
		                        			{
		                         				"fnRender": that.getActionButtons,
		                         				"aTargets": [that.getDataMapping().length]
		                         			}
		                    			];
	}
	
	var customTableParams = this.getCustomTableParams();
	
	$.extend(dataTableParams, customTableParams);
	
	$('#'+elementId+' #grid').dataTable( dataTableParams );
	
	$(".dataTables_paginate ul").addClass("pagination");
	$(".dataTables_length").hide();
	$(".dataTables_filter input").addClass("form-control");
	$(".dataTables_filter input").attr("placeholder","Search");
	$(".dataTables_filter label").contents().filter(function(){
	    return (this.nodeType == 3);
	}).remove();
	
	$('.tableActionButton').tooltip();
});

IceHRMBase.method('getHeaders', function() {
	
});

IceHRMBase.method('getTableData', function() {
	
});

IceHRMBase.method('getFormFields', function() {
	
});

IceHRMBase.method('getFilters', function() {
	return null;
});

IceHRMBase.method('edit', function(id) {
	this.currentId = id;
	this.getElement(id,[]);
});

IceHRMBase.method('renderModel', function(id,header,body) {
	$('#'+id+'ModelBody').html("");
	
	if(body == undefined || body == null){
		body = "";
	}
	
	$('#'+id+'ModelLabel').html(header);
	$('#'+id+'ModelBody').html(body);
});

IceHRMBase.method('renderModelFromDom', function(id,header,element) {
	$('#'+id+'ModelBody').html("");
	
	if(element == undefined || element == null){
		element = $("<div></div>");
	}
	
	$('#'+id+'ModelLabel').html(header);
	$('#'+id+'ModelBody').html("");
	$('#'+id+'ModelBody').append(element);
});

IceHRMBase.method('deleteRow', function(id) {
	this.deleteParams['id'] = id;
	this.renderModel('delete',"Confirm Deletion","Are you sure you want to delete this item ?");
	$('#deleteModel').modal('show');
	
});

IceHRMBase.method('showMessage', function(title,message,closeCallback,closeCallbackData, isPlain) {
	var that = this;
	var modelId = "";
	if(isPlain){
		modelId = "#plainMessageModel";
		this.renderModel('plainMessage',title,message);
	}else{
		modelId = "#messageModel";
		this.renderModel('message',title,message);
	}
	
	$(modelId).unbind('hide');
	if(closeCallback != null && closeCallback != undefined){
		$(modelId).on('hidden.bs.modal',function(){
			closeCallback.apply(that,closeCallbackData);
			$(modelId).unbind('hidden.bs.modal');
		});
	}
	$(modelId).modal({
		  backdrop: 'static'
	});
});

IceHRMBase.method('showDomElement', function(title,element,closeCallback,closeCallbackData, isPlain) {
	var that = this;
	var modelId = "";
	if(isPlain){
		modelId = "#plainMessageModel";
		this.renderModelFromDom('plainMessage',title,element);
	}else{
		modelId = "#messageModel";
		this.renderModelFromDom('message',title,element);
	}
	
	$(modelId).unbind('hide');
	if(closeCallback != null && closeCallback != undefined){
		$(modelId).on('hidden.bs.modal',function(){
			closeCallback.apply(that,closeCallbackData);
			$(modelId).unbind('hidden.bs.modal');
		});
	}
	$(modelId).modal({
		  backdrop: 'static'
	});
});

IceHRMBase.method('confirmDelete', function() {
	if(this.deleteParams['id'] != undefined || this.deleteParams['id'] != null){
		this.deleteObj(this.deleteParams['id'],[]);
	}
	$('#deleteModel').modal('hide');
});

IceHRMBase.method('cancelDelete', function() {
	$('#deleteModel').modal('hide');
	this.deleteParams['id'] = null;
});

IceHRMBase.method('closeMessage', function() {
	$('#messageModel').modal('hide');
});

IceHRMBase.method('closePlainMessage', function() {
	$('#plainMessageModel').modal('hide');
});

IceHRMBase.method('save', function() {
	var validator = new FormValidation(this.getTableName()+"_submit",true,{'ShowPopup':false,"LabelErrorClass":"error"});
	if(validator.checkValues()){
		var params = validator.getFormParameters();
		
		var msg = this.doCustomValidation(params);
		if(msg == null){
			var id = $('#'+this.getTableName()+"_submit #id").val();
			if(id != null && id != undefined && id != ""){
				$(params).attr('id',id);
			}
			this.add(params,[]);
		}else{
			$("#"+this.getTableName()+'Form .label').html(msg);
			$("#"+this.getTableName()+'Form .label').show();
		}
		
	}
});

IceHRMBase.method('filterQuery', function() {
	var validator = new FormValidation(this.getTableName()+"_filter",true,{'ShowPopup':false,"LabelErrorClass":"error"});
	if(validator.checkValues()){
		var params = validator.getFormParameters();
		if(this.doCustomFilterValidation(params)){
			
			//remove null params
			for (var prop in params) {
				if(params.hasOwnProperty(prop)){
					if(params[prop] == "NULL"){
						delete(params[prop]);
					}
				}
			}
			
			this.setFilter(params);
			this.filtersAlreadySet = true;
			$("#"+this.getTableName()+"_resetFilters").show();
			this.currentFilterString = this.getFilterString(params);
			
			this.get([]);
			this.closePlainMessage();
		}
		
	}
});

IceHRMBase.method('getFilterString', function(filters) {

	var str = '';
	var rmf, source, values;
	
	var filterFields = this.getFilters();
	
	for (var prop in filters) {
		if(filters.hasOwnProperty(prop)){
			
			if(str != ''){
				str += " | ";
			}
			
			values = this.getMetaFieldValues(prop,filterFields);
			
			str += values['label']+" = ";
			if((values['type'] == 'select' || values['type'] == 'select2')){
				
				if(values['remote-source']!= undefined && values['remote-source']!= null){
					rmf = values['remote-source'];
					if(filters[prop] == "NULL"){
						if(values['null-label'] != undefined && values['null-label'] != null){
							str += values['null-label'];
						}else{
							str += "Not Selected";
						}
					}else{
						str += this.fieldMasterData[rmf[0]+"_"+rmf[1]+"_"+rmf[2]][filters[prop]];
					}
					
				}else{
					source = values['source'][0];
					if(filters[prop] == "NULL"){
						if(values['null-label'] != undefined && values['null-label'] != null){
							str += values['null-label'];
						}else{
							str += "Not Selected";
						}
					}else{
						for(var i=0; i<source.length; i++){
							if(filters[prop] == source[i]){
								str += values['source'][1][i];
								break;
							}
						}
					}
					
					
				}
			}else{
				str += filters[prop];
			}
		}
	}
	
	return str;
});

IceHRMBase.method('doCustomFilterValidation', function(params) {
	return true;
});

IceHRMBase.method('resetFilters', function() {
	this.filter = this.origFilter;
	this.filtersAlreadySet = false;
	$("#"+this.getTableName()+"_resetFilters").hide();
	this.currentFilterString = "";
	this.get([]);
});

IceHRMBase.method('doCustomValidation', function(params) {
	return null;
});


IceHRMBase.method('showFilters', function(object) {
	var formHtml = this.templates['filterTemplate'];
	var html = "";
	var fields = this.getFilters();
	
	for(var i=0;i<fields.length;i++){
		var metaField = this.getMetaFieldForRendering(fields[i][0]);
		if(metaField == "" || metaField == undefined){
			html += this.renderFormField(fields[i]);
		}else{
			var metaVal = object[metaField];
			if(metaVal != '' && metaVal != null && metaVal != undefined && metaVal.trim() != ''){
				html += this.renderFormField(JSON.parse(metaVal));
			}else{
				html += this.renderFormField(fields[i]);
			}
		}
		
	}
	formHtml = formHtml.replace(/_id_/g,this.getTableName()+"_filter");
	formHtml = formHtml.replace(/_fields_/g,html);
	
	var $tempDomObj;
	var randomFormId = this.generateRandom(14);
	$tempDomObj = $('<div class="reviewBlock popupForm" data-content="Form"></div>');
	$tempDomObj.attr('id',randomFormId);
	
	$tempDomObj.html(formHtml);
	
	
	$tempDomObj.find('.datefield').datepicker({'viewMode':2});
	$tempDomObj.find('.timefield').datetimepicker({
      language: 'en',
      pickDate: false
    });
	$tempDomObj.find('.datetimefield').datetimepicker({
      language: 'en'
    });
	
	$tempDomObj.find('.select2Field').select2();

	//var tHtml = $tempDomObj.wrap('<div>').parent().html();
	this.showDomElement("Edit",$tempDomObj,null,null,true);
	$(".filterBtn").off();
	$(".filterBtn").on('click',function(e) {
		e.preventDefault();
		e.stopPropagation();
		try{
			modJs.filterQuery();
			
		}catch(e){
		};
		return false;
	});
	
	if(this.filter != undefined && this.filter != null){
		this.fillForm(this.filter,"#"+this.getTableName()+"_filter", this.getFilters());
	}
	
});

IceHRMBase.method('preRenderForm', function(object) {

});

IceHRMBase.method('renderForm', function(object) {
	
	this.preRenderForm(object);
	
	var formHtml = this.templates['formTemplate'];
	var html = "";
	var fields = this.getFormFields();
	
	for(var i=0;i<fields.length;i++){
		var metaField = this.getMetaFieldForRendering(fields[i][0]);
		if(metaField == "" || metaField == undefined){
			html += this.renderFormField(fields[i]);
		}else{
			var metaVal = object[metaField];
			if(metaVal != '' && metaVal != null && metaVal != undefined && metaVal.trim() != ''){
				html += this.renderFormField(JSON.parse(metaVal));
			}else{
				html += this.renderFormField(fields[i]);
			}
		}
		
	}
	formHtml = formHtml.replace(/_id_/g,this.getTableName()+"_submit");
	formHtml = formHtml.replace(/_fields_/g,html);
	
	/*
	$("#"+this.getTableName()+'Form').html(formHtml);
	$("#"+this.getTableName()+'Form').show();
	$("#"+this.getTableName()).hide();
	
	$("#"+this.getTableName()+'Form .datefield').datepicker({'viewMode':2});
	$("#"+this.getTableName()+'Form .timefield').datetimepicker({
      language: 'en',
      pickDate: false
    });
	$("#"+this.getTableName()+'Form .datetimefield').datetimepicker({
      language: 'en'
    });
	
	$("#"+this.getTableName()+'Form .select2Field').select2();
	
	if(this.showSave == false){
		$("#"+this.getTableName()+'Form').find('.saveBtn').remove();
	}
	
	if(object != undefined && object != null){
		this.fillForm(object);
	}
	
	*/
	
	var $tempDomObj;
	var randomFormId = this.generateRandom(14);
	if(!this.showFormOnPopup){
		$tempDomObj = $("#"+this.getTableName()+'Form');
	}else{
		$tempDomObj = $('<div class="reviewBlock popupForm" data-content="Form"></div>');
		$tempDomObj.attr('id',randomFormId);
		
	}
	
	$tempDomObj.html(formHtml);
	
	
	$tempDomObj.find('.datefield').datepicker({'viewMode':2});
	$tempDomObj.find('.timefield').datetimepicker({
      language: 'en',
      pickDate: false
    });
	$tempDomObj.find('.datetimefield').datetimepicker({
      language: 'en'
    });
	
	$tempDomObj.find('.select2Field').select2();
	
	if(this.showSave == false){
		$tempDomObj.find('.saveBtn').remove();
	}
	
	if(this.showCancel== false){
		$tempDomObj.find('.cancelBtn').remove();
	}
	
	if(!this.showFormOnPopup){
		$("#"+this.getTableName()+'Form').show();
		$("#"+this.getTableName()).hide();
		if(object != undefined && object != null){
			this.fillForm(object);
		}
		
	}else{
		var tHtml = $tempDomObj.wrap('<div>').parent().html();
		this.showMessage("Edit",tHtml,null,null,true);
		if(object != undefined && object != null){
			this.fillForm(object,"#"+randomFormId);
		}
	}
	
	this.postRenderForm(object,$tempDomObj);
	
	
	
});


IceHRMBase.method('postRenderForm', function(object, $tempDomObj) {

});

IceHRMBase.method('fillForm', function(object, formId, fields) {
	var placeHolderVal;
	if(fields == null || fields == undefined){
		fields = this.getFormFields();
	}
	
	if(formId == null || formId == undefined || formId == ""){
		formId = "#"+this.getTableName()+'Form';
	}
	
	
	for(var i=0;i<fields.length;i++) {
		if(fields[i][1].type == 'date'){
			if(object[fields[i][0]] != '0000-00-00'){
				$(formId + ' #'+fields[i][0]+"_date").datepicker('setValue', object[fields[i][0]]);
			}
		}else if(fields[i][1].type == 'datetime' || fields[i][1].type == 'time'){
			if(object[fields[i][0]] != '0000-00-00 00:00:00'){
				var tempDate = object[fields[i][0]];
				var arr = tempDate.split(" ");
				var dateArr = arr[0].split("-");
				var timeArr = arr[1].split(":");
				$(formId + ' #'+fields[i][0]+"_datetime").data('datetimepicker').setLocalDate(new Date(dateArr[0], parseInt(dateArr[1])-1, dateArr[2], timeArr[0], timeArr[1], timeArr[2]));
			}
		}else if(fields[i][1].type == 'label'){
			$(formId + ' #'+fields[i][0]).html(object[fields[i][0]]);
		}else if(fields[i][1].type == 'placeholder'){
			
			if(fields[i][1]['remote-source'] != undefined && fields[i][1]['remote-source'] != null){
				var key = fields[i][1]['remote-source'][0]+"_"+fields[i][1]['remote-source'][1]+"_"+fields[i][1]['remote-source'][2];
				placeHolderVal = this.fieldMasterData[key][object[fields[i][0]]];
			}else{
				placeHolderVal = object[fields[i][0]];
			}
			
			if(placeHolderVal == undefined || placeHolderVal == null){
				placeHolderVal = "";
			}else{
				placeHolderVal = placeHolderVal.replace(/(?:\r\n|\r|\n)/g, '<br />');
			}
			
			
			
			
			$(formId + ' #'+fields[i][0]).html(placeHolderVal);
		}else if(fields[i][1].type == 'fileupload'){
			if(object[fields[i][0]] != null && object[fields[i][0]] != undefined && object[fields[i][0]] != ""){
				$(formId + ' #'+fields[i][0]).html(object[fields[i][0]]);
				$(formId + ' #'+fields[i][0]).attr("val",object[fields[i][0]]);
				$(formId + ' #'+fields[i][0]).show();
				$(formId + ' #'+fields[i][0]+"_download").show();
				
			}
			if(fields[i][1].readonly == true){
				$(formId + ' #'+fields[i][0]+"_upload").remove();
			}
		}else if(fields[i][1].type == 'select'){
			if(object[fields[i][0]] == undefined || object[fields[i][0]] == null || object[fields[i][0]] == ""){
				object[fields[i][0]] = "NULL";
			}
			$(formId + ' #'+fields[i][0]).val(object[fields[i][0]]);
			
		}else if(fields[i][1].type == 'select2'){
			if(object[fields[i][0]] == undefined || object[fields[i][0]] == null || object[fields[i][0]] == ""){
				object[fields[i][0]] = "NULL";
			}
			$(formId + ' #'+fields[i][0]).select2('val',object[fields[i][0]]);
			
		}else{
			$(formId + ' #'+fields[i][0]).val(object[fields[i][0]]);
		}
	    
	}
});

IceHRMBase.method('cancel', function() {
	$("#"+this.getTableName()+'Form').hide();
	$("#"+this.getTableName()).show();
});

IceHRMBase.method('renderFormField', function(field) {
	var userId = 0;
	if(this.fieldTemplates[field[1].type] == undefined || this.fieldTemplates[field[1].type] == null){
		return "";
	}
	var t = this.fieldTemplates[field[1].type];
	if(field[1].validation != "none" && field[1].type != "placeholder"){
		field[1].label = field[1].label + '<font class="redFont">*</font>';
	}
	if(field[1].type == 'text' || field[1].type == 'textarea' || field[1].type == 'hidden' || field[1].type == 'label' || field[1].type == 'placeholder'){
		t = t.replace(/_id_/g,field[0]);
		t = t.replace(/_label_/g,field[1].label);
		
	}else if(field[1].type == 'select' || field[1].type == 'select2'){
		t = t.replace(/_id_/g,field[0]);
		t = t.replace(/_label_/g,field[1].label);
		if(field[1]['source'] != undefined && field[1]['source'] != null ){
			t = t.replace('_options_',this.renderFormSelectOptions(field[1].source));
		}else if(field[1]['remote-source'] != undefined && field[1]['remote-source'] != null ){
			var key = field[1]['remote-source'][0]+"_"+field[1]['remote-source'][1]+"_"+field[1]['remote-source'][2];
			t = t.replace('_options_',this.renderFormSelectOptionsRemote(this.fieldMasterData[key],field));
		}
		
	}else if(field[1].type == 'date'){
		t = t.replace(/_id_/g,field[0]);
		t = t.replace(/_label_/g,field[1].label);
	
	}else if(field[1].type == 'datetime'){
		t = t.replace(/_id_/g,field[0]);
		t = t.replace(/_label_/g,field[1].label);
	
	}else if(field[1].type == 'time'){
		t = t.replace(/_id_/g,field[0]);
		t = t.replace(/_label_/g,field[1].label);
		
	}else if(field[1].type == 'fileupload'){
		t = t.replace(/_id_/g,field[0]);
		t = t.replace(/_label_/g,field[1].label);
		var ce = this.getCurrentEmployee();
		if(ce != null && ce != undefined){
			userId = ce.id;
		}else{
			userId = this.getUser().id * -1;
		}
		t = t.replace(/_userId_/g,userId);
		t = t.replace(/_group_/g,this.tab);
		
		/*
		if(object != null && object != undefined && object[field[0]] != null && object[field[0]] != undefined && object[field[0]] != ""){
			t = t.replace(/_id___rand_/g,field[0]);
		}
		*/
		t = t.replace(/_rand_/g,this.generateRandom(14));
		
	}
	
	if(field[1].validation != undefined && field[1].validation != null && field[1].validation != ""){
		t = t.replace(/_validation_/g,'validation="'+field[1].validation+'"');
	}else{
		t = t.replace(/_validation_/g,'');
	}
	return t;
});

IceHRMBase.method('renderFormSelectOptions', function(options) {
	var html = "";
	for(var i=0;i<options.length;i++){
		var t = '<option value="_id_">_val_</option>';
		t = t.replace('_id_', options[i][0]);
		t = t.replace('_val_', options[i][1]);
		html += t;
	}
	return html;
	
});

IceHRMBase.method('renderFormSelectOptionsRemote', function(options,field) {
	var html = "";
	if(field[1]['allow-null'] == true){
		if(field[1]['null-label'] != undefined && field[1]['null-label'] != null){
			html += '<option value="NULL">'+field[1]['null-label']+'</option>';
		}else{
			html += '<option value="NULL">Select</option>';
		}
		
	}
	for (var prop in options) {
		var t = '<option value="_id_">_val_</option>';
		t = t.replace('_id_', prop);
		t = t.replace('_val_', options[prop]);
		html += t;
	}
	return html;
	
});

IceHRMBase.method('setTemplates', function(templates) {
	this.templates = templates;
});

IceHRMBase.method('setCustomTemplates', function(templates) {
	this.customTemplates = templates;
});

IceHRMBase.method('setEmailTemplates', function(templates) {
	this.emailTemplates = templates;
});

IceHRMBase.method('getCustomTemplate', function(file) {
	return this.customTemplates[file];
});

IceHRMBase.method('setFieldTemplates', function(templates) {
	this.fieldTemplates = templates;
});

IceHRMBase.method('getDataMapping', function() {

});

IceHRMBase.method('getMetaFieldForRendering', function(fieldName) {
	return "";
});

IceHRMBase.method('clearDeleteParams', function() {
	this.deleteParams = {};
});

IceHRMBase.method('getShowAddNew', function() {
	return this.showAddNew;
});

IceHRMBase.method('setShowAddNew', function(showAddNew) {
	this.showAddNew = showAddNew;
});

IceHRMBase.method('setShowDelete', function(val) {
	this.showDelete = val;
});

IceHRMBase.method('setShowEdit', function(val) {
	this.showEdit = val;
});

IceHRMBase.method('setShowSave', function(val) {
	this.showSave = val;
});

IceHRMBase.method('setShowCancel', function(val) {
	this.showCancel = val;
});

IceHRMBase.method('getCustomTableParams', function() {
	return {};
});

IceHRMBase.method('getActionButtons', function(obj) {
	return modJs.getActionButtonsHtml(obj.aData[0],obj.aData);
});

IceHRMBase.method('getActionButtonsHtml', function(id,data) {	
	var editButton = '<img class="tableActionButton" src="_BASE_images/edit.png" style="cursor:pointer;" rel="tooltip" title="Edit" onclick="modJs.edit(_id_);return false;"></img>';
	var deleteButton = '<img class="tableActionButton" src="_BASE_images/delete.png" style="margin-left:15px;cursor:pointer;" rel="tooltip" title="Delete" onclick="modJs.deleteRow(_id_);return false;"></img>';
	var html = '<div style="width:80px;">_edit__delete_</div>';
	
	if(this.showDelete){
		html = html.replace('_delete_',deleteButton);
	}else{
		html = html.replace('_delete_','');
	}
	
	if(this.showEdit){
		html = html.replace('_edit_',editButton);
	}else{
		html = html.replace('_edit_','');
	}
	
	html = html.replace(/_id_/g,id);
	html = html.replace(/_BASE_/g,this.baseUrl);
	return html;
});


IceHRMBase.method('generateRandom', function(length) {
	var chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	var result = '';
	for (var i = length; i > 0; --i) result += chars[Math.round(Math.random() * (chars.length - 1))];
	return result;
});



IceHRMBase.method('checkFileType', function (elementName, fileTypes) {
	var fileElement = document.getElementById(elementName);
	var fileExtension = "";
	if (fileElement.value.lastIndexOf(".") > 0) {
		fileExtension = fileElement.value.substring(fileElement.value.lastIndexOf(".") + 1, fileElement.value.length);
	}
	
	fileExtension = fileExtension.toLowerCase();
	
	var allowed = fileTypes.split(",");
	
	if (allowed.indexOf(fileExtension) < 0) {
		fileElement.value = "";
		this.showMessage("File Type Error",'Selected file type is not supported');
		this.clearFileElement(elementName);
		return false;
	}
	
	return true;
	
});

IceHRMBase.method('clearFileElement', function (elementName) {

	var control = $("#"+elementName);
	control.replaceWith( control = control.val('').clone( true ) );
});


IceHRMBase.method('fixJSON', function (json) {
	if(this.noJSONRequests == "1"){
		json = json.replace(/"/g,'|');
	}
	return json;
});


IceHRMBase.method('getClientDate', function (date) {

	var offset = this.getClientGMTOffset();
    var tzDate = date.addMinutes(offset*60);
    return tzDate;

});

IceHRMBase.method('getClientGMTOffset', function () {
	
	var rightNow = new Date();
	var jan1 = new Date(rightNow.getFullYear(), 0, 1, 0, 0, 0, 0);
	var temp = jan1.toGMTString();
	var jan2 = new Date(temp.substring(0, temp.lastIndexOf(" ")-1));
	var std_time_offset = (jan1 - jan2) / (1000 * 60 * 60);
	
	return std_time_offset;
	
});

IceHRMBase.method('getHelpLink', function () {

	return null;

});