<?php
//include 'example.php';
class Application_Model_XmlHelper 
{
	//asdfasfdasf
	public $_xmlstr = <<<XML
<?xml version='1.0' standalone='yes'?>
<importFile>
<importer>
<tableName>patientData</tableName>
<column>
<name>skip</name>
<name>State</name>
<name>skip</name>
<name>Location</name>
</column>
</importer>
<data>
<row>
<item>skip</item>
<item>Dog</item>
<item>Mouse</item>
<item>Tampa</item>
</row>
<row>
<item>Elephant</item>
<item>Monkey</item>
<item>Geese</item>
<item>St Pete</item>
</row>
</data>
</importFile>
XML;
	
	public $_xslstr = <<<'XML'
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
 
 <xsl:output method="text" encoding="iso-8859-1" indent="no"/>
 
 
 <xsl:template match="importFile/importer/colum/name[text() = 'skip']">
 <xsl:text>Found it</xsl:text>
 	
 </xsl:template>
 
 <xsl:template match="data/row"><xsl:text disable-output-escaping="yes">&lt;br/&gt;</xsl:text>
	insert into <xsl:value-of select="../../importer/tableName"/>
	<xsl:text>( </xsl:text>
	<xsl:for-each select="../../importer/column/name" >
  	  <xsl:if test="text() != 'skip'" >
  	  
	  	  <xsl:value-of select="." />
		  
		  <xsl:if test="position()!=last()">
		        <xsl:text>, </xsl:text>
		  </xsl:if>
	  </xsl:if>	  
  </xsl:for-each>
  <xsl:variable name="skipList" >
    <xsl:for-each select="../../importer/column/name[text() = 'skip']">
  	  <xsl:text>,</xsl:text><xsl:value-of  select="count(preceding-sibling::*) + 1"/><xsl:text>,</xsl:text>
  	</xsl:for-each>
 </xsl:variable>
 
  <xsl:text>) VALUES (</xsl:text> 
    
    <xsl:for-each select="item" >
  	  
	  <xsl:if test="contains($skipList,concat(',',count(preceding-sibling::*) + 1,','))">
	    
	  </xsl:if>
	  <xsl:if test="not(contains($skipList,concat(',',count(preceding-sibling::*) + 1,',')))">
	    <xsl:value-of select="." />
	    <xsl:if test="position()!=last()">
	        <xsl:text>, </xsl:text>
	  	</xsl:if>
	  </xsl:if>
	  
	  
  </xsl:for-each> 
  <xsl:text>)</xsl:text>
 </xsl:template>
 
 
</xsl:stylesheet>
XML;
	private $_xml ;

	public function __construct() 
	{
		
		//return new SimpleXMLElement($xmlstr);	
		$this->_xml= new SimpleXMLElement($this->_xmlstr);
	
	}
	
	public function getItem()
	{
		//return "not supported";
		$xslt = new XSLTProcessor();
		$xslt->importStylesheet(new SimpleXMLElement($this->_xslstr)) ;
		return $xslt->transformToXml(new SimpleXMLElement($this->_xmlstr));
		//return   		$this->_xslstr;
		//$this->_xml->movie[0]->plot;
	}
  	
}