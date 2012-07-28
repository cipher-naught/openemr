<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

/*	protected function _initView()
	{
		$view = new Zend_View();
		$view->setEncoding('UTF-8');
		$view->doctype('XHTML1_STRICT');
		$view->headMeta()->appendHttpEquiv(
				'Content-Type', 'text/html;charset=utf-8'
		);
		$viewRenderer =
		Zend_Controller_Action_HelperBroker::getStaticHelper(
				'ViewRenderer'
		);
		$viewRenderer->setView($view);
		
		return $view;
	}*/
	protected function _initView()
	{
		// Initialize view
		$view = new Zend_View();
		$view->doctype('XHTML1_STRICT');
		$view->headTitle('My Project');
		$view->env = APPLICATION_ENV;
	
		// Add it to the ViewRenderer
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper(
				'ViewRenderer'
		);
		$viewRenderer->setView($view);
		$view->addHelperPath(APPLICATION_PATH . "/views/helpers", "Zend_Helper");
		
		// Return it, so that it can be stored by the bootstrap
		return $view;
	}
	
/*	protected function _initFrontController()
	{
		//$this->bootstrap('FrontController');
		//$front = $this->getResource('FrontController');
		$front = Zend_Controller_Front::getInstance();
		$response = new Zend_Controller_Response_Http;
		$response->setHeader('Content-Type',
				'text/html; charset=UTF-8', true);
		$front->setResponse($response);
	}*/
	protected function _initFrontControllerOutput() {
	
		$this->bootstrap('FrontController');
		$frontController = $this->getResource('FrontController');
	
		$response = new Zend_Controller_Response_Http;
		$response->setHeader('Content-Type', 'text/html; charset=UTF-8', true);
		$frontController->setResponse($response);
	
		$frontController->setParam('useDefaultControllerAlways', false);
	
		return $frontController;
	
	}

	
}

