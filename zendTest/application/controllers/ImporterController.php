<?php

class ImporterController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function importerAction()
    {
        // action body
    	//$this->view->message = 'This is my new Zend Framework project!';
    	$lcl = new Application_Model_DbMapper();
    	
    	$this->view->message = "Table List: ";
    	foreach($lcl->tables as $data) {
			
    		$this->view->message .= ", ".$data;
		}
    }
    
    public function xmlAction()
    {
    		
    	$this->_helper->viewRenderer('simple');
    	
    	//$item = new XmlHelper();
    	$movies = new Application_Model_XmlHelper();
    	
    	//$this->view->message = gettype($movies->movie[0]->plot);
    	
    	$this->view->message =  $movies->getItem();
    	//$this->view->message =  gettype($movies->movie);
    }


}

