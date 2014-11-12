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

class LeavesActionManager extends SubActionManager{
	
	const FULLDAY = 1;
	const HALFDAY = 0;
	const NOTWORKINGDAY = 2;
	
	public function addLeave($req){
		$employee = $this->baseService->getElement('Employee',$this->getCurrentEmployeeId(),null,true);
		$rule = $this->getLeaveRule($employee, $req->leave_type);
		
		
		if($this->user->user_level == 'Admin' && $this->getCurrentEmployeeId() != $this->user->employee){
			//Admin is updating information for an employee
			if($rule->supervisor_leave_assign == "No"){
				return new IceResponse(IceResponse::ERROR,"You are not allowed to assign this type of leaves as admin");
			}
		}else{
			if($rule->employee_can_apply == "No"){
				return new IceResponse(IceResponse::ERROR,"You are not allowed to apply for this type of leaves");
			}
		}


		//Find Current leave period
		$leaveCounts = array();
		$currentLeavePeriodResp = $this->getCurrentLeavePeriod($req->date_start, $req->date_end);
		if($currentLeavePeriodResp->getStatus() != IceResponse::SUCCESS){
			return new IceResponse(IceResponse::ERROR,$currentLeavePeriodResp->getData());
		}else{
			$currentLeavePeriod = $currentLeavePeriodResp->getData();
		}
		
		//Validate leave days
		$days = json_decode($req->days);
		foreach($days as $day=>$type){
			
			$sql = "select ld.id as leaveId from EmployeeLeaveDays ld join EmployeeLeaves el on ld.employee_leave = el.id where el.employee = ? and ld.leave_date = ? and ld.leave_type = ? and el.status <> 'Rejected'";
			$rs = $this->baseService->getDB()->Execute($sql, array($employee->id,date("Y-m-d",strtotime($day)),$type));
			$counter = 0;
			foreach ($rs as $k => $v) {
				$counter++;
			}	
			
			if($counter > 0){
				return new IceResponse(IceResponse::ERROR,"This leave is overlapping with another leave you have already applied");	
			}
		}

		//Adding Employee Leave
		$employeeLeave = new EmployeeLeave();
		$employeeLeave->employee = $employee->id;
		$employeeLeave->leave_type = $req->leave_type;
		$employeeLeave->leave_period = $currentLeavePeriod->id;
		$employeeLeave->date_start = $req->date_start;
		$employeeLeave->date_end = $req->date_end;
		$employeeLeave->details = $req->details;
		$employeeLeave->status = "Pending";
		$employeeLeave->details = $req->details;
		$employeeLeave->attachment = isset($req->attachment)?$req->attachment:"";
		
		$ok = $employeeLeave->Save();
		
		if(!$ok){
			error_log($employeeLeave->ErrorMsg());
			return new IceResponse(IceResponse::ERROR,"Error occured while applying leave.");	
		}
		
		$days = json_decode($req->days);
		
		foreach($days as $day=>$type){
			$employeeLeaveDay = new EmployeeLeaveDay();
			$employeeLeaveDay->employee_leave = $employeeLeave->id;	
			$employeeLeaveDay->leave_date = date("Y-m-d",strtotime($day));
			$employeeLeaveDay->leave_type = $type;
			$employeeLeaveDay->Save();
		}
		
		if(!empty($this->emailSender)){
			$leavesEmailSender = new LeavesEmailSender($this->emailSender, $this);
			$leavesEmailSender->sendLeaveApplicationEmail($employee);
			$leavesEmailSender->sendLeaveApplicationSubmittedEmail($employee);
		}
		
		$this->baseService->audit(IceConstants::AUDIT_ACTION, "Leave applied \ start:".$employeeLeave->date_start."\ end:".$employeeLeave->date_end);
		$notificationMsg = $employee->first_name." ".$employee->last_name." applied for a leave. Visit leave module to approve or reject";
		
		$this->baseService->notificationManager->addNotification($employee->supervisor,$notificationMsg,'{"type":"url","url":"g=modules&n=leaves&m=module_Leaves#tabSubEmployeeLeaveAll"}',IceConstants::NOTIFICATION_LEAVE);
		return new IceResponse(IceResponse::SUCCESS,$employeeLeave);
	}
	
