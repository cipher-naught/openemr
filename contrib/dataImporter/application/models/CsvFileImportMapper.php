<?php
// Copyright (C) 2012 Chris Paulus <coding@cipher-naught.com>
// Sponsored by David Eschelbacher, MD
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

require_once (dirname(__FILE__) ."/../../../../library/sql.inc");
require_once "/../../../../library/parsecsv.lib.php";

class Application_Model_CsvFileImportMapper
{
	private $uploadLocation = "" ;
	protected $systemLocale;
	
	private $_listOfColumns; //Columns of the table
	
	/**
	 * Constructor
	 * @throws Exception
	 */
	public function __construct() {
		if(empty($this->uploadLocation)) {
			$this->uploadLocation = $GLOBALS['OE_SITE_DIR'] . "/uploadCache";
			if(!file_exists($this->uploadLocation)) {
				try {
					mkdir($this->uploadLocation, 0777);
				}
				catch(Exception $e) {
					//Add any error handling here.
					throw $e;
				}
			}
		}
	}
	/**
	 * Gets Upload location (folder/directory)
	 * @return string
	 */
	public function getUploadLocation() {
		
		return $this->uploadLocation;
		
	}	
	
	/**
	 * Gets the full path for a file name and checks for directory transversal.
	 * @param string $fileName file name portion of the file 
	 * @return boolean|string returns false for security exception 
	 */
	public function uploadLocationFromFile($fileName) {
		if (strpos($fileName, '../') !== false || strpos($fileName, "..\\") !== false || strpos($fileName, '/..') !== false || strpos($fileName, '\..') !== false)
		{
			//security exception.
			return false;
			
		}
		else
		{
			return $this->uploadLocation."/".$fileName;
		}
	}
	/**
	 * Moves the temporary file
	 * @param string $tmpName Orginal file
	 * @param string $newFileName New filename (without path)
	 */
	public function moveTemp($tmpName, $newFileName) {
		
		move_uploaded_file($tmpName,  $this->uploadLocation ."/".$newFileName);
	}
	
	private function setupParser(&$csv, $fieldDelimiter, $txtQualifier, $encoding, $firstRowColumnNames = false) {
		if($firstRowColumnNames == NULL || $firstRowColumnNames == 0 || $firstRowColumnNames == FALSE ) { //Default is true.
			$csv->heading = false;
		}
		if($encoding != 'UTF-8') {
			$csv->encoding($encoding,'UTF-8');
		}
		$csv->delimiter = $fieldDelimiter;
		$csv->enclosure = $txtQualifier;
		
	}
	
	//Update
	/**
	 * Generates an array(s) of data specified in the file.
	 * @param filepointer $fileNamePointer
	 * @param string $tableName
	 * @param string $fieldDelimiter single character
	 * @param string $txtQualifier single character
	 * @param boolean $firstRowColumnNames
	 * @param int $numberOfRows
	 * @return multitype:multitype: either a single array if no columns specified, or 2 arrays if columns are specified
	 */
	public function generateTableArray($fileName, $fieldDelimiter, $txtQualifier, $firstRowColumnNames = false, $numberOfRows =NULL, $encoding='UTF-8') {
		$output = array();
			
		//If first, then do the number of rows		
		//$fp = fopen($fileName, "r") or die(); //TODOCMP: Add die erorr
		//$length = 4096; /// have to be optional length ...
		
		$csv = new parseCSV();
		$this->setupParser($csv, $fieldDelimiter, $txtQualifier, $encoding, $firstRowColumnNames);
		
		if(isset($numberOfRows)) {
			$csv->limit = $numberOfRows;
		}
		
		$csv->parse($fileName);
		foreach($csv->data as $key => $row) {
			array_push($output, $row);
		}
		if($firstRowColumnNames) {
			return array($output,$csv->titles);
		}
		else {
			return array($output);
		}
	}
	
