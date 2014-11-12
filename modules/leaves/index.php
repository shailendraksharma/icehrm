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

$moduleName = 'employee_leave';
define('MODULE_PATH',dirname(__FILE__));
include APP_BASE_PATH.'header.php';
include APP_BASE_PATH.'modulejslibs.inc.php';
?><div class="span9">
			  
	<ul class="nav nav-tabs" id="modTab" style="margin-bottom:0px;margin-left:5px;border-bottom: none;">
		<li class="active"><a id="tabEmployeeLeaveAll" href="#tabPageEmployeeLeaveAll">All My Leaves</a></li>
		<li class=""><a id="tabEmployeeLeaveEntitlement" href="#tabPageEmployeeLeaveEntitlement">Leaves Entitlement</a></li>
		<li class=""><a id="tabEmployeeLeaveApproved" href="#tabPageEmployeeLeaveApproved">Approved Leaves</a></li>
		<li class=""><a id="tabEmployeeLeavePending" href="#tabPageEmployeeLeavePending">Pending Leaves</a></li>
		<li class=""><a id="tabSubEmployeeLeaveAll" href="#tabPageSubEmployeeLeaveAll">Subordinate Leaves</a></li>
	</ul>
	 
	<div class="tab-content">
		<div class="tab-pane active" id="tabPageEmployeeLeaveAll">
			<div id="EmployeeLeaveAll" class="reviewBlock" data-content="List" style="padding-left:5px;">
		
			</div>
			<div id="EmployeeLeaveAllForm" class="reviewBlock" data-content="Form" style="padding-left:5px;display:none;">
		
			</div>
		</div>
		<div class="tab-pane" id="tabPageEmployeeLeaveEntitlement">
			<div id="EmployeeLeaveEntitlement" class="reviewBlock" data-content="List" style="padding-left:5px;">
		
			</div>
		</div>
		<div class="tab-pane" id="tabPageEmployeeLeaveApproved">
			<div id="EmployeeLeaveApproved" class="reviewBlock" data-content="List" style="padding-left:5px;">
		
			</div>
			<div id="EmployeeLeaveApprovedForm" class="reviewBlock" data-content="Form" style="padding-left:5px;display:none;">
		
			</div>
		</div>
		<div class="tab-pane" id="tabPageEmployeeLeavePending">
			<div id="EmployeeLeavePending" class="reviewBlock" data-content="List" style="padding-left:5px;">
		
			</div>
			<div id="EmployeeLeavePendingForm" class="reviewBlock" data-content="Form" style="padding-left:5px;display:none;">
		
			</div>
		</div>
		<div class="tab-pane" id="tabPageSubEmployeeLeaveAll">
			<div id="SubEmployeeLeaveAll" class="reviewBlock" data-content="List" style="padding-left:5px;">
		
			</div>
			<div id="SubEmployeeLeaveAllForm" class="reviewBlock" data-content="Form" style="padding-left:5px;display:none;">
		
			</div>
		</div>
	</div>

</div>
<script>
var modJsList = new Array();

modJsList['tabEmployeeLeaveAll'] = new EmployeeLeaveAdapter('EmployeeLeave','EmployeeLeaveAll','','date_start desc');
modJsList['tabEmployeeLeaveApproved'] = new EmployeeLeaveAdapter('EmployeeLeave','EmployeeLeaveApproved',{"status":"Approved"});
modJsList['tabEmployeeLeaveApproved'].setShowAddNew(false);
modJsList['tabEmployeeLeavePending'] = new EmployeeLeaveAdapter('EmployeeLeave','EmployeeLeavePending',{"status":"Pending"});
modJsList['tabEmployeeLeavePending'].setShowAddNew(false);
modJsList['tabSubEmployeeLeaveAll'] = new SubEmployeeLeaveAdapter('EmployeeLeave','SubEmployeeLeaveAll');
modJsList['tabSubEmployeeLeaveAll'].setShowAddNew(false);
modJsList['tabSubEmployeeLeaveAll'].setRemoteTable(true);
modJsList['tabEmployeeLeaveEntitlement'] = new EmployeeLeaveEntitlementAdapter('EmployeeLeaveEntitlement','EmployeeLeaveEntitlement');
modJsList['tabEmployeeLeaveEntitlement'].setShowAddNew(false);

var modJs = modJsList['tabEmployeeLeaveAll'];

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
			  	<select class="form-control" type="text" id="leave_status" name="leave_status" value="">
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