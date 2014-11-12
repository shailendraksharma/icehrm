<?php
if (!class_exists('AttendanceAdminManager')) {
	
	class AttendanceAdminManager extends AbstractModuleManager{
		
		public function initializeUserClasses(){
			
		}
		
		public function initializeFieldMappings(){
			
		}
		
		public function initializeDatabaseErrorMappings(){
			
		}
		
		public function setupModuleClassDefinitions(){			
			$this->addModelClass('Attendance');
		}
		
	}
}


//Model Classes

if (!class_exists('Attendance')) {
	class Attendance extends ICEHRM_Record {
		var $_table = 'Attendance';

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
			return array("element","save","delete");
		}
	}
}