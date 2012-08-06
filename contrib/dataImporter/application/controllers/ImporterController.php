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

    }

   
    public function formAction()
    {

    	
    	$request = $this->getRequest();
    	$form = new Application_Form_Importer();
    	
    	
    	
    	if($this->getRequest()->isPost()) {
    		//Given that there are no validators, this will always pass.  
    		//Is included for consistency with the Zend framework.
    		if($form->isValid($request->getPost())) {
    			//Process file upload
    			if(isset($_POST['uploadFile']) && isset($_FILES['dataFile']['type']) ) {

    				$csvProcessor = new Application_Model_CsvFileImportMapper();
    				$csvProcessor->moveTemp($_FILES['dataFile']['tmp_name'], $_FILES['dataFile']['name']);
    				
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
    				$fileLocationPointer = $csvProcessor->moveTempAndEncode($csvProcessor->uploadLocationFromFile($form->getValue('fileList')),$form->getValue('txtEncoding'));
    				$importSuccess = $csvProcessor->importFile($fileLocationPointer, //File Pointer
    						$form->getValue('tableList'), //Table
    						$columnRules, //Column Rules
    						$form->getValue('txtEncoding'), //Encoding
    						chr($form->getValue('fieldDelimitBox')), 
    						chr($form->getValue('txtQualifierBox')),
    						intval($form->getValue('firstRowColumnHeading')) //Column Headings
    				
    				);
    				
    				if($importSuccess) {
    					$form->lblOutput = "Your file was successfully imported.";
    				}
    				else {
    					//display error message
    					$form->lblOutput = "Problem importing your file. Please contact support.";
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

    				$fileLocationPointer = $csvProcessor->moveTempAndEncode($csvProcessor->uploadLocationFromFile($form->getValue('fileList')),$form->getValue('txtEncoding'));

    				$form->previewTableStatus = "render";
    				$form->previewTableStatusMessage = NULL;
    				
    					
    				$tableListMapper = new Application_Model_DbMapper();
    				
    				$form->columnListSet = $tableListMapper->columnList( $form->getValue('tableList'));
    					
    					
    				$form->previewTableData = $csvProcessor->generateTableArray($fileLocationPointer,  $form->getValue('tableList'),
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
    	}
    	else {
    		
    		$form->previewTableStatus = "nodata";
    		$form->previewTableStatusMessage = "No data submitted!";
    	}

    	//Assign the form to the view.
    	$this->view->form = $form;
    }

}