	public function getEntitlement($req){
		$employee = $this->baseService->getElement('Employee',$this->getCurrentEmployeeId(),null,true);
		
		$leaveEntitlementArray = array();
		
		$leaveType = new LeaveType();
		$leaveTypes = $leaveType->Find("1=1");
		
		//Find Current leave period
		
		$currentLeavePeriodResp = $this->getCurrentLeavePeriod(date('Y-m-d'),date('Y-m-d'));
		if($currentLeavePeriodResp->getStatus() != IceResponse::SUCCESS){
			return new IceResponse(IceResponse::ERROR,$currentLeavePeriodResp->getData());
		}else{
			$currentLeavePeriod = $currentLeavePeriodResp->getData();
		}
		
		foreach($leaveTypes as $leaveType){
			//$rule = $this->getLeaveRule($employee, $leaveType->id);
			$leaveMatrix = $this->getAvailableLeaveMatrixForEmployeeLeaveType($employee, $currentLeavePeriod, $leaveType->id);
			
			$leaves = array();
			$leaves['id'] = $leaveType->id;
			$leaves['name'] = $leaveType->name;
			//$leaves['totalLeaves'] = floatval($leaveMatrix[0]);
			$leaves['pendingLeaves'] = floatval($leaveMatrix[1]);
			$leaves['approvedLeaves'] = floatval($leaveMatrix[2]);
			$leaves['rejectedLeaves'] = floatval($leaveMatrix[3]);
			$leaves['availableLeaves'] = floatval($leaveMatrix[0]) - $leaves['pendingLeaves'] -  $leaves['approvedLeaves'];
			$leaves['tobeAccrued'] = floatval($leaveMatrix[4]['total']) - floatval($leaveMatrix[4]['accrued']);
			$leaves['carriedForward'] = floatval($leaveMatrix[4]['carriedForward']);
			
			$leaveEntitlementArray[] = $leaves;
		}
		
		return new IceResponse(IceResponse::SUCCESS,$leaveEntitlementArray);
	}

	public function getLeaveDays($req){
				
		$employee = $this->baseService->getElement('Employee',$this->getCurrentEmployeeId(),null,true);
		$rule = $this->getLeaveRule($employee, $req->leave_type);
		
		if($this->user->user_level == 'Admin' && $this->getCurrentEmployeeId() != $this->user->employee){
			//Admin is updating information for an employee	
			if($rule->supervisor_leave_assign == "No"){
				return new IceResponse(IceResponse::ERROR,"You are not allowed to assign this type of leaves as admin");
			}
		}else{
			if($rule->employee_can_apply == "No"){
				return new IceResponse(IceResponse::ERROR,"You are not allowed to apply for this type of leaves");		
			}	
		}

		//Find Current leave period
		$leaveCounts = array();
		$currentLeavePeriodResp = $this->getCurrentLeavePeriod($req->start_date,$req->end_date);
		if($currentLeavePeriodResp->getStatus() != IceResponse::SUCCESS){
			return new IceResponse(IceResponse::ERROR,$currentLeavePeriodResp->getData());
		}else{
			$currentLeavePeriod = $currentLeavePeriodResp->getData();
		}

		$leaveMatrix = $this->getAvailableLeaveMatrixForEmployeeLeaveType($employee, $currentLeavePeriod, $req->leave_type);

		$leaves = array();
		$leaves['totalLeaves'] = floatval($leaveMatrix[0]);
		$leaves['pendingLeaves'] = floatval($leaveMatrix[1]);
		$leaves['approvedLeaves'] = floatval($leaveMatrix[2]);
		$leaves['rejectedLeaves'] = floatval($leaveMatrix[3]);
		$leaves['availableLeaves'] = $leaves['totalLeaves'] - $leaves['pendingLeaves'] -  $leaves['approvedLeaves'];

		//=== Resolve Employee Country
		$employeeCountry = NULL;
		
		if(!empty($employee->country)){
			$country = new Country();
			$country->Load("code = ?",array($employee->country));
			$employeeCountry = $country->id;
		}
		
		//============================
		
		
		$startDate = $req->start_date;
		$endDate = $req->end_date;
		$days = array();
		$days = $this->getDays($startDate, $endDate);
		$dayMap = array();
		foreach($days as $day){
			$dayMap[$day] = $this->getDayWorkTime($day, $employeeCountry);
		}
		
		return new IceResponse(IceResponse::SUCCESS,array($dayMap,$leaves,$rule));
	}
	
