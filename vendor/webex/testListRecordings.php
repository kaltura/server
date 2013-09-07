<?php
require_once(__DIR__ . '/xml/WebexXmlClient.class.php');
require_once(__DIR__ . '/xml/WebexXmlListRecordingRequest.class.php');

$url = "https://kaltura.webex.com/WBXService/XMLService";

$securityContext = new WebexXmlSecurityContext();
$securityContext->setUid("jkanarek"); // webex username
$securityContext->setPwd("1q2w3e4r"); // webex password
$securityContext->setSid("271615"); // webex site id
$securityContext->setPid("271ka"); // webex partner id

$listControl = new WebexXmlEpCreateTimeScopeType();
$listControl->setCreateTimeStart('12/21/2012 05:15:10');
$listControl->setCreateTimeEnd('12/21/2012 05:15:10');
$listRecordingRequest = new WebexXmlListRecordingRequest();
$listRecordingRequest->setCreateTimeScope($listControl);

$xmlClient = new WebexXmlClient($url, $securityContext);
$listRecordingResponse = $xmlClient->send($listRecordingRequest);
var_dump($listRecordingResponse->getRecording());
