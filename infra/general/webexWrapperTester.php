<?php
defined('KALTURA_ROOT_PATH') ||  define('KALTURA_ROOT_PATH', realpath(__DIR__ . '/../../'));
require_once 'webexWrapper.php';

class scriptLogger
{
	static function logScript($msg)
	{
		print $msg.PHP_EOL;
	}
}

print 'Before init'. PHP_EOL;
$securityContext = new WebexXmlSecurityContext();
$securityContext->setUid('kalturaprod'); // webex username
$securityContext->setPwd('v1de04RocheWBX'); // webex password
$securityContext->setSid('657663'); // webex site id
$securityContext->setPid('657ro'); // webex partner id
$webexWrapper = new webexWrapper('https://roche.webex.com/WBXService/XMLService', $securityContext, array("scriptLogger", "logScript"), array("scriptLogger", "logScript"));
print 'after init'. PHP_EOL;
$createTimeStart = date('m/j/Y H:i:s', 1489935463);
$createTimeEnd  = date('m/j/Y H:i:s',1511103463);
$result = $webexWrapper->deleteRecordingsByDates(array(WebexXmlComServiceTypeType::_MEETINGCENTER), $createTimeStart, $createTimeEnd);
print "scrip ended.".PHP_EOL;
