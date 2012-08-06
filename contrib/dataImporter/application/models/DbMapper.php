<?php
require_once (dirname(__FILE__) ."/../../../../library/sql.inc");

class Application_Model_DbMapper
{
	protected $_db_tables ;
	protected $_db_table = "";
	protected $_db;

	/**
	 * Constructor
	 */
	public function __construct()
	{

		$this->_db = $GLOBALS['adodb']['db'];

	}
	/**
	 * Generic Getter method
	 * @param string $name of values trying to get
	 * @throws Exception
	 */
	public function __get($name)
	{
		$method = '__get' . $name;
		if (('mapper' == $name) || !method_exists($this, $method)) {
			throw new Exception('Invalid DBMapper property to get ('.$name . ')');
		}
		return $this->$method();

	}
	/**
	 * Generic Setter method
	 * @param string $name of values trying to set
	 * @throws Exception
	 */
	public function __set($name, $value)
	{
		$method = 'set' . $name;
		if (('mapper' == $name) || !method_exists($this, $method)) {
			throw new Exception('Invalid DBMapper property to set ('.$name . ')');
		}
		$this->$method($value);
	}
	
	/**
	 * returns an array of table names
	 * @returns array of table names
	 */
	public function __getTables()
	{
		$resultSet =  $this->_db->Execute('SHOW TABLES');
		
		if (is_object($resultSet)) {
			while($rowValue = $resultSet->FetchRow()) {
		
		
				foreach ($rowValue as $key => $value) {
					$this->_db_tables[] = $value;
				}
			}
		}
		return $this->_db_tables;

	}

	/**
	 * Gets a list of columns for a given table
	 * @param string $table
	 * @throws Exception if table not found.
	 * @return multitype: Array of column names, false if it fails.
	 */
	public function columnList($table)
	{
		$resultSet =  $this->_db->Execute('DESCRIBE '.$table);
		$outputArray = array();

		if (is_object($resultSet)) {
				
			while($rowValue = $resultSet->FetchRow()) {
				//TODOCMP: Check fetch row type
				foreach ($rowValue as $key => $value) {
					//$outputArray[] = $value;
					array_push($outputArray, $value);
					break;
				}
			}
		}
		if(isset($outputArray)) {
			return $outputArray;
		}
		else
		{
			throw new Exception('DBMapper Problem, table not found');
			return false; //not executed, included for all return paths to return value. 
		}
	}
}

