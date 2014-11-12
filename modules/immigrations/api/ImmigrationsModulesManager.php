<?php
if (!class_exists('ImmigrationsModulesManager')) {
	
	class ImmigrationsModulesManager extends AbstractModuleManager{

		public function initializeUserClasses(){
			if(defined('MODULE_TYPE') && MODULE_TYPE != 'admin'){
				$this->addUserClass("EmployeeImmigration");
			}
		}

		public function initializeFieldMappings(){
				
		}

		public function initializeDatabaseErrorMappings(){

		}

		public function setupModuleClassDefinitions(){
			
			$this->addModelClass('EmployeeImmigration');
			
		}

	}
}


if (!class_exists('EmployeeImmigration')) {
	class EmployeeImmigration extends ICEHRM_Record {
		var $_table = 'EmployeeImmigrations';

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