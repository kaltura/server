<?php
require_once realpath(__DIR__ . '/../../') . '/lib/KalturaClient.php';
require_once realpath(__DIR__ . '/../') . '/XmlHelper.php';
$options = getopt("u:");
$serviceUrl = $options["u"];
$clientConfig = new KalturaConfiguration();
$clientConfig->partnerId = null;

$clientConfig->serviceUrl = $serviceUrl;

$client = new KalturaClient($clientConfig);
$start = microtime(true);
$errors = array();
try {
	$res = $client->system->ping();
	$end = microtime(true);
			
} catch (KalturaClientException $ex) {
	$end = microtime(true);
	$error = new MonitorError();
        $error->level = "ERR";
        $error->description = $ex->getMessage();
	$error->code = $ex->getCode();
        $errors[] = $error;

}
$execTime = $end - $start;
$xml_res =  XmlHelper::getXMLResult($execTime, $execTime, "Execution time was: $execTime", $errors);
$xmlString =  $xml_res->asXML();
echo $xmlString;

