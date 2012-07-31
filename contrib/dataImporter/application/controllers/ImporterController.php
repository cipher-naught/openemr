<?php

class ImporterController extends Zend_Controller_Action
{

	public $my_var;
	//public $previewTableData;
	//public $previewTableStatus;
	
	//public $previewTableStatusMessage;
	
	
    public function init()
    {
        /* Initialize action controller here */
    	//$a = session_id(); //TODOcmp: Remove
    	//if(empty($a)) session_start();
    	//$a = session_id();
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
    
    public function reviewAction() {
    	$form = new Application_Form_Review();
    	$this->view->form = $form;
    	
    	$csvMapper = new Application_Model_CsvFileImportMapper();
    	if($this->getRequest()->isPost()) {
    		if($form->isValid($this->getRequest()->getPost())) {
    			//Do Work.
    			if(isset($_POST['proceed'])) {
    				//Run import process
    				//Pass Session, Table, file
    				$csvMapper = new Application_Model_CsvFileImportMapper();
    				
    				$outputVal = $csvMapper->importFile( $_SESSION['fileLocation'], //FileName
    						$_SESSION['fileImportProcessData']['table'], //Table
    						$_SESSION['fileImportProcessData']['columnRules'], //
    						$_SESSION['fileImportProcessData']['encodingRules']['txtEncoding'],
    						$_SESSION['fileImportProcessData']['encodingRules']['fieldDelimit'],
    						$_SESSION['fileImportProcessData']['encodingRules']['txtQualifier'],
    						$_SESSION['fileImportProcessData']['firstRowIsColumnHeaders']
    						
    						);
					//IF successful
    				//delete($_SESSION['fileLocation']);
    				//SQL to run is $outputVal;
    				$outputVal;
    				
    				
    			}
    			elseif(isset($_POST['cancel'])) {
    				$form->pageMessage = "Cancel Pressed";
    			}
    			else {
    				//Error;
    			}
    		}
    	}
    	elseif (!isset($_SESSION)) {
    		session_start();
    	}
    	
    }
    public function formAction()
    {
    	//$this->view->form = $form;
    	
    	$request = $this->getRequest();
    	$form = new Application_Form_Importer();
    	
    	
    	
    	if($this->getRequest()->isPost()) {
    		//Given that there are no validators, this will always pass.  
    		//Is included for consistency with the Zend framework.
    		if($form->isValid($request->getPost())) {
    			//Remove.


    			if(isset($_POST['uploadFile']) && isset($_FILES['dataFile']['type']) ) {
    				if(isset($_SESSION['fileLocation'])) {
    					unset($_SESSION['fileLocation']);
    				}
    				else {
    					$csvProcessor = new Application_Model_CsvFileImportMapper();
    					$fileLocation = $csvProcessor->moveTemp($_FILES['dataFile']['tmp_name'], $_FILES['dataFile']['name'], session_id());
    					//$tableName = $form->getValue('tableList');
    					$_SESSION['fileLocation'] = $fileLocation;
    					 
    					$form->previewTableStatus = "render";
    					$form->previewTableStatusMessage = NULL;
    					 
    					
    					$tableListMapper = new Application_Model_DbMapper();
    					 
    					$form->columnListSet = $tableListMapper->columnList( $form->getValue('tableList'));
    					
    					
    					//$tableListMapper = new Application_Model_DbMapper();
    					//$form->columnListSet = $tableListMapper->columnList($form->getValue('tableList'));
    					$form->previewTableData = $csvProcessor->generateTableArray($fileLocation,  $form->getValue('tableList'),
    							chr($form->getValue('fieldDelimitBox')),
    							chr($form->getValue('txtQualifierBox')),
    							true);
    				
    					//$this->previewTable = $lcl;
    					//$form->setValue('hidTableData') = base64_encode($form->previewTableData);
    				}
    			}  
    			elseif($_POST['processFile']) { //Process File, sending to Review "action"
    				
    				$tableName = $form->getValue('tableList');
    				
    				
    				
    				$columnRules = array();
    				$i =0;
    				$colName = 'name_'.$i;
    				$lclVal = $_POST[$colName];
    				while(isset($lclVal)) {
    					if($lclVal != "skip") {
    						array_push($columnRules, $lclVal);
    					}
    					else {
    						array_push($columnRules, NULL);
    					}
    					$i++;
    					$colName = 'name_'.$i;
    					$lclVal = $_POST[$colName];
    				}
    				
    				$encodingRules = array(
    						"txtEncoding" => $form->getValue('txtEncoding'),
    						"fieldDelimit" => chr($fieldDelimit),//44 => comma
    						"txtQualifier" => chr($txtQualifier), //34 => "
    						
    						);
    				
    				$_SESSION['fileImportProcessData'] = 
    					array(
    						"table" => $form->getValue('tableList'),
    						"columnRules" => $columnRules,
    					    "firstRowIsColumnHeaders" => false,
    						"encodingRules" => $encodingRules				
    					);
    				$this->_redirect("importer/review");
    			}
    			else {

    				//if(isset($form->getValue('hidTableData'))) { //we have post back data
    					
    					//$form->previewTableData = base_decode($form->getValue('hidTableData'));
	    				//Repopulate data here
    				//}
    				//else {
    					$form->previewTableStatus = "processing";
    					$form->previewTableStatusMessage = "Processing file, please wait";
    				//}
    			}
    		}
    		else {
    			//$this->view->message = "Sorry, there was a problem.";
    			//formResponse("Sorry, there was a problem.");
    			//Repopulate data here 
    		}
    	}
    	else {
    		
    		$form->previewTableStatus = "nodata";
    		$form->previewTableStatusMessage = "No data submitted!";
    	}

    	//Assign the form to the view.
    	$this->view->form = $form;
    }

}

