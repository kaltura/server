<?php
require_once(__DIR__ . '/../bootstrap.php');

class scriptLogger
{
	static function logScript($msg)
	{
		print $msg.PHP_EOL;
		KalturaLog::debug($msg);
	}
}

if($argc < 9){
	scriptLogger::logScript("Usage: [webex service URL] [webex username] [webex password] [webex site id] [webex partner id] [webex siteName] [start date timestamp] [end date timestamp]");
	scriptLogger::logScript("Usage: [webex siteName] can be '' in case its not relevant.");
	die("Not enough parameters" . "\n");
}

$webexServiceUrl = $argv[1];
$webexUserName = $argv[2];
$webexPass = $argv[3];
$webexSiteId = $argv[4];
$webexPartnerId = $argv[5];
$webexSiteName = $argv[6];
$startTime = $argv[7];
$endTime = $argv[8];
scriptLogger::logScript('Init webexWrapper');
$securityContext = new WebexXmlSecurityContext();
$securityContext->setUid($webexUserName); // webex username
$securityContext->setPwd($webexPass); // webex password
$securityContext->setSid($webexSiteId); // webex site id
$securityContext->setPid($webexPartnerId); // webex partner id
$securityContext->setSiteName($webexSiteName); //webex site name
$webexWrapper = new webexWrapper($webexServiceUrl, $securityContext, array("scriptLogger", "logScript"), array("scriptLogger", "logScript"));
$createTimeStart = date('m/j/Y H:i:s', $startTime);
$createTimeEnd  = date('m/j/Y H:i:s', $endTime);
$serviceTypes = webexWrapper::stringServicesTypesToWebexXmlArray(array(WebexXmlComServiceTypeType::_MEETINGCENTER));
$result = $webexWrapper->listAllRecordings($serviceTypes, $createTimeStart, $createTimeEnd);
if($result)
{
	foreach ($result as $recording)
	{
		$text = $recording->getName();
		if($recording->getPasswordReq())
			$text = $text. "; password required";
		else
			$text = $text. "; no password required";
		print($text. PHP_EOL);
	}
}