	public function getLeaveDaysReadonly($req){
		$leaveId = $req->leave_id;
		$leaveLogs = array();
		
		$employeeLeave = new EmployeeLeave();
		$employeeLeave->Load("id = ?",array($leaveId));

		$employee = $this->baseService->getElement('Employee',$employeeLeave->employee,null,true);
		$rule = $this->getLeaveRule($employee, $employeeLeave->leave_type);
		
		$currentLeavePeriodResp = $this->getCurrentLeavePeriod($employeeLeave->date_start, $employeeLeave->date_end);
		if($currentLeavePeriodResp->getStatus() != IceResponse::SUCCESS){
			return new IceResponse(IceResponse::ERROR,$currentLeavePeriodResp->getData());
		}else{
			$currentLeavePeriod = $currentLeavePeriodResp->getData();
		}

		$leaveMatrix = $this->getAvailableLeaveMatrixForEmployeeLeaveType($employee, $currentLeavePeriod, $employeeLeave->leave_type);

		$leaves = array();
		$leaves['totalLeaves'] = floatval($leaveMatrix[0]);
		$leaves['pendingLeaves'] = floatval($leaveMatrix[1]);
		$leaves['approvedLeaves'] = floatval($leaveMatrix[2]);
		$leaves['rejectedLeaves'] = floatval($leaveMatrix[3]);
		$leaves['availableLeaves'] = $leaves['totalLeaves'] - $leaves['pendingLeaves'] -  $leaves['approvedLeaves'];
		$leaves['attachment'] = $employeeLeave->attachment;

		$employeeLeaveDay = new EmployeeLeaveDay();
		$days = $employeeLeaveDay->Find("employee_leave = ?",array($leaveId));
		
		$employeeLeaveLog = new EmployeeLeaveLog();
		$logsTemp = $employeeLeaveLog->Find("employee_leave = ? order by created",array($leaveId));
		foreach($logsTemp as $empLeaveLog){
			$t = array();
			$t['time'] = $empLeaveLog->created;
			$t['status_from'] = $empLeaveLog->status_from;
			$t['status_to'] = $empLeaveLog->status_to;
			$t['time'] = $empLeaveLog->created;
			$userName = null;
			if(!empty($empLeaveLog->user_id)){
				$lgUser = new User();
				$lgUser->Load("id = ?",array($empLeaveLog->user_id));
				if($lgUser->id == $empLeaveLog->user_id){
					if(!empty($lgUser->employee)){
						$lgEmployee = new Employee();
						$lgEmployee->Load("id = ?",array($lgUser->employee));
						$userName = $lgEmployee->first_name." ".$lgEmployee->last_name;
					}else{
						$userName = $lgUser->userName;
					}
					
				}
			}
			
			if(!empty($userName)){
				$t['note'] = $empLeaveLog->data." (by: ".$userName.")";
			}else{
				$t['note'] = $empLeaveLog->data;
			}
			
			$leaveLogs[] = $t;
		}
		
		return new IceResponse(IceResponse::SUCCESS,array($days,$leaves,$leaveId,$employeeLeave,$leaveLogs));
	}
	
	private function getDays($start, $end){
		$days = array();
		$curent = $start;
		while(strtotime($curent)<=strtotime($end)){
			$days[] = $curent;
			$curent = date("Y-m-d",strtotime("+1 day",strtotime($curent)));	
		}
		return $days; 
	}
	
	private function getDayWorkTime($day, $countryId){
		$holiday = $this->getHoliday($day, $countryId);
		if(!empty($holiday)){
			if($holiday->status == 'Full Day'){
				return self::NOTWORKINGDAY;	
			}else{
				return self::HALFDAY;
			}	
		}
		
		$workday = $this->getWorkDay($day, $countryId);
		if(empty($workday)){
			return self::FULLDAY;
		}
		
		if($workday->status == 'Full Day'){
			return self::FULLDAY;
		}else if($workday->status == 'Half Day'){
			return self::HALFDAY;
		}else{
			return self::NOTWORKINGDAY;
		}
		
	}
	
