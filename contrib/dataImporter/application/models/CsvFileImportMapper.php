<?php

require_once (dirname(__FILE__) ."/../../../../library/sql.inc");

class Application_Model_CsvFileImportMapper
{
	
	
	private $returnSQL = false; //Should the SQL be run directly or stored as an array.
	private $uploadLocation = "" ;
	protected $systemLocale;
	
	//"C:/dev/OpenEMR/Source/MyOpenEMR/desktopOpenemr/openemr/sites/default/documents/";
	private $_listOfColumns; //Columns of the table
	
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
	public function getUploadLocation() {
		return $this->uploadLocation;
		
	}	
	
	public function uploadLocationFromFile($fileName) {
		return $this->uploadLocation."/".$fileName;
	}
	//Add getters and setters
	//public function
	/**
	 * Used to move temporary file to a more permanent location. 
	 * @param string $fileNameFull complete path of the orginal file
	 * @param unknown_type $fileName basic name of the file
	 * @param string $randVal value to prepend to file being created.
	 * @return string full path to the file being created
	 */
	public function moveTempAndEncode($fileNameFull, $txtEncoding) {
		
		//$fileLocation = $this->uploadLocation ."/".$fileName;
		if($txtEncoding != "utf8") {
			return $this->convertEncoding($fileNameFull, tmpfile(),$txtEncoding );
		}
		else { 
			return open($fileNameFull,"r") or die();
		}
	}
	
	public function moveTemp($tmpName, $newFileName) {
		
		move_uploaded_file($tmpName,  $this->uploadLocation ."/".$newFileName);
	}
	
	public function convertEncoding($fromFile, $toFile, $txtEncoding) {
		$buf = mb_convert_encoding(file_get_contents($fromFile), "utf8", $txtEncoding);
		
		fwrite($toFile, $buf);
		rewind($toFile);
		return $toFile;
	}

	
	public function generateTableArray($fileNamePointer, $tableName, $fieldDelimiter, $txtQualifier, $firstRowColumnNames = false, $numberOfRows =NULL) {
		$output = array();
			
		//If first, then do the number of rows		
		//$fp = fopen($fileName, "r") or die(); //TODOCMP: Add die erorr
		//$length = 4096; /// have to be optional length ...
		
		$lineCounter = 0; // to omission first row if it is table headers, 0 => Skip first row
		$withError = false;
		$prevRowCount = 0;
		
		
		$rowCount = 0;		
		while(!feof($fileNamePointer)) {
				$rowArray = array();
				//$output = $output ."<tr>";
				if($rowCount>$numberOfRows) {
					break;
				}
				if(($buffer = fgetcsv($fileNamePointer, 4096, $fieldDelimiter)) !== false) {
					
					//TODOCMP: fix
					//if($txtEncoding != "UTF-8") {
					//	$buffer = mb_convert_encoding($buffer, "UTF-8",$txtEncoding);
					//}
					
					for($colcount=0; $colcount< count($buffer); $colcount++) {
						// For example insert in 3 column (e.g. id, name, email )
							
						//$columns = array_push($columns, $key);
						if($firstRowColumnNames && $lineCounter == 0) {
							isset($columnMatch) ? : $columnMatch = array();
							array_push($columnMatch,utf8_encode($buffer[$colcount])); 
							
						}
						else {
							//$output = $output . "<td>" .$value ."</td>";
							array_push($rowArray,$buffer[$colcount]);
						}
					}
					//Note: Rowcount will be one more than the numbers of columns
					if($lineCounter == 0) { //Set $prevRowCount;
						$prevRowCount = $colcount; 
					}	
					if($prevRowCount != $colcount) {
						$withError = true;
						break;	
					}
					if(!($lineCounter == 0 && $firstRowColumnNames))  {//If not header row.
						array_push($output, $rowArray);		
						$rowCount++;
					}

					$lineCounter++;
				}
			
		}
		fclose($fileNamePointer);
		

		if($firstRowColumnNames) {
			return array($output,$columnMatch);
		}
		else {
			return array($output);
		}
	}
	
	/**
	 * 
	 * @param string $fileName Full path location of file to be parsed
	 * @param string $tableName Table to be inserted into
	 * @param array $columnRules array of columns orders to be used 
	 * @param string $txtEncoding encoding of file
	 * @param string $fieldDelimit
	 * @param string $txtQualifier
	 * @param bool $firstRowIsColumnHeaders boolean of whether the first row is column names, if true then skip first row.
	 * @return string_array an array of SQL insert statements
	 */
	public function importFile($fileName, $tableName, $columnRules,$txtEncoding, $fieldDelimit, $txtQualifier, $firstRowIsColumnHeaders) {
		
		

		
		
		
		$fp = fopen($fileName, "r") or die("Unable to read uploaded file."); //Add die erorr
		
		$_db = $GLOBALS['adodb']['db'];
		$_db->StartTrans();
		$counter = 0; // to omission first row if it is table headers, 0 => Skip first row
		
		while(($buffer = fgets($fp)) !== false) {
			if($txtEncoding != "UTF-8") {
				$buffer = mb_convert_encoding($buffer, "UTF8",$txtEncoding);
			}
			//Some times csv files do not include text qualifiers like they should
			//We automatically insert them if they are missing.
			//if(!strpos($buffer,$txtQualifier) ) { 
			//	$buffer = str_replace(",","\",\"",$buffer);
			//	$buffer = "\"$buffer\"";
			//}
			//If we fail to get a line, then just skip it.
			if( !$line = str_getcsv($buffer, $fieldDelimit, $txtQualifier) 
					&& ($counter ==0 && $firstRowIsColumnHeaders)) { //skip first row if it contains column information.
				continue;
			}
			$columns = NULL;
			$values = NULL;
			$rowcount = 0;
			$outputLine1 = "insert into $tableName (";
			$outputLine2 = "\nValues (";
			for($c =0; $c< count($line); $c++) {
				// For example insert in 3 column (e.g. id, name, email )
				 
				//$columns = array_push($columns, $key);
				if($columnRules[$c] != NULL) {
					//$values = array_push($values, $value);
					$outputLine1 .= mysqli_real_escape_string($columnRules[$c]) .", ";
					$outputLine2 .= "\"".mysqli_real_escape_string($line[$c]) ."\", ";
				}
			}
			//Character clean up and add to system			
			$_db->sqlInsert(rtrim($outputLine1,", "). ")".rtrim($outputLine2,", ").");\n\n");
			$counter++;
		}
		fclose($fp);
		return $_db->CompleteTrans();
	}
}

