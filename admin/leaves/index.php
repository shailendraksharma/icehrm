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

$moduleName = 'leaves';
define('MODULE_PATH',dirname(__FILE__));
include APP_BASE_PATH.'header.php';
include APP_BASE_PATH.'modulejslibs.inc.php';
?><div class="span9">
			  
	<ul class="nav nav-tabs" id="modTab" style="margin-bottom:0px;margin-left:5px;border-bottom: none;">
		<li class="active"><a id="tabLeaveType" href="#tabPageLeaveType">Leave Types</a></li>
		<li ><a id="tabLeavePeriod" href="#tabPageLeavePeriod">Leave Period</a></li>
		<li ><a id="tabWorkDay" href="#tabPageWorkDay">Work Week</a></li>
		<li ><a id="tabHoliDay" href="#tabPageHoliDay">Holidays</a></li>
		<li ><a id="tabLeaveRule" href="#tabPageLeaveRule">Leave Rules</a></li>
		<li ><a id="tabEmployeeLeave" href="#tabPageEmployeeLeave">Employee Leaves</a></li>
	</ul>
	 
	<div class="tab-content">
		<div class="tab-pane active" id="tabPageLeaveType">
			<div id="LeaveType" class="reviewBlock" data-content="List" style="padding-left:5px;">
		
			</div>
			<div id="LeaveTypeForm" class="reviewBlock" data-content="Form" style="padding-left:5px;display:none;">
		
			</div>
		</div>
		<div class="tab-pane" id="tabPageLeavePeriod">
			<div id="LeavePeriod" class="reviewBlock" data-content="List" style="padding-left:5px;">
		
			</div>
			<div id="LeavePeriodForm" class="reviewBlock" data-content="Form" style="padding-left:5px;display:none;">
		
			</div>
		</div>
		<div class="tab-pane" id="tabPageWorkDay">
			<div id="WorkDay" class="reviewBlock" data-content="List" style="padding-left:5px;">
		
			</div>
			<div id="WorkDayForm" class="reviewBlock" data-content="Form" style="padding-left:5px;display:none;">
		
			</div>
		</div>
		<div class="tab-pane" id="tabPageHoliDay">
			<div id="HoliDay" class="reviewBlock" data-content="List" style="padding-left:5px;">
		
			</div>
			<div id="HoliDayForm" class="reviewBlock" data-content="Form" style="padding-left:5px;display:none;">
		
			</div>
		</div>
		<div class="tab-pane" id="tabPageLeaveRule">
			<div id="LeaveRule" class="reviewBlock" data-content="List" style="padding-left:5px;">
		
			</div>
			<div id="LeaveRuleForm" class="reviewBlock" data-content="Form" style="padding-left:5px;display:none;">
		
			</div>
		</div> 
		<div class="tab-pane" id="tabPageEmployeeLeave">
			<div id="EmployeeLeave" class="reviewBlock" data-content="List" style="padding-left:5px;">
		
			</div>
			<div id="EmployeeLeaveForm" class="reviewBlock" data-content="Form" style="padding-left:5px;display:none;">
		
			</div>
		</div>
	</div>

</div>
<script>
var modJsList = new Array();
modJsList['tabLeaveType'] = new LeaveTypeAdapter('LeaveType');
modJsList['tabLeaveRule'] = new LeaveRuleAdapter('LeaveRule');
modJsList['tabLeavePeriod'] = new LeavePeriodAdapter('LeavePeriod');
modJsList['tabWorkDay'] = new WorkDayAdapter('WorkDay');
modJsList['tabHoliDay'] = new HoliDayAdapter('HoliDay','HoliDay','','dateh');
modJsList['tabEmployeeLeave'] = new EmployeeLeaveAdapter('EmployeeLeave','EmployeeLeave','','date_start desc');
modJsList['tabEmployeeLeave'].setShowAddNew(false);
modJsList['tabEmployeeLeave'].setRemoteTable(true);
modJsList['tabHoliDay'].setRemoteTable(true);
var modJs = modJsList['tabLeaveType'];

</script>

<div class="modal" id="leaveStatusModel" tabindex="-1" role="dialog" aria-labelledby="messageModelLabel" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">	
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><li class="fa fa-times"/></button>
		<h3 style="font-size: 17px;">Change Leave Status</h3>
	</div>
	<div class="modal-body">
		<form id="leaveStatusForm">
		<div class="control-group">
			<label class="control-label" for="leave_status">Leave Status</label>
			<div class="controls">
			  	<select type="text" id="leave_status" class="form-control" name="leave_status" value="">
				  	<option value="Approved">Approved</option>
				  	<option value="Pending">Pending</option>
				  	<option value="Rejected">Rejected</option>
			  	</select>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="leave_status">Status Change Note</label>
			<div class="controls">
			  	<textarea id="leave_reason" class="form-control" name="leave_reason" maxlength="500"></textarea>
			</div>
		</div>
		</form>
	</div>
	<div class="modal-footer">
 		<button class="btn btn-primary" onclick="modJs.changeLeaveStatus();">Change Leave Status</button>
 		<button class="btn" onclick="modJs.closeLeaveStatus();">Not Now</button>
	</div>
</div>
</div>
</div>

<?php include APP_BASE_PATH.'footer.php';?>      