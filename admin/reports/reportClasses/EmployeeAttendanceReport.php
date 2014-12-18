<?php
if(!class_exists('ReportBuilder')){
	include_once MODULE_PATH.'/reportClasses/ReportBuilder.php';
}
class EmployeeAttendanceReport extends ReportBuilder{
	
	public function getMainQuery(){
		$query = "SELECT 
(SELECT `employee_id` from Employees where id = at.employee) as 'Employee',
(SELECT concat(`first_name`,' ',`middle_name`,' ', `last_name`) from Employees where id = at.employee) as 'Employee',
in_time as 'Time In',
out_time as 'Time Out',
note as 'Note'
FROM Attendance at";	
		
		return $query;

	}
	
	public function getWhereQuery($request){
		if(($request['employee'] != "NULL" && !empty($request['employee']))){
			$query = "where employee = ? and in_time >= ? and out_time <= ? order by in_time desc;";
			$params = array(
					$request['employee'],	
					$request['date_start']." 00:00:00",	
					$request['date_end']." 23:59:59",	
			);
		}else{
			$query = "where in_time >= ? and out_time <= ? order by in_time desc;";
			$params = array(
					$request['date_start']." 00:00:00",	
					$request['date_end']." 23:59:59",	
			);
		}
		
		return array($query, $params);
	}
}