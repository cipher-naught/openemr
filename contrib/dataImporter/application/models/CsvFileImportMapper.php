<?php

require_once (dirname(__FILE__) ."/../../../../library/sql.inc");

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
	 * Returns a file pointer to either the file specified if UTF8, or re-encodes the file as UTF8 and a pointer to that file.
	 * Note: Orginal file is not changed. 
	 * @param string $fileNameFull
	 * @param string $txtEncoding
	 * @return filepointer
	 */
	public function moveTempAndEncode($fileNameFull, $txtEncoding) {
		
		//$fileLocation = $this->uploadLocation ."/".$fileName;
		if($txtEncoding != "utf8") {
			return $this->convertEncoding($fileNameFull, tmpfile(),$txtEncoding );
		}
		else { 
			return open($fileNameFull,"r") or die("Unable to open file in CsvFileImportMapper.php");
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
	
	/**
	 * Converts the file to a temporary location and return a file pointer to it.
	 * @param string $fromFile
	 * @param string $toFile
	 * @param string $txtEncoding
	 * @return filepointer
	 */
	public function convertEncoding($fromFile, $toFile, $txtEncoding) {
		$buf = mb_convert_encoding(file_get_contents($fromFile), "utf8", $txtEncoding);
		
		fwrite($toFile, $buf);
		rewind($toFile);
		return $toFile;
	}

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
				if(($buffer = fgetcsv($fileNamePointer, 4096, $fieldDelimiter, $txtQualifier)) !== false) {
					
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
	public function importFile($filePointer, $tableName, $columnRules,$txtEncoding, $fieldDelimit, $txtQualifier, $firstRowIsColumnHeaders) {
				
		
		
		$_db = $GLOBALS['adodb']['db'];
		$_db->StartTrans();
		$counter = 0; // to omission first row if it is table headers, 0 => Skip first row
		$success = false;
		
		while(!feof($filePointer)){
			if(($buffer = fgetcsv($filePointer, 4096, $fieldDelimit, $txtQualifier)) !== false) {
				//Need to include skip for first line.
			
				$columns = NULL;
				$values = NULL;
				
				$outputLine1 = "insert into $tableName (";
				$outputLine2 = "\nValues (";
				$dataInserted = false;
				for($c =0; $c< count($buffer); $c++) {
					// For example insert in 3 column (e.g. id, name, email )
					 
					//$columns = array_push($columns, $key);
					if($counter == 0 && $firstRowIsColumnHeaders)  {
						//Skip first row.
						continue;
					}
					if($columnRules[$c] != NULL) {
						//$values = array_push($values, $value);
						$dataInserted = true;
						$outputLine1 .=  ($columnRules[$c]) .", ";
						$outputLine2 .= "\"".($buffer[$c]) ."\", ";
					}
					
				}
				//Character clean up and add to system
				if($dataInserted) {			
					$success = $_db->Execute(rtrim($outputLine1,", "). ")".rtrim($outputLine2,", ").");\n\n");
					if(!$success) {
						$_db->FailTrans();
						break;
					}
				}
				$counter++;
			}
		}
		fclose($filePointer);
		return $_db->CompleteTrans();
		
	}
}

