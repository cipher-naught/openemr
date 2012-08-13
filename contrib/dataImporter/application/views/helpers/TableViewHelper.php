<?php
// Copyright (C) 2012 Chris Paulus <coding@cipher-naught.com>
// Sponsored by David Eschelbacher, MD
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

class Zend_Helper_TableViewHelper extends Zend_View_Helper_Abstract
{
	public function tableViewHelper() {
		return $this;
	}
	/**
	 * Generate HTML table from data specified.
	 * @param array $tableData
	 * @param array $listOfColumns
	 * @param arry $columnChoice 
	 * @return string HTML table
	 */
	public function generateTable($tableData, $listOfColumns, $columnChoice = NULL) {
	
		$output =  "<table border=\"1\"><tr>";
		$i = 0;
		$lclColumns = array_merge(array("skip"), $listOfColumns);
		if(isset($columnChoice)) { 
			foreach($columnChoice as $value) {
				$output = $output."<td>".$this->generateSelectList($lclColumns, $i, $value)."</td>";
				$i++;
			}
			$output .= "</tr>\n";
		}
		else {
			for($i=0;$i<count($tableData[0]); $i++)
			{
				$output = $output."<td>".$this->generateSelectList($lclColumns, $i)."</td>";
			}
			$output .= "</tr>\n";
		}
		foreach($tableData as $row) {
			$output .= "<tr>";
			foreach($row as $value) {
				$output .= "<td>".htmlspecialchars($value,ENT_QUOTES)."</td>";
			}
			$output .= "</tr>";
		}
		$output .= "</table>";
		return $output;
	}
	/**
	 * Generates the select list for display at top of columns
	 * @param array $fieldArray
	 * @param int $nameID number to be attended to "name_"
	 * @param string $headerValue deafult value
	 * @return string Zend Element Select rendered
	 */
	public function generateSelectList($fieldArray, $nameID, $headerValue = NULL) {
	
		$category = new Zend_Form_Element_Select('column');
		foreach($fieldArray  as $data) {
			$category->addMultiOption($data,$data);
		}
		//$category->addMultiOption('skip','skip');
		//$category->addMultiOption('FirstName','FirstName');
		$category->removeDecorator('Errors');
		$category->removeDecorator('HtmlTag');
		$category->removeDecorator('Label');
		//$columnArry = array();
		$category->name = "name_".$nameID;
		//array_push($columnArry, $category);
		if(isset($headerValue)) {
			//Do case insensitive search
			$arr2 = array_map('strtoupper', $fieldArray);
			$key = array_search(strtoupper($headerValue), $arr2);
			if($key) {
				$category->setValue($fieldArray[$key]);
			}
			else { //$key not found, set to skip.
				$category->setValue("skip");
			}
		}
		return $category->render();
	}
	
}