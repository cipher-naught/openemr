<?php


class Application_Form_Importer extends Zend_Form
{

	public $columnListSet;
	public $previewTableStatus;
	public $previewTableData;
	public $previewTableStatusMessage;
	
	
		
	/**
	 * Creates Field Delimit Select List
	 * @param string $name
	 * @return Zend_Form_Element_Select
	 */
    public function createFieldDelimitSelectList($name)
    {
    	$fielddDelimit = new Zend_Form_Element_Select($name);
    	$this->clearDecorators($fielddDelimit);
    	$fielddDelimit->addMultiOption(0,"Other");
    	$fielddDelimit->addMultiOption(44,", (Comma)");
    	$fielddDelimit->addMultiOption(124,"| (Pipe");
    	$fielddDelimit->addMultiOption(9,"(Tab)");
    	$fielddDelimit->setValue(44);
    	$fielddDelimit->setAttribs(array(
    				"onchange" => "SetElement(this.value,this.attributes[\"id\"].value+\"Box\")"
    			)
    			);

    	
    	return $fielddDelimit;
    	
    } 
    /**
     * Creates Text Qualifer Select List
     * @param string $name
     * @return Zend_Form_Element_Select
     */
    public function createTextQualifierSelectList($name)
    {
    	$localSelect = new Zend_Form_Element_Select($name);
    	$this->clearDecorators($localSelect);
    	 
    	$localSelect->addMultiOption(0,"Other");
    	$localSelect->addMultiOption(34,"\"");
    	$localSelect->addMultiOption(39,"'");
    	$localSelect->addMultiOption(96,"`");  //TODOcmp: May need to update
    	$localSelect->addMultiOption(124,"| (Pipe");
    	$localSelect->setValue(34);
    	$localSelect->setAttribs(array(
    			"onchange" => "SetElement(this.value,this.attributes[\"id\"].value+\"Box\")"
    	)
    	);
    	return $localSelect;    	 
    }
    
    /**
     * Creates Text Encoding drop down list.
     * @param string $name
     * @return Zend_Form_Element_Select
     */
    public function createTextEncoding($name)
    {
    	
    	//See here for more information: http://php.net/manual/en/mbstring.supported-encodings.php
    	$localSelect = new Zend_Form_Element_Select($name);
    	$this->clearDecorators($localSelect);
    
    	$localSelect->addMultiOption("ASCII","ASCII");
    	$localSelect->addMultiOption("UTF-8","UTF-8");
    	$localSelect->addMultiOption("EUC-JP","EUC-JP");
    	$localSelect->addMultiOption("eucJP-win","eucJP-win");
    	$localSelect->addMultiOption("JIS","JIS");
    	
    	$localSelect->addMultiOption("ISO-8859-1","ISO-8859-1");
    	$localSelect->addMultiOption("ISO-8859-2","ISO-8859-2");
    	$localSelect->addMultiOption("ISO-8859-3","ISO-8859-3");
    	$localSelect->addMultiOption("ISO-8859-4","ISO-8859-4");
    	$localSelect->addMultiOption("ISO-8859-5","ISO-8859-5");
    	$localSelect->addMultiOption("ISO-8859-6","ISO-8859-6");
    	$localSelect->addMultiOption("ISO-8859-7","ISO-8859-7");
    	$localSelect->addMultiOption("ISO-8859-8","ISO-8859-8");
    	$localSelect->addMultiOption("ISO-8859-9","ISO-8859-9");
    	$localSelect->addMultiOption("ISO-8859-10","ISO-8859-10");
    	$localSelect->addMultiOption("ISO-8859-13","ISO-8859-13");
    	$localSelect->addMultiOption("ISO-8859-14","ISO-8859-14");
    	$localSelect->addMultiOption("ISO-8859-15","ISO-8859-15");
    	$localSelect->addMultiOption("UTF-32","UTF-32");
    	$localSelect->addMultiOption("UTF-32BE","UTF-32BE");
    	$localSelect->addMultiOption("UTF-32LE","UTF-32LE");
    	$localSelect->addMultiOption("UTF-16","UTF-16");
    	$localSelect->addMultiOption("UTF-16BE","UTF-16BE");
    	$localSelect->addMultiOption("UTF-16LE","UTF-16LE");
    	
    	return $localSelect;
    
    }
    
    /**
     * Remove Zend Errors, HtmlTag, and Label from Zend_Element
     * @param Zend_Element $item
     */
    
    public function clearDecorators($item){
    	$item->removeDecorator('Errors');
    	$item->removeDecorator('HtmlTag');
    	$item->removeDecorator('Label');
    }
    
    public function init()
    {
        /* Form Elements & Other Definitions Here ... */
    	
    	$this->setMethod('post');
    	$element = new Zend_Form_Element_File('dataFile');

    	$element->addValidator('Count', false, 1);
    	$element->addValidator('Size',false,29*1024*1024);
    	$this->addElement($element, 'dataFile');
    	
    	$fieldDelimit = $this->createFieldDelimitSelectList('fieldDelimit');
    	
    	$this->addElement($fieldDelimit,'fieldDelimit');
    	
    	$txtQualifier = $this->createTextQualifierSelectList('txtQualifier');
    	$this->addElement($txtQualifier,'txtQualifier');
    	
    	$txtEncoding = $this->createTextEncoding('txtEncoding');
    	$this->addElement($txtEncoding,'txtEncoding');
    	
    	$this->setAttrib('enctype','multipart/form-data');

    	$fileUpload = new Zend_Form_Element_Submit('uploadFile');
    	$fileUpload->setLabel("Upload File");
    	
    	$this->addElement($fileUpload,'uploadFile');
    	$fileProcess = new Zend_Form_Element_Submit('processFile');
    	$fileProcess->setLabel("Process File");
    	 
    	$this->addElement($fileProcess,'processFile');
    	
    	$view = $this->getView();
    	$tableListMapper = new Application_Model_DbMapper();
    	$tableList = $tableListMapper->tables;//$this->getView()->translate('dataBaseTables');
    	
    	$this->addElement($view->ArrayView()->makeSelectList('tableList',$tableList),'tableList');
    	
    	$this->columnListSet = $tableListMapper->columnList("addresses");
    	
    	$txtQualifierBox = new Zend_Form_Element_Text("txtQualifierBox");
    	
    	$this->clearDecorators($txtQualifierBox);
    	$txtQualifierBox->setAttribs(array(
    			"class" => "textbox",
    			"size" => 4,
    			"onblur" => "SelectElement(this.value,this.attributes[\"id\"].value.replace(\"Box\",\"\"))"
    			)
    	);
    	$txtQualifierBox->setValue($txtQualifier->getValue());
    	
    	$this->addElement($txtQualifierBox,'txtQualifierBox');
    	
    	
    	$fieldDelimitBox = new Zend_Form_Element_Text("fieldDelimitBox");
    	$fieldDelimitBox->setAttribs(array(
    			"class" => "textbox",
    			"size" => 4,
    			"onblur" => "SelectElement(this.value,this.attributes[\"id\"].value.replace(\"Box\",\"\"))"
    	)
    	);
    	$this->clearDecorators($fieldDelimitBox);
    	$fieldDelimitBox->setValue($fieldDelimit->getValue());
    	$this->addElement($fieldDelimitBox,'fieldDelimitBox');
    	
    	//Hidden Table Data.
    	$hidTableData = new  Zend_Form_Element_Hidden('hidTableData');
    	$this->addElement($hidTableData,'hidTableData');
    	
    }

}

