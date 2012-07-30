<?php
require_once (dirname(__FILE__) ."/../../../../library/sql.inc");

class Application_Model_DbMapper
{
	protected $_db_tables ;
	protected $_db_table = "";
	protected $_db;


	public function __construct()
	{
		//Instantiate the Table Data Gateway for the User table
		//$this->_db_tables = "";
		//$db = Zend_Db_Table::getDefaultAdapter();

		//$params = array('host' => 'localhost:3306', 'username' => 'root',

		$this->_db = $GLOBALS['adodb']['db'];

		//$this->_db->
		//$stmt =  $GLOBALS['adodb']['db']->Execute('SHOW TABLES');
		$resultSet =  $this->_db->Execute('SHOW TABLES');
		//if (is_array($stmt)) {

		if (is_object($resultSet)) {
			while($rowValue = $resultSet->FetchRow()) {
				//TODOCMP: Check fetch row type

				foreach ($rowValue as $key => $value) {
					$this->_db_tables[] = $value;
				}
				//$this->_db_tables[] = $value[1];
			}
			//while ($row = $resultSet->FetchRow()) {
			//$lcl = $row[0];
			//$this->_db_tables[] = $row[0];
			//}
		}

	}
	public function __get($name)
	{
		$method = '__get' . $name;
		if (('mapper' == $name) || !method_exists($this, $method)) {
			throw new Exception('Invalid DBMapper property to get ('.$name . ')');
		}
		return $this->$method();

	}
	
	public function __set($name, $value)
	{
		$method = 'set' . $name;
		if (('mapper' == $name) || !method_exists($this, $method)) {
			throw new Exception('Invalid DBMapper property to set ('.$name . ')');
		}
		$this->$method($value);
	}
	public function __getTables()
	{
		return $this->_db_tables;

	}

	public function __setStuff($value)
	{
		$this->_db_table = "";
	}
	public function __getStuff()
	{
		//return $this->_db_table;
		return "asdfasdf";
	}

	//TODOCmp: Probably can't pass a value into a get.
	public function columnList($table)
	{
		//DESCRIBE business;

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


				//$this->_db_tables[] = $value[1];
			}
			//while ($row = $resultSet->FetchRow()) {
			//$lcl = $row[0];
			//$this->_db_tables[] = $row[0];
			//}
		}


		//$stmt =  $db->query('DESCRIBE '.$table);
		//
		//$stmt->setFetchMode(Zend_Db::FETCH_NUM);
		//$rows = $stmt->fetchAll();
		//
		////$outputArray = NULL;
		//$outputArray = array();
		//foreach($rows as $data) {
		//	$lclVal = $data[0];
		//	array_push($outputArray, $lclVal);
		//}
		if(isset($outputArray)) {
			return $outputArray;
		}
		else
		{
			//TODO: Raise Error
			throw new Exception('DBMapper Problem, table not found');
			return "bad";
		}

			
	}


}

