<?php

ini_set("memory_limit","256M");
require_once(__DIR__ . '/bootstrap.php');

if($argc < 3)
{
	echo "Arguments missing.\n\n";
	echo "Usage: php " . __FILE__ . " {responseProfileId} {newResponseProfileId} <realrun / dryrun> \n";
	exit;
}

$responseProfileId = $argv[1];
$newResponseProfileId = $argv[2];
$dryRun = true;
if ($argc > 3)
{
	$dryRun = $argv[3] != 'realrun';
}
KalturaLog::debug('dry run ['.print_r($dryRun,true).']');

KalturaStatement::setDryRun($dryRun);

//SELECT id,partner_id,system_name FROM event_notification_template WHERE type = '12722' AND custom_data like '%16922%';


$criteria = new Criteria();
$criteria->add(EventNotificationTemplatePeer::TYPE, PushNotificationPlugin::getPushNotificationTemplateTypeCoreValue(PushNotificationTemplateType::PUSH));
$allEnts = EventNotificationTemplatePeer::doSelect($criteria);

foreach ($allEnts as $ent)
{
	/** @var PushNotificationTemplate $ent */
	//if ($ent->getResponseProfileId() == '16922')//prod
	if ($ent->getResponseProfileId() == $responseProfileId) // QA
	{
		$ent->setResponseProfileId($newResponseProfileId); //QA
		KalturaLog::log('Going to change event notification template [' . $ent->getId() . ']');
		$ent->save();
	}
}


?>