	public function dateHelper($value, $dateFormat) {
		//Add logic for two digit date
		//0 => dd
		//1 => mm
		//2 => yy
		//Check for two digit year and append "19"
		$yearFormat = "YYYY";
		$yearPrefix = "";
		if(strlen($value)<9) {
			$yearFormat = "YY";
			$yearPrefix = "19";
		}
		if($dateFormat != "none") {
			//$arr = explode(substr($dateFormat,0,1),$value);
			$arr = preg_split("[\D]",$value);
			$arr2 = explode(",",$dateFormat);
			
			$lcll=  $yearPrefix.$arr[intval($arr2[2])]."-".$arr[intval($arr2[1])]."-".$arr[intval($arr2[0])];
			return $lcll;
		}
		else {
			return $value;
		}
	}
	
	/**
	 * Process the import of the file given the following parameters.
	 * @param filepointer $filePointer
	 * @param string $tableName
	 * @param array $columnRules
	 * @param string $txtEncoding
	 * @param string $fieldDelimit
	 * @param string $txtQualifier
	 * @param boolean $firstRowIsColumnHeaders
	 * @return boolean success or failure
	 */
	public function importFile($fileName, $tableName, $columnRules,$txtEncoding, $fieldDelimit, 
			$txtQualifier, $firstRowIsColumnHeaders, $dateFormat) {
				
		$_db = $GLOBALS['adodb']['db'];
		$_db->StartTrans();
		$counter = 0; // to omission first row if it is table headers, 0 => Skip first row
		$success = false;
		
		$successObject = array("success" => false,
							    "rolledback" => false,
				                "successfulTransactions" => 0,
				                 "failedTransactions" => 0);
		$csv = new parseCSV();
		$this->setupParser($csv, $fieldDelimit, $txtQualifier, $txtEncoding, $firstRowIsColumnHeaders);
		$result = sqlQuery("SELECT MAX(id)+1 AS id FROM ".$tableName);
		$idVal = -1;
		if($result != null) {
			//There is an ID field
			if($result['id'] == null) { //first insert
				$idVal = 1;
			}
			else {
				$idVal = intval($result['id']);
			}
		}
		if($tableName == "patient_data") {
			$result = sqlQuery("SELECT MAX(pid)+1 AS pid FROM ".$tableName);
			$pidVal = -1;
			if($result != null) {
				//There is an ID field
				if($result['pid'] == null) { //first insert
					$pidVal = 1;
				}
				else {
					$pidVal = intval($result['pid']);
				}
			}
		}
		$csv->parse($fileName);
		foreach($csv->data as $key => $row) {
			$outputLine1 = "insert into $tableName (";
			$outputLine2 = "\nValues (";
			if($idVal != -1) {
				$outputLine1 .= "id, ";
				$outputLine2 .= $idVal.", ";
			}
			if($tableName == "patient_data") {
				$outputLine1 .= "pid, ";
				$outputLine2 .= $pidVal.", ";
			}
			$c= 0;
			$arrayArgs = array();
			$valuesToInsert = false;
			$columnsToInsert = false;
			foreach ($row as $value) {
				//See if the row has any values.
				if(!$valuesToInsert && $value != NULL) {
					$valuesToInsert = true;
				}
				if($columnRules[$c] != Null) {
					$columnsToInsert = true;
					
					if(strpos($columnRules[$c],"*") === FALSE) {
						$outputLine1 .=  '`'.str_replace('`','',$columnRules[$c]) ."`, ";
						array_push($arrayArgs, $_db->qstr($value));
					}
					else { //Format Date
						$outputLine1 .=  "`".str_replace('`','',substr($columnRules[$c],0,-1)) ."`, ";
						array_push($arrayArgs, $this->dateHelper($value,$dateFormat));
					}
					$outputLine2 .= "?, ";
				}
				$c++;
			}
			if($columnsToInsert && $valuesToInsert) {
				$successObject["success"] = $_db->Execute(rtrim($outputLine1,", "). ")".rtrim($outputLine2,", ").");\n\n",$arrayArgs);
				if($pidVal != -1) $pidVal++;
				if($idVal != -1) $idVal++;
				if(!$successObject["success"]) {
					$_db->FailTrans();
					$successObject["failedTransactions"]++;
					break;
				}
				else {
					$successObject["successfulTransactions"]++;
				}
			}
			
		}
		
		$successObject["success"] =$_db->CompleteTrans();
		//Rollback failed if complete transaction failed and there was a failed transaction.
		$successObject["rolledback"] =  !$successObject["success"] &&  $_db->HasFailedTrans();
		return  $successObject;
		 
		
		
	}
}

