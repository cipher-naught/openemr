<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    	$contextSwitch = $this->_helper->getHelper('contextSwitch');
    	$contextSwitch->addActionContext('action1', array('xml','json'))->initContext();
    }

    public function action1Action()
    {
    	$this->view->assign('title','This is the action');
    }

    public function indexAction()
    {
        // action body
    	$this->view->message = 'This is my new Zend Framework projdddect!'.APPLICATION_PATH;
    }

    //public function importerAction()
    //{
    //    // action body
    //}

    //public function reviewAction()
    //{
     //   // action body
    //}


}





