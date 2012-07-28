<?php

class Application_Form_Review extends Zend_Form
{     

	public $pageMessage;
    public function init()
    {
        /* Form Elements & Other Definitions Here ... */
    	$this->setMethod('post');
    	$this->pageMessage = "None";
    	$proceedBtn = new Zend_Form_Element_Submit('proceed');
    	$proceedBtn->setLabel("Process File");
    	
    	$this->addElement($proceedBtn,'proceed');
    	
    	$cancelBtn = new Zend_Form_Element_Submit('cancel');
    	$cancelBtn->setLabel("Cancel");
    	 
    	$this->addElement($cancelBtn,'cancel');
    	 
    	
    	
    	 
    }


}

