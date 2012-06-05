<?php

$partnerId = -2;


require_once (dirname ( __FILE__ ) . '/../../bootstrap.php');


$objectsToTrack = array(
	KalturaAuditTrailObjectType::PARTNER => array(
		'actions' => array(
			KalturaAuditTrailAction::CHANGED,
		),
		'descriptors' => array(
			"extendedFreeTrailExpiryDate",
			"extendedFreeTrailExpiryReason",
			
		),
	)
);

kCurrentContext::$ps_vesion = 'ps3'; // used for different logic in alpha libs

$partner = PartnerPeer::retrieveByPK($partnerId);
$partner->setPluginEnabled(AuditPlugin::PLUGIN_NAME, true);
$partner->save();

foreach($objectsToTrack as $objectType => $objectConfig)
{
	$actions = implode(',', $objectConfig['actions']);
	$descriptors = isset($objectConfig['descriptors']) ? implode(',', $objectConfig['descriptors']) : null;
	
	$auditTrailConfig = AuditTrailConfigPeer::retrieveByObjectType($objectType, $partnerId);
	if(!$auditTrailConfig)
	{
		$auditTrailConfig = new AuditTrailConfig();
		$auditTrailConfig->setPartnerId($partnerId);
	}
		
	$auditTrailConfig->setObjectType($objectType);
	$auditTrailConfig->setActions($actions);
	$auditTrailConfig->setDescriptors($descriptors);
	$auditTrailConfig->save();
}