	private function getWorkDay($day, $countryId){
		$dayName = date("l",strtotime($day));
		$workDay = new WorkDay();
		if(empty($countryId)){
			$workDay->Load("name = ? and country IS NULL",array($dayName));
			if($workDay->name == $dayName){
				return $workDay;
			}	
		}else{
			$workDay->Load("name = ? and country = ?",array($dayName, $countryId));
			if($workDay->name == $dayName){
				return $workDay;
			}else{
				$workDay = new WorkDay();
				$workDay->Load("name = ? and country IS NULL",array($dayName));
				if($workDay->name == $dayName){
					return $workDay;
				}
			}
		}
		
		return null;
	}
	
	private function getHoliday($day, $countryId){
		$hd = new HoliDay();
		if(empty($countryId)){
			$hd->Load("dateh = ? and country IS NULL",array($day));
			if($hd->dateh == $day){
				return $hd;
			}	
		}else{
			$hd->Load("dateh = ? and country = ?",array($day, $countryId));
			if($hd->dateh == $day){
				return $hd;
			}else{
				$hd = new HoliDay();
				$hd->Load("dateh = ? and country IS NULL",array($day));
				if($hd->dateh == $day){
					return $hd;
				}
			}
		}
		
		return null;
	}

	private function getAvailableLeaveMatrixForEmployee($employee,$currentLeavePeriod){


		//Iterate all leave types and create leave matrix
		/**
		 * [[Leave Type],[Total Available],[Pending],[Approved],[Rejected]]
		 */
		$leaveType = new LeaveType();
		$leaveTypes = $leaveType->Find("1=1",array());

		foreach($leaveTypes as $leaveType){
			$employeeLeaveQuota = new stdClass();
				
			$rule = $this->getLeaveRule($employee, $leaveType->id);
			$employeeLeaveQuota->avalilable = floatval($rule->default_per_year);
			$pending = $this->countLeaveAmounts($this->getEmployeeLeaves($employee->id, $currentLeavePeriod->id, $leaveType->id, 'Pending'));
			$approved = $this->countLeaveAmounts($this->getEmployeeLeaves($employee->id, $currentLeavePeriod->id, $leaveType->id, 'Approved'));
			$rejected = $this->countLeaveAmounts($this->getEmployeeLeaves($employee->id, $currentLeavePeriod->id, $leaveType->id, 'Rejected'));
				
				
			$leaveCounts[$leaveType->name] = array($avalilable,$pending,$approved,$rejected);
		}

		return $leaveCounts;

	}

	private function getAvailableLeaveMatrixForEmployeeLeaveType($employee,$currentLeavePeriod,$leaveTypeId){

		/**
		 * [Total Available],[Pending],[Approved],[Rejected]
		 */

		$rule = $this->getLeaveRule($employee, $leaveTypeId);
		//$avalilable = $rule->default_per_year;
		$avalilableLeaves = $this->getAvailableLeaveCount($employee, $rule, $currentLeavePeriod, $leaveTypeId);
		$avalilable = $avalilableLeaves[0];
		$pending = $this->countLeaveAmounts($this->getEmployeeLeaves($employee->id, $currentLeavePeriod->id, $leaveTypeId, 'Pending'));
		$approved = $this->countLeaveAmounts($this->getEmployeeLeaves($employee->id, $currentLeavePeriod->id, $leaveTypeId, 'Approved'));
		$rejected = $this->countLeaveAmounts($this->getEmployeeLeaves($employee->id, $currentLeavePeriod->id, $leaveTypeId, 'Rejected'));

		return array($avalilable,$pending,$approved,$rejected,$avalilableLeaves[1]);


	}
	
