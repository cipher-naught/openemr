<?php 
/**
 * @param  $xml
 * @param  $xsl
 * @return string xml
 */
function transform($xml, $xsl) {
   $xslt = new XSLTProcessor();
   $xslt->importStylesheet(new  SimpleXMLElement($xsl));
   return $xslt->transformToXml(new SimpleXMLElement($xml));
} 

//$handle = fopen("test.xml","r");
//include $_SERVER["DOCUMENT_ROOT"] . "/path/to/file.php";  

$fHandle= //file_get_contect("C:\\dev\\OpenEMR\\Source\\MyOpenEMR\\supportingFiles\\test.xml",FALSE);
  fopen("test.xml","r");
  $contents = "";
  while (!feof($fHandle)) {
  $line = fgets($fHandle);
//   echo $line;
   $contents .= $line;
    }
  fclose($fHandle);
  
$line ="";

$fHandle= //file_get_contect("C:\\dev\\OpenEMR\\Source\\MyOpenEMR\\supportingFiles\\test.xml",FALSE);
  fopen("test.xsl","r");
  $xslContents = "";
  while (!feof($fHandle)) {
  $line = fgets($fHandle);
//   echo $line;
   $xslContents .= $line;
    }
  fclose($fHandle);
//echo $contents;
//echo $_SERVER["DOCUMENT_ROOT"] 
//echo phpinfo();
echo transform($contents, $xslContents);
echo "\nDone";


//C:/dev/OpenEMR/Source/MyOpenEMR/supportingFiles