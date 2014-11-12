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

class FileService{
	public function updateEmployeeImage($employee){
		$file = new File();
		$file->Load('name = ?',array('profile_image_'.$employee->id));
		
		if($file->name == 'profile_image_'.$employee->id){
			$employee->image = CLIENT_BASE_URL.'data/'.$file->filename;
		}else{
			if($employee->gender == 'Female'){
				$employee->image = BASE_URL."images/user_female.png";			
			}else{
				$employee->image = BASE_URL."images/user_male.png";	
			}
		}

		return $employee;
	}
	
	public function deleteProfileImage($employeeId){
		$file = new File();
		$file->Load('name = ?',array('profile_image_'.$employeeId));
		if($file->name == 'profile_image_'.$employeeId){
			$ok = $file->Delete();	
			if($ok){
				error_log("Delete File:".CLIENT_BASE_PATH.$file->filename);
				unlink(CLIENT_BASE_PATH.'data/'.$file->filename);		
			}else{
				return false;
			}	
		}	
		return true;
	}
	
	public function deleteFileByField($value, $field){
		$file = new File();
		$file->Load("$field = ?",array($value));
		if($file->$field == $value){
			$ok = $file->Delete();
			if($ok){
				error_log("Delete:".CLIENT_BASE_PATH.'data/'.$file->filename);
				unlink(CLIENT_BASE_PATH.'data/'.$file->filename);
			}else{
				return false;
			}
		}
		return true;
	}
}