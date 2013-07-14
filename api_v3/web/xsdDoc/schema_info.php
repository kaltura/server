<?php 

$downloadUrl = 'http://' . kConf::get('www_host') . "/api_v3/index.php/service/schema/action/serve/type/$schemaType/name/$schemaType.xsd";

echo "Download URL: <a href=\"$downloadUrl;\" target=\"_blank\">$downloadUrl</a><br/>\n";

$schemaPath = SchemaService::getSchemaPath($schemaType);
$xslPath = dirname(__FILE__) . '/xsl/type.xsl';

// Load the XML source
$xml = new KDOMDocument;
$xml->load($schemaPath);

if($xml->firstChild->hasAttribute('version'))
	echo "Version: " . $xml->firstChild->getAttribute('version') . "<br/>\n";

$xsl = new KDOMDocument;
$xsl->load($xslPath);

// Configure the transformer
$proc = new XSLTProcessor;
$proc->importStyleSheet($xsl); // attach the xsl rules

echo "<br/>\n";
echo $proc->transformToXML($xml);
