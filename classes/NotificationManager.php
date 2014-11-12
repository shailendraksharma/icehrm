<?php
class NotificationManager{
	
	var $baseService;
	
	public function setBaseService($baseService){
		$this->baseService = $baseService;
	}
	
	public function addNotification($toUser, $message, $action, $type){
		
		$userEmp = new User();
		$userEmp->load("employee = ?",array($toUser));
		
		if(!empty($userEmp->employee) && $userEmp->employee == $toUser){
			$toUser = $userEmp->id;
		}else{
			return;
		}
		
		$noti = new Notification();
		$user = $this->baseService->getCurrentUser();
		$noti->fromUser = $user->id;
		$noti->fromEmployee = $user->employee;
		$noti->toUser = $toUser;
		$noti->message = $message;
		
		if(!empty($noti->fromEmployee)){
			$employee = $this->baseService->getElement('Employee',$noti->fromEmployee,null,true);
			if(!empty($employee)){
				$fs = new FileService();
				$employee = $fs->updateEmployeeImage($employee);
				$noti->image = $employee->image;
			}
		}
		
		if(empty($noti->image)){
			$noti->image = BASE_URL."images/user_male.png";	
		}

		$noti->action = $action;
		$noti->type = $type;
		$noti->time = date('Y-m-d H:i:s');
		$noti->status = 'Unread';
		
		$ok = $noti->Save();
		if(!$ok){
			error_log("Error adding notification: ".$noti->ErrorMsg());
		}
	}
	
	public function clearNotifications($userId){
		$notification = new Notification();
		
		$listUnread = $notification->Find("toUser = ? and status = ?",array($userId,'Unread'));
		
		foreach($listUnread as $not){
			$not->status = "Read";
			$not->Save();	
		}
	}
	
	public function getLatestNotificationsAndCounts($userId){
		$notification = new Notification();	
		
		$listUnread = $notification->Find("toUser = ? and status = ?",array($userId,'Unread'));
		$unreadCount = count($listUnread);
		
		$limit = ($unreadCount < 20)?20:$unreadCount;
		
		$list = $notification->Find("toUser = ? order by time desc limit ?",array($userId,$limit));
		
		return array($unreadCount, $list);

	}
	
}