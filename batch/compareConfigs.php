<?php

$newDir = $argv[1];
$oldDir = $argv[2];

require_once(__DIR__ . "/bootstrap.php");

$hosts = array(
	'ny-batch1',
	'ny-batch2',
	'ny-batch3',
	'ny-batch4',
	'ny-batch5',
	'ny-batch6',
	'ny-cencoder2',
	'ny-cencoder3',
	'ny-encoder10',
	'ny-encoder11',
	'ny-encoder12',
	'ny-encoder13',
	'ny-encoder14',
	'ny-encoder15',
	'ny-encoder16',
	'ny-encoder17',
	'ny-encoder18',
	'ny-encoder19',
	'ny-encoder2',
	'ny-encoder20',
	'ny-encoder21',
	'ny-encoder22',
	'ny-encoder3',
	'ny-encoder4',
	'ny-encoder5',
	'ny-encoder6',
	'ny-encoder7',
	'ny-encoder8',
	'ny-encoder9',
	'ny-fms1',
	'ny-vmwin',
	'ny-vmwin1',
	'NY-VMWIN10',
	'NY-VMWIN11',
	'NY-VMWIN12',
	'NY-VMWIN13',
	'NY-VMWIN14',
	'NY-VMWIN15',
	'ny-vmwin2',
	'ny-vmwin3',
	'NY-VMWIN4',
	'NY-VMWIN5',
	'NY-VMWIN6',
	'NY-VMWIN7',
	'NY-VMWIN8',
	'NY-VMWIN9',
	'ny-vmwin-small',
	'ny-win1',
	'ny-win3',
	'ny-win4',
	'ny-xserve1',
	'pa-batch1',
	'pa-batch2',
	'pa-batch3',
	'pa-batch4',
	'pa-batch5',
	'pa-batch6',
	'pa-cencoder2',
	'pa-cencoder3',
	'pa-encoder10',
	'pa-encoder11',
	'pa-encoder12',
	'pa-encoder13',
	'pa-encoder14',
	'pa-encoder15',
	'pa-encoder16',
	'pa-encoder17',
	'pa-encoder18',
	'pa-encoder19',
	'pa-encoder2',
	'pa-encoder20',
	'pa-encoder21',
	'pa-encoder22',
	'pa-encoder23',
	'pa-encoder24',
	'pa-encoder3',
	'pa-encoder4',
	'pa-encoder5',
	'pa-encoder6',
	'pa-encoder7',
	'pa-encoder8',
	'pa-encoder9',
	'pa-fms1',
	'pa-reports',
	'pa-vmwin',
	'pa-vmwin1',
	'pa-vmwin10',
	'pa-vmwin11',
	'pa-vmwin12',
	'pa-vmwin13',
	'pa-vmwin14',
	'pa-vmwin15',
	'pa-vmwin2',
	'pa-vmwin3',
	'pa-vmwin4',
	'pa-vmwin5',
	'pa-vmwin6',
	'pa-vmwin7',
	'pa-vmwin8',
	'pa-vmwin9',
	'pa-vmwin-small',
	'PA-WIN1',
	'pa-win3',
	'pa-win4',
	'pa-xserve1',
	'vmwin-small',
);

foreach($hosts as $host)
{
	KSchedulerConfig::setHostname($host);
	
	$newConfig = new KSchedulerConfig($newDir);
	$oldConfig = new KSchedulerConfig($oldDir);
	
	$array1 = $newConfig->toArray();
	$array2 = $oldConfig->toArray();
	
	$diff1 = array_diff($array1, $array2);
	$diff2 = array_diff($array2, $array1);

	if(count($diff1))
	{
		KalturaLog::info("Host $host [" . print_r($diff1, true) . "]");
	}
	if(count($diff2))
	{
		KalturaLog::info("Host $host [" . print_r($diff2, true) . "]");
	}
	
	if(!count($diff1) && !count($diff2))
		KalturaLog::info("Host $host OK");
}
