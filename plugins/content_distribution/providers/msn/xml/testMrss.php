<?php

var_dump(time() + (60*60*24*70));

$xml = new DOMDocument;
$xml->load('mrss.xml');

$xsl = new DOMDocument;
$xsl->load('submit.xsl');

$providerData = array(
	'metadataProfileId' => 1,
);

$varNodes = $xsl->getElementsByTagName('variable');
foreach($varNodes as $varNode)
{
	$nameAttr = $varNode->attributes->getNamedItem('name');
	if(!$nameAttr)
		continue;
		
	$name = $nameAttr->value;
	if($name && $providerData[$name])
	{
		$varNode->textContent = $providerData[$name];
		$varNode->appendChild($xsl->createTextNode($providerData[$name]));
	}
}

$proc = new XSLTProcessor;
$proc->importStyleSheet($xsl); // attach the xsl rules
$proc->registerPHPFunctions();

$out = $proc->transformToDoc($xml);
$out->save('out.xml');