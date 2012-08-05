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
    				//TODOCMP: Removed since no longer seems needed.
    				//if(isset($_SESSION['fileLocation'])) {
    				//	unset($_SESSION['fileLocation']);
    				//}
    				//else {

    				
    				
    					//$this->previewTable = $lcl;
    					//$form->setValue('hidTableData') = base64_encode($form->previewTableData);
    				//}
    				$csvProcessor = new Application_Model_CsvFileImportMapper();
    				$csvProcessor->moveTemp($_FILES['dataFile']['tmp_name'], $_FILES['dataFile']['name']);
    				
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
    				
    				//$encodingRules = array(
    				//		"txtEncoding" => $form->getValue('txtEncoding'),
    				//		"fieldDelimit" => chr($fieldDelimit),//44 => comma
    				//		"txtQualifier" => chr($txtQualifier), //34 => "
    				//
    				//		);
    				//
    				//$_SESSION['fileImportProcessData'] = 
    				//	array(
    				//		"table" => $form->getValue('tableList'),
    				//		"columnRules" => $columnRules,
    				//	    "firstRowIsColumnHeaders" => false,
    				//		"encodingRules" => $encodingRules				
    				//	);
    				
    				$csvProcessor = new Application_Model_CsvFileImportMapper();
    				
    				$importSuccess = $csvProcessor->importFile($csvProcessor->uploadLocationFromFile($form->getValue('fileList')), //FileName
    						$form->getValue('tableList'), //Table
    						$columnRules, //Column Rules
    						$form->getValue('txtEncoding'), //Encoding
    						chr($form->getValue('fieldDelimitBox')), 
    						chr($form->getValue('txtQualifierBox')),
    						intval($form->getValue('firstRowColumnHeading')) //Column Headings
    				
    				);
    				
    				if($importSuccess) {
    					$this->_redirect("importer/review"); //???
    				}
    				else {
    					//display message
    				}
    			}
    			elseif ($_POST['delete']) {
    				//Process are you sure delete
    				
    				//Need to set value to none.
					//if answer to are you sure
    				$csvProcessor = new Application_Model_CsvFileImportMapper();
    				unlink($csvProcessor->uploadLocationFromFile($form->getValue('fileList')));
					
					
    			}
    			elseif ($_POST['apply']) {
    				//Run processing
    				$csvProcessor = new Application_Model_CsvFileImportMapper();
    				//$csvProcessor->moveTempAndEncode($_FILES['dataFile']['tmp_name'], $_FILES['dataFile']['name'],$form->getValue('txtEncoding'));
    				$fileLocationPointer = $csvProcessor->moveTempAndEncode($csvProcessor->uploadLocationFromFile($form->getValue('fileList')),$form->getValue('txtEncoding'));
    				//$tableName = $form->getValue('tableList');
    				//$_SESSION['fileLocation'] = $fileLocation;
    				
    				//TODOCMP: clean up and move
    				//Move this into its own function
    				$form->previewTableStatus = "render";
    				$form->previewTableStatusMessage = NULL;
    				
    					
    				$tableListMapper = new Application_Model_DbMapper();
    				
    				$form->columnListSet = $tableListMapper->columnList( $form->getValue('tableList'));
    					
    					
    				//$tableListMapper = new Application_Model_DbMapper();
    				//$form->columnListSet = $tableListMapper->columnList($form->getValue('tableList'));
    				$form->previewTableData = $csvProcessor->generateTableArray($fileLocationPointer,  $form->getValue('tableList'),
    						chr($form->getValue('fieldDelimitBox')), 
    						chr($form->getValue('txtQualifierBox')),
    						$form->getValue('firstRowColumnHeading'), $form->getValue('rowLimitList'));
    				
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

