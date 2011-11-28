<?php

if (count($argv) != 4)
	throw new Exception("Invalid argument. Usage: php cpcp.php <src sysname> <dst name> <dst sysname>");

$ini = parse_ini_file("abc.ini");
require_once ($ini["CLIENT_LIBS"]."/php5/KalturaClient.php");

$src = $argv[1];
$dstnm = $argv[2];
$dst = $argv[3];

$config = new KalturaConfiguration($ini["ABC_PARTNER_ID"]);
$config->serviceUrl = $ini["SERVICE_URL"];
$client = new KalturaClient($config);
$ks = $client->session->start($ini["ABC_ADMIN_SECRET"], "", KalturaSessionType :: ADMIN);
$client->setKs($ks);

$filter = new KalturaConversionProfileFilter;
$filter->systemNameEqual = $src;
$cps = $client->conversionProfile->listAction($filter);
if ($cps->totalCount == 1)
	$cpid = $cps->objects[0]->id;
else 
	throw new Exception("cannot find cpid sysname=$conversionProfileName");

$cps->objects[0]->name = $dstnm;
$cps->objects[0]->systemName = $dst;
	$cps->objects[0]->id = 
	$cps->objects[0]->partnerId = 
	$cps->objects[0]->createdAt = 
	$cps->objects[0]->isPartnerDefault = null;

$client->conversionProfile->add($cps->objects[0]);
