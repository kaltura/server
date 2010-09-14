<?php
/**
 * This script executed by the xymon monitor
 * 
 * Its creating a session in order to get a ks
 * With the ks it's requesting for the full status from the API
 * The resulted XML is parsed using the fullstatus.xsl file
 * The resulted html is sent to the xymon
 * 
 * @package Scheduler
 * @subpackage Monitor
 */

$bbHosts = '@XYMON_ROOT_DIR@/server/etc/bb-hosts';
$bb = '@XYMON_ROOT_DIR@/home/xymon/client/bin/bb';
$bbDisp = "127.0.0.1";

function check(&$msg, $url)
{
	$service = 'session';
	$action = 'start';
	$secret = '@BATCH_PARTNER_ADMIN_SECRET@';
	$partnerId = -1;
	$userId = 'xymon';
	$type = 2;
	
	$getKS = "http://$url/api_v3/index.php?service=$service&action=$action&secret=$secret&partnerId=$partnerId&userId=$userId&type=$type&nocache";
	try
	{
		$content = @file_get_contents($getKS);
	}
	catch(Exception $e)
	{
		$msg = $e->getMessage();
		return false;
	}
	
	if(!$content)
	{
		$msg = "unable to get data";
		return false;
	}

	$arr = null;
	if(!preg_match('/<result>([^<]+)<\/result>/', $content, $arr))
	{
		$msg = "invalid return data\n$content";
		return false;
	}

	$ks = $arr[1];
	$service = 'batchControl';
	$action = 'getFullStatus';
	
	$getStatus = "http://$url/api_v3/index.php?service=$service&action=$action&ks=$ks&nocache";

	// Load the XML source
	if(class_exists('DOMDocument') && class_exists('XSLTProcessor'))
	{
		$xml = new DOMDocument;
		$xml->load($getStatus);
		
		$resultElements = $xml->getElementsByTagName("result");
		$resultElement = $resultElements->item(0);
		$resultElement->setAttribute('timestamp', time());
		
		$xsl = new DOMDocument;
		$xsl->load(dirname(__FILE__) . '/fullstatus.xsl');
		
		// Configure the transformer
		$proc = new XSLTProcessor;
		$proc->importStyleSheet($xsl); // attach the xsl rules
		
		$msg = $proc->transformToXML($xml);
	}
	else
	{
		$msg = "batches are ok\nXSL is required to parse the XML data.";
	}
//	echo "'$msg'\n";
//	echo "-----------------------------------------------\n\n";
//	echo $xml->saveHTML();
//	exit;

	return true;
}


function check_host($url, $host)
{
	global $bb, $bbDisp;
	
	echo "check $host on $url: ";
	
	$msg = null;
	if(check($msg, $url))
	{
		echo "OK \n";
	}
	else
	{
		echo "$msg \n";
		$msg = "red `date`\n\n$msg";
	}
		
//	echo substr($msg, 0, 500);
//	exit;

	$data = "\"status $host.batch $msg\"";
	$cmd = "$bb $bbDisp $data";
	
	system($cmd);
}
	
$lines = file($bbHosts);
foreach($lines as $line)
{
	$line = trim($line);
	if(preg_match('/^#/', $line))
		continue;
		
	if(!preg_match('/^(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})\s+([\w.\d-]+)\s+#(.+)$/', $line, $arr))
		continue;
	$url = $arr[1];
	$host = $arr[2];
	$modules = explode(' ', $arr[3]);
	
	if(!in_array('batch', $modules))
		continue;
	check_host($url, $host);
}
