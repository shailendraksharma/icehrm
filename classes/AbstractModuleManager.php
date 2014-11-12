<?php
abstract class AbstractModuleManager{
	
	private $fileFieldMappings = array();
	private $userClasses = array();
	private $errorMappings = array();
	private $modelClasses = array();
	
	public abstract function initializeUserClasses();
	public abstract function initializeFieldMappings();
	public abstract function initializeDatabaseErrorMappings();
	public abstract function setupModuleClassDefinitions();
	
	public function setupFileFieldMappings(&$fileFields){
		foreach ($this->fileFieldMappings as $mapping){
			if(empty($fileFields[$mapping[0]])){
				$fileFields[$mapping[0]] = array();
			}
			
			$fileFields[$mapping[0]][$mapping[1]] = $mapping[2];
		}	
	}
	
	public function setupUserClasses(&$userTables){
		foreach($this->userClasses as $className){
			if(!in_array($className, $userTables)){
				$userTables[] = $className;
			}	
		}
			
	}
	
	public function setupErrorMappings(&$mysqlErrors){
		foreach($this->errorMappings as $name=>$desc){
			$mysqlErrors[$name] = $desc;
		}
			
	}
	
	public function getModelClasses(){
		return $this->modelClasses;
	}
	
	protected function addFileFieldMapping($className, $fieldName, $fileTableFieldName){
		$this->fileFieldMappings[] = array($className, $fieldName, $fileTableFieldName);
	}
	
	protected function addUserClass($className){
		$this->userClasses[] = $className;
	}
	
	protected function addDatabaseErrorMapping($error, $description){
		$this->errorMappings[$error] = $description;
	}
	
	protected function addModelClass($className){
		$this->modelClasses[] = $className;
	}
}