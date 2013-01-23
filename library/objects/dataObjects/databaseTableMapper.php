<?php

require_once (dirname(__FILE__) ."/mapper.php");
require_once (dirname(__FILE__) ."/../../sql.inc");


class DatabaseTableMapper extends MapperObject
{
	protected $_db;
	
	protected $tableName;
	
	protected $suppressColumns;
	
	//concreate examples would have a certain value for the table used.
	public function __construct() {
		//Do setup
		$this->_db = $GLOBALS['adodb']['db'];
		$this->tableName = "ee";
	}
	
	
	public function getFieldList() {
		$lclVal = $this->_db->MetaColumns($this->tableName);
		$retVal = array();
		foreach($lclVal as $column) {
			if($column->type == "date") {
				$retVal[] = $column->name."*";
			}
			else {
				$retVal[] = $column->name;
			}
		}
		return $retVal;
		
	}
	
}