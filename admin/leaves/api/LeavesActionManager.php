<?php
include (APP_BASE_PATH."modules/leaves/api/LeavesEmailSender.php");
class AdminLeavesEmailSender extends LeavesEmailSender{

}

class LeavesActionManager extends SubActionManager{
	
	const FULLDAY = 1;
	const HALFDAY = 0;
	const NOTWORKINGDAY = 2;
	
	
	
	public function getLeaveDaysReadonly1($req){
		$leaveId = $req->leave_id;
		$leaveLogs = array();
		error_log(json_encode($req));
		$employeeLeave = new EmployeeLeave();
		$employeeLeave->Load("id = ?",array($leaveId));

		$employee = $this->baseService->getElement('Employee',$employeeLeave->employee,null,true);
		$rule = $this->getLeaveRule($employee, $employeeLeave->leave_type);
		
		$currentLeavePeriod = $this->getCurrentLeavePeriod($employeeLeave->date_start, $employeeLeave->date_end)->getData();

		$leaveMatrix = $this->getAvailableLeaveMatrixForEmployeeLeaveType($employee, $currentLeavePeriod, $employeeLeave->leave_type);

		$leaves = array();
		$leaves['totalLeaves'] = floatval($leaveMatrix[0]);
		$leaves['pendingLeaves'] = floatval($leaveMatrix[1]);
		$leaves['approvedLeaves'] = floatval($leaveMatrix[2]);
		$leaves['rejectedLeaves'] = floatval($leaveMatrix[3]);
		$leaves['availableLeaves'] = $leaves['totalLeaves'] - $leaves['pendingLeaves'] -  $leaves['approvedLeaves'];

		$employeeLeaveDay = new EmployeeLeaveDay();
		$days = $employeeLeaveDay->Find("employee_leave = ?",array($leaveId));
		
		
		
		
		return new IceResponse(IceResponse::SUCCESS,array($days,$leaves));
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
		$avalilable = $rule->default_per_year;
		$pending = $this->countLeaveAmounts($this->getEmployeeLeaves($employee->id, $currentLeavePeriod->id, $leaveTypeId, 'Pending'));
		$approved = $this->countLeaveAmounts($this->getEmployeeLeaves($employee->id, $currentLeavePeriod->id, $leaveTypeId, 'Approved'));
		$rejected = $this->countLeaveAmounts($this->getEmployeeLeaves($employee->id, $currentLeavePeriod->id, $leaveTypeId, 'Rejected'));

		return array($avalilable,$pending,$approved,$rejected);


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
			error_log($employeeLeave->ErrorMsg(),true);
		}
		
		return $employeeLeaves;
			
	}
	
	private function getCurrentLeavePeriod($startDate,$endDate){
	
		$leavePeriod = new LeavePeriod();
		$leavePeriod->Load("date_start <= ? and date_end >= ?",array($startDate,$endDate));
		if(empty($leavePeriod->id)){
			return new IceResponse(IceResponse::ERROR,"Error in leave period" );
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

		$mappingStr = $req->sm;
		$map = json_decode($mappingStr);
		$employeeLeave = new EmployeeLeave();
		$list = $employeeLeave->Find("1=1");	
		if(!$list){
			error_log($employeeLeave->ErrorMsg());	
		}
		if(!empty($mappingStr)){
			$list = $this->baseService->populateMapping($list,$map);	
		}
		
		return new IceResponse(IceResponse::SUCCESS,$list);
	}
	
	public function changeLeaveStatus($req){
		
		//$employee = $this->baseService->getElement('Employee',$this->getCurrentEmployeeId());
		
		
		$employeeLeave = new EmployeeLeave();
		$employeeLeave->Load("id = ?",array($req->id));
		if($employeeLeave->id != $req->id){
			return new IceResponse(IceResponse::ERROR,"Leave not found");
		}
		
		if($this->user->user_level != 'Admin'){
			return new IceResponse(IceResponse::ERROR,"Only an admin can do this");	
		}
		
		$oldLeaveStatus = $employeeLeave->status;
		$employeeLeave->status = $req->status;
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
		
		$employee = $this->getEmployeeById($employeeLeave->employee);
		
		if($oldLeaveStatus != $employeeLeave->status){
			$this->sendLeaveStatusChangedEmail($employee, $employeeLeave);
		}
		
		$this->baseService->audit(IceConstants::AUDIT_ACTION, "Leave status changed \ from:".$oldLeaveStatus."\ to:".$employeeLeave->status." \ id:".$employeeLeave->id);
		
		$currentEmpId = $this->getCurrentEmployeeId();
		
		if(!empty($currentEmpId)){
			$employee = $this->baseService->getElement('Employee',$currentEmpId);
			
			if($employeeLeave->status != "Pending"){
				$notificationMsg = "Your leave has been $employeeLeave->status by ".$employee->first_name." ".$employee->last_name;
				if(!empty($req->reason)){
					$notificationMsg.=" (Note:".$req->reason.")";
				}
			}
			
			$this->baseService->notificationManager->addNotification($employeeLeave->employee,$notificationMsg,'{"type":"url","url":"g=modules&n=leaves&m=module_Leaves#tabEmployeeLeaveApproved"}',IceConstants::NOTIFICATION_LEAVE);
			
		}
		
		
		return new IceResponse(IceResponse::SUCCESS,"");
	}
	
	public function sendLeaveStatusChangedEmail($employee, $leave){
	
		$emp = $this->getEmployeeById($leave->employee);
	
		$params = array();
		$params['name'] = $emp->first_name." ".$emp->last_name;
		$params['startdate'] = $leave->date_start;
		$params['enddate'] = $leave->date_end;
		$params['status'] = $leave->status;
	
		$user = $this->subActionManager->getUserFromEmployeeId($employee->id);
		
		if(!empty($user)){
			$email = file_get_contents(APP_BASE_PATH."modules/leaves/emailTemplates/leaveStatusChanged.html");
			if(!empty($this->emailSender)){
				$this->emailSender->sendEmail("Leave Application ".$leave->status,$user->email,$email,$params);
			}	
		}
		
		
	}
	
	private function getEmployeeById($id){
		$sup = new Employee();
		$sup->Load("id = ?",array($id));
		if($sup->id != $id){
			error_log("Employee not found");
			return null;
		}
	
		return $sup;
	}
	

}