<?php
require_once(__DIR__ . '/xml/WebexXmlClient.class.php');
require_once(__DIR__ . '/xml/WebexXmlListRecordingRequest.class.php');

$url = "https://kaltura.webex.com/WBXService/XMLService";

$securityContext = new WebexXmlSecurityContext();
$securityContext->setUid(""); // webex username
$securityContext->setPwd(""); // webex password
$securityContext->setSid(""); // webex site id
$securityContext->setPid(""); // webex partner id

$listControl = new WebexXmlEpListControlType();
$listControl->setStartFrom(1);
$listControl->setMaximumNum(2);

$listRecordingRequest = new WebexXmlListRecordingRequest();
$listRecordingRequest->setListControl($listControl);

$xmlClient = new WebexXmlClient($url, $securityContext);
$listRecordingResponse = $xmlClient->send($listRecordingRequest);
var_dump($listRecordingResponse);
