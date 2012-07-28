<?php
class Application_Model_DatabaseTables
{
	protected $_name;
	
	
	public function __set($name, $value);
	public function __get($name);

}

class Application_Model_GuestbookMapper
{
	public function find($name);
	public function fetchAll();
}