<?php
class ReportHandler{
	public function handleReport($request){
		if(!empty($request['id'])){
			$report = new Report();
			$report->Load("id = ?",array($request['id']));
			if($report->id."" == $request['id']){
				
				if($report->type == 'Query'){
					$where = $this->buildQueryOmmit(json_decode($report->paramOrder,true), $request);
					$query = str_replace("_where_", $where[0], $report->query);
					return $this->executeReport($report,$query,$where[1]);
				}else if($report->type == 'Class'){
					$className = $report->query;
					include MODULE_PATH.'/reportClasses/ReportBuilder.php';
					include MODULE_PATH.'/reportClasses/'.$className.".php";
					$cls = new $className();
					$data = $cls->getData($report,$request);
					return $this->generateReport($report,$data);
				}
			}else{
				return array("ERROR","Report id not found");
			}
		}		
	}
	
	
	private function executeReport($report,$query,$parameters){
		
		$report->DB()->SetFetchMode(ADODB_FETCH_ASSOC);
		$rs = $report->DB()->Execute($query,$parameters);
		if(!$rs){
			error_log($report->DB()->ErrorMsg());
			return array("ERROR","Error generating report");
		}
		
		$reportNamesFilled = false;
		$columnNames = array();
		$reportData = array();
		foreach ($rs as $rowId => $row) {
			$reportData[] = array();
			if(!$reportNamesFilled){
				foreach ($row as $name=> $value){
					$columnNames[] = $name;
					$reportData[count($reportData)-1][] = $value;
				}
				$reportNamesFilled = true;
			}else{
				foreach ($row as $name=> $value){
					$reportData[count($reportData)-1][] = $value;
				}
			}
		}
		
		
		array_unshift($reportData,$columnNames);
		
		return $this->generateReport($report, $reportData);
		
		
	}
	
	private function generateReport($report, $data){
		global $employeeCurrent;
		$fileFirst = "Report_".str_replace(" ", "_", $report->name)."-".date("Y-m-d_H-i-s");
		$file = $fileFirst.".csv";
		$fileName = CLIENT_BASE_PATH.'data/'.$file;
		$fp = fopen($fileName, 'w');
		
		foreach ($data as $fields) {
			fputcsv($fp, $fields);
		}
		
		fclose($fp);
		
		$fileObj = new File();
		$fileObj->name = $fileFirst;
		$fileObj->filename = $file;
		$fileObj->file_group = "Report";
		$ok = $fileObj->Save();
		
		if(!$ok){
			error_log($fileObj->ErrorMsg());
			return array("ERROR","Error generating report");
		}
		
		return array("SUCCESS",$file);
	}
	
	private function buildQueryOmmit($names, $params){
		$parameters = array();
		$query = "";
		foreach($names as $name){
			if($params[$name] != "NULL"){
				if($query != ""){
					$query.=" AND ";
				}
				$query.=$name." = ?";
				$parameters[] = $params[$name];
			}	
		}
		
		if($query != ""){
			$query = "where ".$query;
		}
		
		return array($query, $parameters);
	}
}