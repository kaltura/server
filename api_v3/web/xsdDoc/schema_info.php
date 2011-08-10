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
