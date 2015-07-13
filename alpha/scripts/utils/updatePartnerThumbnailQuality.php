<?php

	/*	errors:
	/	ERR1 - partner not found
	*/

	// this chdir can be changed according to environment
	chdir(__DIR__.'/../');
	require_once(__DIR__ . '/../bootstrap.php');
	//direct all select queries to A slave since by default it's to the master
	myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL3;
	
	if($argc < 4){
        KalturaLog::debug ("Usage: [fileName] [quality] [realRun]");
        die("Not enough parameters" . "\n");
	}
	
	$fileName = $argv[1];
	if (!file_exists($fileName))
		die ("file does not exist" . PHP_EOL);
	
	$quality = $argv[2];
	if ($quality < 0 || $quality > 100)
		die ("please enter quality value between 0 and 100 , including");

	//should the script save() ? by default will not save
	$dryRun= $argv[3] !== 'realRun';
	KalturaStatement::setDryRun($dryRun);
	if ($dryRun)
        	KalturaLog::debug('dry run --- in order to save, give realRun as a third parameter');
	
	$partnerIds = file($fileName);
	$partnerIds = array_map('trim' , $partnerIds);
	
	foreach($partnerIds as $partnerId){
		$partner = PartnerPeer::retrieveByPK($partnerId);
		if (!$partner)
		{
			KalturaLog::debug("ERR1 - partner $partnerId wasn\'t found");
			continue;
		}
	
		$partner->setDefThumbQuality($quality);
		KalturaLog::debug("saving partner id $partnerId");
		$partner->save();
	}

