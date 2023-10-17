<?php
/**
 * @package deployment
 *
 * Deploy new Azerbaijani and Urdu language flavors for live
 */
require_once (__DIR__ . '/../../bootstrap.php');

$kConfMaxMetadataIndexLength = kConf::get('max_metadata_index_length', 'elasticDynamicMap', array());
foreach ($kConfMaxMetadataIndexLength as $partnerId => $maxMetadataIndexLength)
{
	$partnerId = trim($partnerId);
	$partner = PartnerPeer::retrieveByPK($partnerId);
	if(!$partner)
	{
		KalturaLog:debug("Failed to find partnerId []$partnerId");
		continue;
	}

	$partner->setSearchMaxMetadataIndexLength($maxMetadataIndexLength);
	$partner->save();
}