	/*
	 * Find available leave counts considering Leaves Accrued and Carried Forward
	 */
	private function getAvailableLeaveCount($employee, $rule, $currentLeavePeriod, $leaveTypeId){
		
		$availableLeaveArray = array();
		
		$currentLeaves = intval($rule->default_per_year);
		$availableLeaveArray["total"] = $currentLeaves;
		if($rule->leave_accrue == "Yes"){
			$dateTodayTime = strtotime(date("Y-m-d"));	
			$startTime = strtotime($currentLeavePeriod->date_start);
			$endTime = strtotime($currentLeavePeriod->date_end);
			$datediffFromStart = $dateTodayTime - $startTime;
			$datediffPeriod = $endTime - $startTime;
			
			$currentLeaves = intval(ceil(($currentLeaves * $datediffFromStart)/$datediffPeriod));
		}	
		
		$availableLeaveArray["accrued"] = $currentLeaves;
		
		$availableLeaveArray["carriedForward"] = 0;

		if($rule->carried_forward == "Yes"){
			//findPreviosLeavePeriod
			$dayInPreviousLeavePeriod = date('Y-m-d', strtotime($currentLeavePeriod->date_start.' -1 day'));
			$resp = $this->getCurrentLeavePeriod($dayInPreviousLeavePeriod,$dayInPreviousLeavePeriod);
			if($resp->getStatus() == "SUCCESS"){
				$prvLeavePeriod = $resp->getData();
				$avalilable = $rule->default_per_year;
				$approved = $this->countLeaveAmounts($this->getEmployeeLeaves($employee->id, $prvLeavePeriod->id, $leaveTypeId, 'Approved'));
				
				$leavesCarriedForward =  intval($avalilable) - intval($approved);
				if($leavesCarriedForward < 0){
					$leavesCarriedForward = 0;
				}
				$availableLeaveArray["carriedForward"] = $leavesCarriedForward;
				$currentLeaves = intval($currentLeaves) + intval($leavesCarriedForward);
			}
		}
		
		return array($currentLeaves, $availableLeaveArray);
	}

	private function countLeaveAmounts($leaves){
		$amount = 0;
		foreach($leaves as $leave){
			$empLeaveDay = new EmployeeLeaveDay();
			$leaveDays = $empLeaveDay->Find("employee_leave = ?",array($leave->id));
			foreach($leaveDays as $leaveDay){
				if($leaveDay->leave_type == 'Full Day'){
					$amount += 1;
				}else if($leaveDay->leave_type == 'Half Day - Morning'){
					$amount += 0.5;
				}else if($leaveDay->leave_type == 'Half Day - Afternoon'){
					$amount += 0.5;
				}
			}
		}
		return floatval($amount);
	}

	private function getEmployeeLeaves($employeeId,$leavePeriod,$leaveType,$status){
		$employeeLeave = new EmployeeLeave();
		$employeeLeaves = $employeeLeave->Find("employee = ? and leave_period = ? and leave_type = ? and status = ?",
		array($employeeId,$leavePeriod,$leaveType,$status));
		if(!$employeeLeaves){
			error_log($employeeLeave->ErrorMsg());
		}
		
		return $employeeLeaves;
			
	}

	public function getCurrentLeavePeriod($startDate,$endDate){
		
		$leavePeriod = new LeavePeriod();
		$leavePeriod->Load("date_start <= ? and date_end >= ?",array($startDate,$endDate));
		if(empty($leavePeriod->id)){
			$leavePeriod1 = new LeavePeriod();
			$leavePeriod1->Load("date_start <= ? and date_end >= ?",array($startDate,$startDate));
			
			$leavePeriod2 = new LeavePeriod();
			$leavePeriod2->Load("date_start <= ? and date_end >= ?",array($endDate,$endDate));
			
			if(!empty($leavePeriod1->id) && !empty($leavePeriod2->id)){
				return new IceResponse(IceResponse::ERROR,"You are trying to apply leaves in two leave periods. You may apply leaves til $leavePeriod1->date_end. Rest you have to apply seperately" );
			}else{
				return new IceResponse(IceResponse::ERROR,"The leave period for your leave application is not defined. Please inform administrator" );
			}
			
		}else{
			return new IceResponse(IceResponse::SUCCESS,$leavePeriod);
		}
	}

