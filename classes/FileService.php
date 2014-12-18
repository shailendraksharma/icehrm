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
		global $settingsManager;
		$file = new File();
		$file->Load('name = ?',array('profile_image_'.$employee->id));
		
		if($file->name == 'profile_image_'.$employee->id){
			$uploadFilesToS3 = $settingsManager->getSetting("Files: Upload Files to S3");	
			if($uploadFilesToS3 == "1"){
				$uploadFilesToS3Key = $settingsManager->getSetting("Files: Amazon S3 Key for File Upload");
				$uploadFilesToS3Secret = $settingsManager->getSetting("Files: Amazone S3 Secret for File Upload");
				$s3FileSys = new S3FileSystem($uploadFilesToS3Key, $uploadFilesToS3Secret);
				$s3WebUrl = $settingsManager->getSetting("Files: S3 Web Url");
				$fileUrl = $s3WebUrl.CLIENT_NAME."/".$file->filename;
				$fileUrl = $s3FileSys->generateExpiringURL($fileUrl);
				$employee->image = $fileUrl;
			}else{
				$employee->image = CLIENT_BASE_URL.'data/'.$file->filename;
			}
			
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
		global $settingsManager;
		error_log("Delete file by field: $field / value: $value");
		$file = new File();
		$file->Load("$field = ?",array($value));
		if($file->$field == $value){
			$ok = $file->Delete();
			if($ok){			
				$uploadFilesToS3 = $settingsManager->getSetting("Files: Upload Files to S3");
				
				if($uploadFilesToS3 == "1"){
					$uploadFilesToS3Key = $settingsManager->getSetting("Files: Amazon S3 Key for File Upload");
					$uploadFilesToS3Secret = $settingsManager->getSetting("Files: Amazone S3 Secret for File Upload");
					$s3Bucket = $settingsManager->getSetting("Files: S3 Bucket");
					
					$uploadname = CLIENT_NAME."/".$file->filename;
					error_log("Delete from S3:".$uploadname);
					
					$s3FileSys = new S3FileSystem($uploadFilesToS3Key, $uploadFilesToS3Secret);
					$res = $s3FileSys->deleteObject($s3Bucket, $uploadname);
						
				}else{
					error_log("Delete:".CLIENT_BASE_PATH.'data/'.$file->filename);
					unlink(CLIENT_BASE_PATH.'data/'.$file->filename);
				}
				
				
			}else{
				return false;
			}
		}
		return true;
	}
}