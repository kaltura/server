<?php
require_once(dirname(__FILE__).'/../bootstrap.php');

if($argc < 3)
{
	echo PHP_EOL . ' ---- Set Input Tags Map ---- ' . PHP_EOL;
	echo ' Execute: php ' . $argv[0] . ' [ conversionProfileId ] [ partnerId ] [ tagToAdd ]' . PHP_EOL;
	die(' Error: missing conversion profile id or partner id ' . PHP_EOL . PHP_EOL);
}

$conversionProfileId = $argv[1];
$partnerId = $argv[2];

// Default tagToAdd is force_tags
$tagToAdd = "force_tags";

// If argv contains tag, set tagToAdd.
if($argc >= 4)
{
	$tagToAdd = $argv[3];
}

$conversionProfile = conversionProfile2Peer::retrieveByPKAndPartnerId($conversionProfileId, $partnerId);
if(!$conversionProfile)
{
	die(" Error: conversion profile $conversionProfileId not exist for partner id $partnerId " . PHP_EOL . PHP_EOL);
}
$inputTagsMap = $conversionProfile->getInputTagsMap();
KalturaLog::debug("InputTagsMap = $inputTagsMap");

if(str_contains($inputTagsMap, $tagToAdd))
{
	KalturaLog::debug("tagToAdd $tagToAdd already contained in Input Tags Map $inputTagsMap");
}
else
{
	$inputTagsMap = $inputTagsMap . "," . $tagToAdd;
	KalturaLog::debug("setting InputTagsMap to $inputTagsMap");
	$conversionProfile->setInputTagsMap($inputTagsMap);
	$conversionProfile->save();
}
