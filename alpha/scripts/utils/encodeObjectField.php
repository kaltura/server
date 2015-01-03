<?php
// this chdir can be changed according to environment
chdir(__DIR__ . '/../');
require_once 'bootstrap.php';

if($argc != 6){
    KalturaLog::debug ("Usage: php encodeObjectField.php [partner_id] [object_id] [object_type] [field_name] [realRun]");
    die("Not enough parameters" . PHP_EOL);
}

$partner_id = $argv[1];
$object_id =  $argv[2];	
$object_type = $argv[3];
$field_name = $argv[4];

//should the script save() ? by default will not save
$dryRun= $argv[5] !== 'realRun';
KalturaStatement::setDryRun($dryRun);
if ($dryRun)
	KalturaLog::debug('dry run --- in order to save, give dryRun as a second parameter');

//this is the addition to the original file fileSyncPendingStatusCorrection.php
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
$encodedFieldValue = utf8_encode($fieldValue);
call_user_func_array( $setter_callback , array($encodedFieldValue));

$object->save();

