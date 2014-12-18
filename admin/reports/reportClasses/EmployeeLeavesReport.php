<?php
if(!class_exists('ReportBuilder')){
	include_once MODULE_PATH.'/reportClasses/ReportBuilder.php';
}
class EmployeeLeavesReport extends ReportBuilder{
	
	public function getMainQuery(){
		$query = "SELECT 
(SELECT concat(`first_name`,' ',`middle_name`,' ', `last_name`) from Employees where id = employee) as 'Employee',
(SELECT name from LeaveTypes where id = leave_type) as 'Leave Type',
(SELECT name from LeavePeriods where id = leave_type) as 'Leave Type',
date_start as 'Start Date',
date_end as 'End Date',
details as 'Reason',
status as 'Leave Status',
(select count(*) from EmployeeLeaveDays d where d.employee_leave = lv.id and leave_type = 'Full Day') as 'Full Day Count',
(select count(*) from EmployeeLeaveDays d where d.employee_leave = lv.id and leave_type = 'Half Day - Morning') as 'Half Day (Morning) Count',
(select count(*) from EmployeeLeaveDays d where d.employee_leave = lv.id and leave_type = 'Half Day - Afternoon') as 'Half Day (Afternoon) Count'
from EmployeeLeaves lv";	
		
		return $query;

	}
	
	public function getWhereQuery($request){
		if(($request['employee'] != "NULL" && !empty($request['employee'])) && ($request['status'] != "NULL" && !empty($request['status']))){
			$query = "where employee = ? and date_start >= ? and date_end <= ? and status = ?;";
			$params = array(
					$request['employee'],	
					$request['date_start'],	
					$request['date_end'],	
					$request['status']
			);
		}else if(($request['employee'] != "NULL" && !empty($request['employee']))){
			$query = "where employee = ? and date_start >= ? and date_end <= ?;";
			$params = array(
					$request['employee'],
					$request['date_start'],
					$request['date_end']
			);
		}else if(($request['status'] != "NULL" && !empty($request['status']))){
			$query = "where status = ? and date_start >= ? and date_end <= ?;";
			$params = array(
					$request['status'],
					$request['date_start'],
					$request['date_end']
			);
		}else{
			$query = "where date_start >= ? and date_end <= ?;";
			$params = array(
					$request['date_start'],
					$request['date_end']
			);
		}
		
		return array($query, $params);
	}
}