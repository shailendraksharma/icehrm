<?php
if(!defined("AWS_REGION")){define('AWS_REGION','us-east-1');}
include(APP_BASE_PATH.'lib/Mail.php');
//include(APP_BASE_PATH.'lib/aws-sdk-1.5.17/sdk.class.php');
require (APP_BASE_PATH.'lib/aws.phar');
include(APP_BASE_PATH.'adodb512/adodb.inc.php');
include(APP_BASE_PATH.'adodb512/adodb-active-record.inc.php');
$ADODB_ASSOC_CASE = 2;

//detect admin and user modules
if(defined("MODULE_PATH")){
	$tArr = explode("/", MODULE_PATH);
	if(!defined('MODULE_TYPE')){
		if(count($tArr) >= 2){
			define('MODULE_TYPE',$tArr[count($tArr)-2]);
		}else{
			define('MODULE_TYPE',"");
		}
	
	}	
}


$user = getSessionObject('user');

include (APP_BASE_PATH."classes/BaseService.php");
include (APP_BASE_PATH."classes/FileService.php");
include (APP_BASE_PATH."classes/SubActionManager.php");
include (APP_BASE_PATH."classes/AbstractInitialize.php");
include (APP_BASE_PATH."classes/AbstractModuleManager.php");
include (APP_BASE_PATH."classes/SettingsManager.php");
include (APP_BASE_PATH."classes/EmailSender.php");
include (APP_BASE_PATH."classes/ReportHandler.php");
include (APP_BASE_PATH."classes/NotificationManager.php");
include (APP_BASE_PATH."classes/S3FileSystem.php");
include (APP_BASE_PATH."classes/crypt/Aes.php");
include (APP_BASE_PATH."classes/crypt/AesCtr.php");

include (APP_BASE_PATH."model/models.inc.php");

$dbLocal = NewADOConnection(APP_CON_STR);



Country::SetDatabaseAdapter($dbLocal);
Province::SetDatabaseAdapter($dbLocal);
CurrencyType::SetDatabaseAdapter($dbLocal);
Nationality::SetDatabaseAdapter($dbLocal);
Employee::SetDatabaseAdapter($dbLocal);
User::SetDatabaseAdapter($dbLocal);
File::SetDatabaseAdapter($dbLocal);
Setting::SetDatabaseAdapter($dbLocal);
Module::SetDatabaseAdapter($dbLocal);
Report::SetDatabaseAdapter($dbLocal);
Permission::SetDatabaseAdapter($dbLocal);
DataEntryBackup::SetDatabaseAdapter($dbLocal);
Audit::SetDatabaseAdapter($dbLocal);
Notification::SetDatabaseAdapter($dbLocal);

include (APP_BASE_PATH."model/custom.models.inc.php");



$baseService = new BaseService();
$baseService->setNonDeletables("User", "id", 1);
$baseService->setCurrentUser($user);
$baseService->setDB($dbLocal);

$fileService = new FileService();
$reportHandler = new ReportHandler();
$settingsManager = new SettingsManager();
$notificationManager = new NotificationManager();

$baseService->setNotificationManager($notificationManager);
$baseService->setSettingsManager($settingsManager);

$notificationManager->setBaseService($baseService);



$noJSONRequests = $settingsManager->getSetting("System: Do not pass JSON in request");

$debugMode = $settingsManager->getSetting("System: Debug Mode");
if($debugMode == "1"){
	error_reporting(E_ALL);
	error_log("System is on debug mode");	
}

$userTables = array();
$fileFields = array();
$mysqlErrors = array();
//============ Start - Initializing Modules ==========
if(defined('CLIENT_PATH')){
$moduleManagers = array();
include 'modules.php';




foreach($moduleManagers as $moduleManagerObj){
	
	$moduleManagerObj->setupModuleClassDefinitions();
	$moduleManagerObj->initializeUserClasses();
	$moduleManagerObj->initializeFieldMappings();
	$moduleManagerObj->initializeDatabaseErrorMappings();
	
	$moduleManagerObj->setupUserClasses($userTables);
	$moduleManagerObj->setupFileFieldMappings($fileFields);
	$moduleManagerObj->setupErrorMappings($mysqlErrors);
	
	$modelClassList = $moduleManagerObj->getModelClasses();
	
	foreach($modelClassList as $modelClass){
		$modelClass::SetDatabaseAdapter($dbLocal);
	}
}
}
//============= End - Initializing Modules ============


$baseService->setUserTables($userTables);

$baseService->setSqlErrors($mysqlErrors);

include ("includes.com.php");

if(file_exists(APP_BASE_PATH.'admin/audit/api/AuditActionManager.php')){
	include APP_BASE_PATH.'admin/audit/api/AuditActionManager.php';
	$auditManager = new AuditActionManager();
	$auditManager->setBaseService($baseService);
	$auditManager->setUser($user);
	$baseService->setAuditManager($auditManager);
}

$emailEnabled = $settingsManager->getSetting("Email: Enable");
$emailMode = $settingsManager->getSetting("Email: Mode");
$emailSender = null;
if($emailEnabled == "1"){
	if($emailMode == "SMTP"){
		$emailSender = new SMTPEmailSender($settingsManager);
	}else if($emailMode == "SNS"){
		$emailSender = new SNSEmailSender($settingsManager);
	}else if($emailMode == "PHP Mailer"){
		$emailSender = new PHPMailer($settingsManager);	
	}
}

?>
