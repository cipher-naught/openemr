<?php

require_once (dirname(__FILE__) ."/databaseTableMapper.php");

class employerTableMapper extends DatabaseMapper
{
	
	protected $suppressedColumns = array("id", "person_id");
	
	
	//concreate examples would have a certain value for the table used.
	public function __construct() {
		//Do setup
		$this->_db = $GLOBALS['adodb']['db'];
		$this->tableName = "patient_data";	
		
	}
	
	//Todo: update to return 2-d value with types.
	public function getFieldList() {
		$lclVal = $this->_db->MetaColumns($this->tableName);
		$retVal = array();
		foreach($lclVal as $column) {
			if(!in_array($column->name, $this->suppressedColumns)) {			
				if($column->type == "date") {
					$retVal[] = $column->name;
				}
				else {
					$retVal[] = $column->name;
				}
			} //Column skipped.
			
		}
		return $retVal;
		
	}
	
}