<?php
if (!class_exists('LeavesModulesManager')) {
	
	class LeavesModulesManager extends AbstractModuleManager{

		public function initializeUserClasses(){
			$this->addUserClass("EmployeeLeave");
		}

		public function initializeFieldMappings(){
			$this->addFileFieldMapping("EmployeeLeave", "attachment", "name");
		}

		public function initializeDatabaseErrorMappings(){
			$this->addDatabaseErrorMapping("Duplicate entry|for key 'workdays_name_country'", "You have already defined this work day for selected country");
			$this->addDatabaseErrorMapping("Duplicate entry|for key 'holidays_dateh_country'", "You have already defined this holiday for selected country");
		}

		public function setupModuleClassDefinitions(){
			
			$this->addModelClass('EmployeeLeave');
			$this->addModelClass('EmployeeLeaveDay');
			$this->addModelClass('EmployeeLeaveLog');
			
		}

	}
}

if (!class_exists('EmployeeLeave')) {

	class EmployeeLeave extends ICEHRM_Record {
		var $_table = 'EmployeeLeaves';
		public function getAdminAccess(){
			return array("get","element","save","delete");
		}

		public function getManagerAccess(){
			return array("get","element","save","delete");
		}

		public function getUserAccess(){
			return array("get");
		}

		public function getUserOnlyMeAccess(){
			return array("element","delete");
		}
	}

	class EmployeeLeaveDay extends ICEHRM_Record {
		var $_table = 'EmployeeLeaveDays';
		public function getAdminAccess(){
			return array("get","element","save","delete");
		}

		public function getManagerAccess(){
			return array("get","element","save","delete");
		}

		public function getUserAccess(){
			return array("get");
		}

		public function getUserOnlyMeAccess(){
			return array("element","delete");
		}
	}

	class EmployeeLeaveLog extends ICEHRM_Record {
		var $_table = 'EmployeeLeaveLog';
		public function getAdminAccess(){
			return array("get","element","save","delete");
		}

		public function getManagerAccess(){
			return array("get","element","save","delete");
		}

		public function getUserAccess(){
			return array("get");
		}

		public function getUserOnlyMeAccess(){
			return array("element","delete");
		}
	}

	class EmployeeLeaveEntitlement{

	}
}