<?php
class EmploymentStatus extends ICEHRM_Record {
	var $_table = 'EmploymentStatus';

	public function getAdminAccess(){
		return array("get","element","save","delete");
	}

	public function getUserAccess(){
		return array();
	}
}

EmploymentStatus::SetDatabaseAdapter($dbLocal);