	private function getLeaveRule($employee,$leaveType){
		$rule = null;
		$leaveRule = new LeaveRule();
		$leaveTypeObj = new LeaveType();
		$rules = $leaveRule->Find("employee = ? and leave_type = ?",array($employee->id,$leaveType));
		if(count($rules)>0){
			return $rules[0];
		}

		$rules = $leaveRule->Find("job_title = ? and employment_status = ? and leave_type = ? and employee is null",array($employee->job_title,$employee->employment_status,$leaveType));
		if(count($rules)>0){
			return $rules[0];
		}

		$rules = $leaveRule->Find("job_title = ? and employment_status is null and leave_type = ? and employee is null",array($employee->job_title,$leaveType));
		if(count($rules)>0){
			return $rules[0];
		}

		$rules = $leaveRule->Find("job_title is null and employment_status = ? and leave_type = ? and employee is null",array($employee->employment_status,$leaveType));
		if(count($rules)>0){
			return $rules[0];
		}

		$rules = $leaveTypeObj->Find("id = ?",array($leaveType));
		if(count($rules)>0){
			return $rules[0];
		}

	}
	
	
	public function getSubEmployeeLeaves($req){
		
		$employee = $this->baseService->getElement('Employee',$this->getCurrentEmployeeId(),null,true);
		
		$subordinate = new Employee();
		$subordinates = $subordinate->Find("supervisor = ?",array($employee->id));
		
		$subordinatesIds = "";
		foreach($subordinates as $sub){
			if($subordinatesIds != ""){
				$subordinatesIds.=",";	
			}
			$subordinatesIds.=$sub->id;
		}
		$subordinatesIds.="";
		
		
		$mappingStr = $req->sm;
		$map = json_decode($mappingStr);
		$employeeLeave = new EmployeeLeave();
		$list = $employeeLeave->Find("employee in (".$subordinatesIds.")",array());	
		if(!$list){
			error_log($employeeLeave->ErrorMsg());	
		}
		if(!empty($mappingStr)){
			$list = $this->baseService->populateMapping($list,$map);	
		}
		return new IceResponse(IceResponse::SUCCESS,$list);
	}
	
	public function changeLeaveStatus($req){
		$employee = $this->baseService->getElement('Employee',$this->getCurrentEmployeeId(),null,true);
		
		$subordinate = new Employee();
		$subordinates = $subordinate->Find("supervisor = ?",array($employee->id));
		
		$subordinatesIds = array();
		foreach($subordinates as $sub){
			$subordinatesIds[] = $sub->id;
		}
		
		
		$employeeLeave = new EmployeeLeave();
		$employeeLeave->Load("id = ?",array($req->id));
		if($employeeLeave->id != $req->id){
			return new IceResponse(IceResponse::ERROR,"Leave not found");
		}
		
		if(!in_array($employeeLeave->employee, $subordinatesIds) && $this->user->user_level != 'Admin'){
			return new IceResponse(IceResponse::ERROR,"This leave does not belong to any of your subordinates");	
		}
		$oldLeaveStatus = $employeeLeave->status;
		$employeeLeave->status = $req->status;
		
		if($oldLeaveStatus == $req->status){
			return new IceResponse(IceResponse::SUCCESS,"");
		}
		
		
		$ok = $employeeLeave->Save();
		if(!$ok){
			error_log($employeeLeave->ErrorMsg());
			return new IceResponse(IceResponse::ERROR,"Error occured while saving leave infomation. Please contact admin");
		}
		
		$employeeLeaveLog = new EmployeeLeaveLog();
		$employeeLeaveLog->employee_leave = $employeeLeave->id;
		$employeeLeaveLog->user_id = $this->baseService->getCurrentUser()->id;
		$employeeLeaveLog->status_from = $oldLeaveStatus;
		$employeeLeaveLog->status_to = $employeeLeave->status;
		$employeeLeaveLog->created = date("Y-m-d H:i:s");
		$employeeLeaveLog->data = isset($req->reason)?$req->reason:"";
		$ok = $employeeLeaveLog->Save();
		if(!$ok){
			error_log($employeeLeaveLog->ErrorMsg());
		}
		
		
		if(!empty($this->emailSender) && $oldLeaveStatus != $employeeLeave->status){
			$leavesEmailSender = new LeavesEmailSender($this->emailSender, $this);
			$leavesEmailSender->sendLeaveStatusChangedEmail($employee, $employeeLeave);
		}
		
		
		$this->baseService->audit(IceConstants::AUDIT_ACTION, "Leave status changed \ from:".$oldLeaveStatus."\ to:".$employeeLeave->status." \ id:".$employeeLeave->id);
		
		if($employeeLeave->status != "Pending"){
			$notificationMsg = "Your leave has been $employeeLeave->status by ".$employee->first_name." ".$employee->last_name;
			if(!empty($req->reason)){
				$notificationMsg.=" (Note:".$req->reason.")";
			}
		}
		
		$this->baseService->notificationManager->addNotification($employeeLeave->employee,$notificationMsg,'{"type":"url","url":"g=modules&n=leaves&m=module_Leaves#tabEmployeeLeaveApproved"}',IceConstants::NOTIFICATION_LEAVE);
		
		return new IceResponse(IceResponse::SUCCESS,"");
	}

}