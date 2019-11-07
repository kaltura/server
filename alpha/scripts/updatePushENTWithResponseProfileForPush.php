<?php

ini_set("memory_limit","256M");
require_once(__DIR__ . '/bootstrap.php');

if($argc < 4)
{
	echo "Arguments missing.\n\n";
	echo "Usage: php " . __FILE__ . " {event_notification_profile_ids_file_path} {responseProfileId} {newResponseProfileId} <realrun / dryrun> \n";
	exit;
}

$entNotificationPorfilesIdsFilePath = $argv[1];
$allEnts = file ( $entNotificationPorfilesIdsFilePath ) or die ( 'Could not read file!' );

$responseProfileId = $argv[2];
$newResponseProfileId = $argv[3];
$dryRun = true;
if ($argc > 4)
{
	$dryRun = $argv[4] != 'realrun';
}
KalturaLog::debug('dry run ['.print_r($dryRun,true).']');

KalturaStatement::setDryRun($dryRun);

foreach ($allEnts as $entId)
{
	$entId = trim($entId);
	EventNotificationTemplatePeer::setUseCriteriaFilter(false);
	$ent = EventNotificationTemplatePeer::retrieveByPK($entId);
	if (!$ent)
	{
		KalturaLog::warning('could not find Event notification template with id ['. $entId . ']');
		continue;
	}
	if ($ent->getType() != PushNotificationPlugin::getPushNotificationTemplateTypeCoreValue(PushNotificationTemplateType::PUSH))
	{
		KalturaLog::warning('Event notificaiton template wrong type ['. $entId .']');
		continue;
	}
	/** @var PushNotificationTemplate $ent */
	if ($ent->getResponseProfileId() == $responseProfileId)
	{
		$ent->setResponseProfileId($newResponseProfileId);
		KalturaLog::log('Going to change event notification template [' . $ent->getId() . ']');
		$ent->save();
	}
}


?>
