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

class ICEHRM_Record extends ADOdb_Active_Record{
	
	public function getAdminAccess(){
		return array("get","element","save","delete");
	}
	
	public function getManagerAccess(){
		return array("get","element");
	}
	
	public function getUserAccess(){
		return array("get","element");
	}
	
	public function getUserOnlyMeAccess(){
		return array("get","element");
	}
	
	public function getUserOnlyMeAccessField(){
		return "employee";
	}
	
	public function validateSave($obj){
		return new IceResponse(IceResponse::SUCCESS,"");
	}	
}

class Country extends ICEHRM_Record {
	var $_table = 'Country';
	
	public function getAdminAccess(){
		return array("get","element","save","delete");
	}
	
	public function getUserAccess(){
		return array();
	}
}

class Province extends ICEHRM_Record {
	var $_table = 'Province';
	
	public function getAdminAccess(){
		return array("get","element","save","delete");
	}
	
	public function getUserAccess(){
		return array();
	}
}

class CurrencyType extends ICEHRM_Record {
	var $_table = 'CurrencyTypes';
	
	public function getAdminAccess(){
		return array("get","element","save","delete");
	}
	
	public function getUserAccess(){
		return array();
	}
}

class Nationality extends ICEHRM_Record {
	var $_table = 'Nationality';
	
	public function getAdminAccess(){
		return array("get","element","save","delete");
	}
	
	public function getUserAccess(){
		return array();
	}
}

class Employee extends ICEHRM_Record {
	
	public function getAdminAccess(){
		return array("get","element","save","delete");
	}
	
	public function getManagerAccess(){
		return array("get","element","save");
	}
	
	public function getUserAccess(){
		return array("get");
	}
	
	public function getUserOnlyMeAccess(){
		return array("element","save");
	}
	
	public function getUserOnlyMeAccessField(){
		return "id";
	}
	
	var $_table = 'Employees';
}

class User extends ICEHRM_Record {
	public function getAdminAccess(){
		return array("get","element","save","delete");
	}
	
	public function getUserAccess(){
		return array();
	}
	
	
	public function validateSave($obj){
		$userTemp = new User();
		
		if(empty($obj->id)){
			$users = $userTemp->Find("email = ?",array($obj->email));
			if(count($users) > 0){
				return new IceResponse(IceResponse::ERROR,"A user with same authentication email already exist");
			}
		}else{
			$users = $userTemp->Find("email = ? and id <> ?",array($obj->email, $obj->id));
			if(count($users) > 0){
				return new IceResponse(IceResponse::ERROR,"A user with same authentication email already exist");
			}
		}
	
		return new IceResponse(IceResponse::SUCCESS,"");
	}
	
	var $_table = 'Users';
}


class File extends ICEHRM_Record {
	var $_table = 'Files';
	public function getAdminAccess(){
		return array("get","element","save","delete");
	}
	
	public function getUserAccess(){
		return array();
	}
}


class Module extends ICEHRM_Record {
	public function getAdminAccess(){
		return array("get","element","save","delete");
	}
		
	public function getUserAccess(){
		return array();
	}
	var $_table = 'Modules';
}



class Setting extends ICEHRM_Record {
	public function getAdminAccess(){
		return array("get","element","save","delete");
	}
	
	public function getUserAccess(){
		return array();
	}
	var $_table = 'Settings';
}

class Report extends ICEHRM_Record {
	public function getAdminAccess(){
		return array("get","element","save","delete");
	}
	
	public function getManagerAccess(){
		return array("get","element","save","delete");
	}
	
	public function getUserAccess(){
		return array();
	}
	var $_table = 'Reports';
}

class Audit extends ICEHRM_Record {
	var $_table = 'AuditLog';
}

class Permission extends ICEHRM_Record {
	var $_table = 'Permissions';

	public function getAdminAccess(){
		return array("get","element","save","delete");
	}

	public function getUserAccess(){
		return array();
	}

}

class DataEntryBackup extends ICEHRM_Record {
	var $_table = 'DataEntryBackups';
}


class Notification extends ICEHRM_Record {
	var $_table = 'Notifications';
}



