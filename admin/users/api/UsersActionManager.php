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

class UsersActionManager extends SubActionManager{
	public function changePassword($req){
		if($this->user->user_level == 'Admin' || $this->user->id == $req->id){
			$user = $this->baseService->getElement('User',$req->id);
			if(empty($user->id)){
				return new IceResponse(IceResponse::ERROR,"Please save the user first");
			}	
			$user->password = md5($req->pwd);
			$ok = $user->Save();
			if(!$ok){
				return new IceResponse(IceResponse::ERROR,$user->ErrorMsg());		
			}
			return new IceResponse(IceResponse::SUCCESS,$user);
		}
		return new IceResponse(IceResponse::ERROR);
	}
}