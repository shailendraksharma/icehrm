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

class LeavecalActionManager extends SubActionManager{
	
	public function getLeavesForMeAndSubordinates($req){
		$map = json_decode('{"employee":["Employee","id","first_name+last_name"],"leave_type":["LeaveType","id","name"]}');
		
		$employee = $this->baseService->getElement('Employee',$this->getCurrentEmployeeId(),null,true);
		
		
		$employeeLeave = new EmployeeLeave();
		$startDate = date("Y-m-d H:i:s",$req->start);
		$endDate = date("Y-m-d H:i:s",$req->end);
		
		$list = $employeeLeave->Find("status in ('Approved','Pending') and ((date_start >= ? and date_start <= ? ) or (date_end >= ? and date_end <= ?))",array($startDate,$endDate,$startDate,$endDate));
		if(!$list){
			error_log($employeeLeave->ErrorMsg());
		}
		if(!empty($map)){
			$list = $this->baseService->populateMapping($list,$map);
		}
		
		$data = array();
		foreach($list as $leave){
			$data[] = $this->leaveToEvent($leave);
		}
		
		$holiday = new HoliDay();
		$holidays = $holiday->Find("1=1",array());
		
		foreach($holidays as $holiday){
			$data[] = $this->holidayToEvent($holiday);
		}
		
		echo json_encode($data);
		exit();
	}
	
	
	public function leaveToEvent($leave){
		$event = array();
		$event['id'] = $leave->id;
		$event['title'] = $leave->employee." (".$leave->leave_type.")";
		$event['start'] = $leave->date_start;
		$event['end'] = $leave->date_end;
		$eventBackgroundColor = "";
		if($leave->status == "Pending"){
			$eventBackgroundColor = "#cc9900";
		}else{
			$eventBackgroundColor = "#336633";
		}
		$event['color'] = $eventBackgroundColor;
		$event['backgroundColor'] = $eventBackgroundColor;
		$event['textColor'] = "#FFF";
		
		return $event;
	}
	
	public function holidayToEvent($holiday){
		$event = array();
		$event['id'] = "hd_".$holiday->id;
		if($holiday->status == "Full Day"){
			$event['title'] = $holiday->name;
		}else{
			$event['title'] = $holiday->name." (".$holiday->status.")";
		}

		if(!empty($holiday->country)){
			$country = new Country();
			$country->Load("id = ?",array($holiday->country));
			$event['title'] .=" / ".$country->name." only";
		}
		
		$event['start'] = $holiday->dateh;
		$event['end'] = $holiday->dateh;

		$eventBackgroundColor = "#3c8dbc";
		
		$event['color'] = $eventBackgroundColor;
		$event['backgroundColor'] = $eventBackgroundColor;
		$event['textColor'] = "#FFF";
	
		return $event;
	}

}