<?php
	// this chdir can be changed according to environment
	chdir(__DIR__ . '/../');
	require_once(__DIR__ . '/../bootstrap.php');
	
	if($argc < 7){
        KalturaLog::debug ("Usage: [real_run] [partner_id] [object_id] [object_type] [field_name] [value]");
        die("Not enough parameters");
	}
	
	//should the script save() ? by default will not save
	$dry_run= $argv[1] !== 'real_run';
	KalturaStatement::setDryRun($dry_run);
	if ($dry_run)
        KalturaLog::debug('>>>dry run --- in order to save, give real_run as a second parameter');
	
	$partner_id = $argv[2];
	$object_id =  $argv[3];	
	$object_type = $argv[4];
	$field_name = $argv[5];
	$value = $argv[6];
	
	//this is the addition to the original file fileSyncPendingStatusCorrection.php
	//$fileSyncs=retrievePendingFileSyncs();
	$peer = "{$object_type}Peer";	

	$peer::setUseCriteriaFilter(false);
	$object = $peer::retrieveByPK($object_id);
	$peer::setUseCriteriaFilter(true);
	if ($object->getPartnerId() != $partner_id )
		die ('wrong partner !');
			
	$getter_callback = array($object ,"get{$field_name}");
	$setter_callback = array($object ,"set{$field_name}");

	if (!is_callable($getter_callback) || !is_callable($setter_callback))
		die ('missing getter/setter');

	$fieldValue = call_user_func_array( $getter_callback , array());
	KalturaLog::debug('old value - ' . $fieldValue);
	KalturaLog::debug('new value - ' . $value);
	$tmp = call_user_func_array( $setter_callback ,  array($value));

	$object->save();