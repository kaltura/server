<?php 

$downloadUrl = 'http://' . kConf::get('www_host') . "/api_v3/index.php/service/schema/action/serve/type/$schemaType/name/$schemaType.xsd";
?>
Download URL: <a href="<?php echo $downloadUrl; ?>" target="_blank"><?php echo $downloadUrl; ?></a><br/><br/>
<?php 

$schemaPath = SchemaService::getSchemaPath($schemaType);
$xslPath = dirname(__FILE__) . '/xsl/type.xsl';

// Load the XML source
$xml = new DOMDocument;
$xml->load($schemaPath);

$xsl = new DOMDocument;
$xsl->load($xslPath);

// Configure the transformer
$proc = new XSLTProcessor;
$proc->importStyleSheet($xsl); // attach the xsl rules

echo $proc->transformToXML($xml);
