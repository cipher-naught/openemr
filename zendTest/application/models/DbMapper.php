<?php

class Application_Model_DbMapper
{
	protected $_db_tables = array("test1","test2");
    protected $_db_table = "";
    
		
	
	public function __construct()
	{
		//Instantiate the Table Data Gateway for the User table
		//$this->_db_tables = "";
		$db = Zend_Db_Table::getDefaultAdapter();
		$stmt =  $db->query('SHOW TABLES');
		$stmt->setFetchMode(Zend_Db::FETCH_NUM);
		$rows = $stmt->fetchAll();
		//foreach ($data as $row) {
		  	//$this->_db_tables[] = $row[1];
		//}
		//Tables_in_test
		foreach($rows as $data) {
			$this->_db_tables[] = $data[0];
		}
		
	}
	public function __get($name)
	{
		$method = '__get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid DBMapper property');
        }
        return $this->$method();	
		
	}
	public function __set($name, $value)
	{
		$method = 'set' . $name;
		if (('mapper' == $name) || !method_exists($this, $method)) {
			throw new Exception('Invalid DBMapper property');
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
	
	public function __getColumns()
	{
		if($this->_db_table <> "")
		{
			return "$_db_table: ".$this->_db_table;
			echo "hey";
		
		}
		else 
		{
		  //TODO: Raise Error	
		  return "bad";
		}
		
			
	}
	
	
}

