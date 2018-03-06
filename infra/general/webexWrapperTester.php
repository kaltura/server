<?php
defined('KALTURA_ROOT_PATH') ||  define('KALTURA_ROOT_PATH', realpath(__DIR__ . '/../../'));
require_once 'webexWrapper.php';
print 'Before init'. PHP_EOL;
$securityContext = new WebexXmlSecurityContext();
$securityContext->setUid('kalturaprod'); // webex username
$securityContext->setPwd('v1de04RocheWBX'); // webex password
$securityContext->setSid('657663'); // webex site id
$securityContext->setPid('657ro'); // webex partner id
$webexWrapper = new webexWrapper('https://roche.webex.com/WBXService/XMLService', $securityContext);
print 'after init'. PHP_EOL;
$temp = $webexWrapper->listRecordings(array(WebexXmlComServiceTypeType::_MEETINGCENTER));
print count($temp);