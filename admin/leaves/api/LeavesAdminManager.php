<?php
if (!class_exists('LeavesAdminManager')) {
	class LeavesAdminManager extends AbstractModuleManager{
		
		public function initializeUserClasses(){
			
		}
		
		public function initializeFieldMappings(){
			
		}
		
		public function initializeDatabaseErrorMappings(){

		}
		
		public function setupModuleClassDefinitions(){
			
			$this->addModelClass('LeaveType');
			$this->addModelClass('LeavePeriod');
			$this->addModelClass('WorkDay');
			$this->addModelClass('HoliDay');
			$this->addModelClass('LeaveRule');
			
		}
		
	}
}


if (!class_exists('LeaveType')) {
	class LeaveType extends ICEHRM_Record {
		var $_table = 'LeaveTypes';

		public function getAdminAccess(){
			return array("get","element","save","delete");
		}


		public function getUserAccess(){
			return array();
		}

	}
}
	
if (!class_exists('LeavePeriod')) {
	class LeavePeriod extends ICEHRM_Record {
		var $_table = 'LeavePeriods';

		public function getAdminAccess(){
			return array("get","element","save","delete");
		}

		public function getUserAccess(){
			return array();
		}

		public function validateSave($obj){
			$leavePeriod = new LeavePeriod();
			$leavePeriods = $leavePeriod->Find("1=1");

			if(strtotime($obj->date_end) <= strtotime($obj->date_start)){
				return new IceResponse(IceResponse::ERROR,"Start date should be less than end date");
			}

			foreach($leavePeriods as $lp){
				if(!empty($obj->id) && $obj->id == $lp->id){
					continue;
				}

				if(strtotime($lp->date_end) >= strtotime($obj->date_end) && strtotime($lp->date_start) <= strtotime($obj->date_end)){
					//-1---0---1---0 || ---0--1---1---0
					return new IceResponse(IceResponse::ERROR,"Leave period is overlapping with an existing one");
				}else if(strtotime($lp->date_end) >= strtotime($obj->date_start) && strtotime($lp->date_start) <= strtotime($obj->date_start)){
					//---0---1---0---1 || ---0--1---1---0
					return new IceResponse(IceResponse::ERROR,"Leave period is overlapping with an existing one");
				}else if(strtotime($lp->date_end) <= strtotime($obj->date_end) && strtotime($lp->date_start) >= strtotime($obj->date_start)){
					//--1--0---0--1--
					return new IceResponse(IceResponse::ERROR,"Leave period is overlapping with an existing one");
				}
			}
			return new IceResponse(IceResponse::SUCCESS,"");
		}
	}
}
	
	
if (!class_exists('WorkDay')) {
	class WorkDay extends ICEHRM_Record {
		var $_table = 'WorkDays';
		public function getAdminAccess(){
			return array("get","element","save","delete");
		}

		public function getUserAccess(){
			return array();
		}
	}
}
	
	
if (!class_exists('HoliDay')) {
	class HoliDay extends ICEHRM_Record {
		var $_table = 'HoliDays';
		public function getAdminAccess(){
			return array("get","element","save","delete");
		}

		public function getUserAccess(){
			return array();
		}
	}
}
	
if (!class_exists('LeaveRule')) {
	class LeaveRule extends ICEHRM_Record {
		var $_table = 'LeaveRules';
		public function getAdminAccess(){
			return array("get","element","save","delete");
		}

		public function getUserAccess(){
			return array();
		}
	}
}