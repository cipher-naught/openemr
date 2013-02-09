<?php
// Copyright (C) 2012 Chris Paulus <coding@cipher-naught.com>
// Sponsored by David Eschelbacher, MD
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

require_once (dirname(__FILE__) ."/../sql.inc");

class DataImportTable
{
	protected $tableData;
	protected $completeData; //Used to determine if all data is present.
	protected $actionIds;
	protected $columnNames; //from imported data
	protected $knownColumnHeadings; //If data has column headings.
	protected $targetTable;
	protected $_db;
	
	
	public function __construct() {
		
		$this->actionIds = array();
		$this->columnNames = NULL;
		$this->_db = $GLOBALS['adodb']['db'];
		
	}
	
	public function addAction($val) {
		if($val === NULL) {
			array_push($this->actionIds, -1);
		}
		elseif($val > 1) { //More than one entry.
			array_push($this->actionIds, -2);
		}
		else {
			array_push($this->actionIds, $val);
		}
	}
	
	public function convertMatchingToSelectStatement($matchingResults) {
		
		$filterResults = array();
		foreach($this->tableData as $lclVal2) {
			$whereClause = "";
			foreach($matchingResults as $key => $value) {
				if($value !== NULL) { //Skip NULL values
					$whereClause = $whereClause ." `$key` = '$lclVal2[$key]' AND";
				}
			}
			$whereClause = rtrim($whereClause, " AND");
			array_push($filterResults,$whereClause);
		}
		//Return the where clause
		return $filterResults;
	}
	
	
	public function getCriteriaForMatch($patternMatchingOptions) {
		$filterResults = array();
		if(isset($patternMatchingOptions) && in_array($patternMatchingOptions, true) || $this->knownColumnHeadings) { //atleast one is true, and we know the column headings.
			//look at each and find out if it is 
			foreach(array_keys($patternMatchingOptions, true) as $columnName) {//return array of all keys with true.
				
				$val = array_search($columnName, $this->columnNames);  //Probably need to make case-insentitive.
				if(!($val ===  FALSE)) {
					$filterResults[$columnName] = $val;
				}
				
			}
			return $filterResults;
		}
		else {
			return NULL; //No matched values found. 
		}
	}
	

	public function getActionIds() {
		if(isset($this->actionIds) && is_array($this->actionIds) && count($this->actionIds) > 0) {
			return $this->actionIds ;
		}
		else {
			return NULL;
		}
	}
	public function setActionIds($val) {
		$this->actionIds = $val;
	}
	
	public function containsColumns() {
		return $this->columnNames == NULL ? false : true; 
	}
	
	public function setTargetTable($val) {
		$this->targetTable = $val;
	}
	public function getTargetTable() {
		return $this->targetTable;
	}
	
	//Boolean if we know headers.
	public function setKnownColumnHeadings($val) {
		$this->knownColumnHeadings = $val;
	}
	
	public function getKnownColumnHeadings() {
		return $this->knownColumnHeadings;
	}
	
	//Set columnnames.
	public function setColumns($val) {
		$this->columnNames = $val;
	}
	public function getColumns() {
		return $this->columnNames;
	}
	public function setData($val) {
		$this->tableData = $val;
	}
	public function getData() {
		return $this->tableData;
	}
	
	public function getDatabaseColumnListWithDateMarker($table, $dateMarker = NULL)
	{
		$lclVal = $this->_db->MetaColumns($table);
		$retVal = array();
		foreach($lclVal as $column) {
			if($column->type == "date" && isset($dateMarker)) {
				$retVal[] = $column->name.$dateMarker;
			}
			else {
				$retVal[] = $column->name;
			}
		}
		return $retVal;
	}
	
	public function getDatabaseColumnList($table, $withType = NULL) {
		$lclVal = $this->_db->MetaColumns($table);
		$retVal = array();
		foreach($lclVal as $column) {
			if(isset($withType) && $withType ) {
				$retVal[strtolower($column->name)] = $column->type;
			}
			else {
				$retVal[] = $column->name;
			}
		}
		return $retVal;		
	}

}
