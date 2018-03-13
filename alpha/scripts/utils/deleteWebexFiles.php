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

if($argc < 7){
	scriptLogger::logScript("Usage: [webex username] [webex password] [webex site id] [webex partner id] [start date timestamp] [end date timestamp]");
	die("Not enough parameters" . "\n");
}

$webexUserName = $argv[1];
$webexPass = $argv[2];
$webexSiteId = $argv[3];
$webexPartnerId = $argv[4];
$startTime = $argv[5];
$endTime = $argv[6];
scriptLogger::logScript('Init webexWrapper');
$securityContext = new WebexXmlSecurityContext();
$securityContext->setUid($webexUserName); // webex username
$securityContext->setPwd($webexPass); // webex password
$securityContext->setSid($webexSiteId); // webex site id
$securityContext->setPid($webexPartnerId); // webex partner id
$webexWrapper = new webexWrapper('https://roche.webex.com/WBXService/XMLService', $securityContext, array("scriptLogger", "logScript"), array("scriptLogger", "logScript"));
$createTimeStart = date('m/j/Y H:i:s', $startTime);
$createTimeEnd  = date('m/j/Y H:i:s', $endTime);
scriptLogger::logScript('Starting to delete webex files.');
$serviceTypes = webexWrapper::stringServicesTypesToWebexXmlArray(array(WebexXmlComServiceTypeType::_MEETINGCENTER));
$webexWrapper->deleteRecordingsByDates($serviceTypes, $createTimeStart, $createTimeEnd);

