<?php
if(!class_exists('ReportBuilder')){
	include_once MODULE_PATH.'/reportClasses/ReportBuilder.php';
}
class EmployeeTimesheetReport extends ReportBuilder{
	
	public function getMainQuery(){
		$query = "SELECT 
(SELECT concat(`first_name`,' ',`middle_name`,' ', `last_name`) from Employees where id = te.employee) as 'Employee',
(SELECT name from Projects where id = te.project) as 'Project',
details as 'Details',
date_start as 'Start Time',
date_end as 'End Time',
SEC_TO_TIME(TIMESTAMPDIFF(SECOND,te.date_start,te.date_end)) as 'Duration'
FROM EmployeeTimeEntry te";	
		
		return $query;

	}
	
	public function getWhereQuery($request){
		if(($request['employee'] != "NULL" && !empty($request['employee'])) && ($request['project'] != "NULL" && !empty($request['project']))){
			$query = "where employee = ? and date_start >= ? and date_end <= ? and project = ?;";
			$params = array(
					$request['employee'],	
					$request['date_start'],	
					$request['date_end'],	
					$request['project']
			);
		}else if(($request['employee'] != "NULL" && !empty($request['employee']))){
			$query = "where employee = ? and date_start >= ? and date_end <= ?;";
			$params = array(
					$request['employee'],
					$request['date_start'],
					$request['date_end']
			);
		}else if(($request['project'] != "NULL" && !empty($request['project']))){
			$query = "where project = ? and date_start >= ? and date_end <= ?;";
			$params = array(
					$request['project'],
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