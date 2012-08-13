<?php
// Copyright (C) 2012 Chris Paulus <coding@cipher-naught.com>
// Sponsored by David Eschelbacher, MD
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

class ImporterController extends Zend_Controller_Action
{

	public $my_var;
	//public $previewTableData;
	//public $previewTableStatus;
	
	//public $previewTableStatusMessage;
	
	
    public function init()
    {
        /* Initialize action controller here */

    }

   
    public function formAction()
    {

    	
    	$request = $this->getRequest();
    	$form = new Application_Form_Importer();
    	$tableListMapper = new Application_Model_DbMapper();
    	
    	
    	if($this->getRequest()->isPost()) {
    		
    		
    		//Given that there are no validators, this will always pass.  
    		//Is included for consistency with the Zend framework.
    		if($form->isValid($request->getPost())) {
    			$form->columnListSet = $tableListMapper->columnList( $form->getValue('tableList'));
    			//Process file upload
    			if(isset($_POST['uploadFile']) && isset($_FILES['dataFile']['type']) ) {

    				$csvProcessor = new Application_Model_CsvFileImportMapper();
    				$csvProcessor->moveTemp($_FILES['dataFile']['tmp_name'], $_FILES['dataFile']['name']);
    				
    				$form->populateFileList($form->getElement('fileList'));
    				
    			}  
    			//Process/Import File
    			elseif($_POST['processFile']) { 
    				
    				$tableName = $form->getValue('tableList');
    				
    				
					//This section processes the file column heading numbers.    				
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
    				
    				//The actual processing of the file.
    				$csvProcessor = new Application_Model_CsvFileImportMapper();
    				
    				$importSuccess = $csvProcessor->importFile($csvProcessor->uploadLocationFromFile($form->getValue('fileList')), //File Pointer
    						$form->getValue('tableList'), //Table
    						$columnRules, //Column Rules
    						$form->getValue('txtEncoding'), //Encoding
    						chr($form->getValue('fieldDelimitBox')), 
    						chr($form->getValue('txtQualifierBox')),
    						intval($form->getValue('firstRowColumnHeading')), //Column Headings
    						$form->getValue('dateSelect')
    				
    				);
    				
					if($importSuccess["success"]) {
    					$form->lblOutput = "Your file was successfully imported. ".$importSuccess["successfulTransactions"].
    					                       " transaction imported.";
    				}
    				else if($importSuccess["rolledback"]){
    					//display error message
    					$form->lblOutput = "Problem importing your file. All updates rolled back.";
    				}
    				else {
    					$form->lblOutput = "Problem importing your file. Unable to rollback transactions.";
    				}
    			}
    			elseif ($_POST['delete']) {

    				$csvProcessor = new Application_Model_CsvFileImportMapper();
    				unlink($csvProcessor->uploadLocationFromFile($form->getValue('fileList')));
					
    				$this->lblOutput = "File successfully deleted. Please select a file to import.";
    				$form->getElement("processFile")->setAttrib("disable", true);
    				
    				
    			}
    			elseif ($_POST['apply']) {
    				//Run processing
    				$csvProcessor = new Application_Model_CsvFileImportMapper();

    				//$fileLocationPointer = $csvProcessor->moveTempAndEncode($csvProcessor->uploadLocationFromFile($form->getValue('fileList')),$form->getValue('txtEncoding'));


    				$form->previewTableStatus = "render";
    				$form->previewTableStatusMessage = NULL;
    				
    					
    				
    				
    				
    				
    				 
    					
    				$form->previewTableData = $csvProcessor->generateTableArray($csvProcessor->uploadLocationFromFile($form->getValue('fileList')), 
    						chr($form->getValue('fieldDelimitBox')), 
    						chr($form->getValue('txtQualifierBox')),
    						$form->getValue('firstRowColumnHeading'), $form->getValue('rowLimitList'));
    				
    				$form->lblOutput = "Check the preview of the file.  If it is correct please process the file.";
    				$form->getElement("processFile")->setAttrib("disable", false);
    			}
    			else {
    				$form->previewTableStatus = "processing";
    				$form->previewTableStatusMessage = "Processing file, please wait";
    			}
    		}
    		$form->populateFileList($form->getElement('fileList'));
    	}
    	else {
    		
    		$form->previewTableStatus = "nodata";
    		$form->previewTableStatusMessage = "No data submitted!";
    	}

    	//Assign the form to the view.
    	$this->view->form = $form;
    }

}

