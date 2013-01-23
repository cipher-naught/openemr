<?php

class DatabaseQueryBuilder
{
	
	static public function generateSQL($tableName, $columns, $values, $type, $criteria = NULL) {
		$retString = "";
		if($type === NULL || (!isset($criteria) && 
			(count($columns) != count($values)))) {

			//raise exception.
		}
		else {
			if($type == "UPDATE") {
				$retString = "UPDATE `".$tableName."`\nSET ";
				for($i = 0; $i < count($columns); $i++) {
					$retString .= "`$columns[$i]` = ?, ";
				}
				$retString = rtrim($retString,", ") . "\n WHERE $criteria";
			}
			elseif($type == "INSERT") {
				$retString = "INSERT INTO ".$tableName."\n(";
				$line2 = "\nVALUES (";
				for($i = 0; $i < count($columns); $i++) {
					$retString .= "`$columns[$i]`, ";
					$line2 .= "?, "; 
				}
				$retString = rtrim($retString,", ") .")". rtrim($line2,", ").")";
			}
			else {
				//raise exception.
			}

		}
		return $retString;
	}
}