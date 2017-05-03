<?php

/**
 * @package plugins.schedule_task
 * @subpackage Admin
 */
class MediaRepurposingHandler
{

	public static function enableMrPermission($partnerId)
	{
		if (!MetadataProfilePeer::retrieveBySystemName('MRP', $partnerId)) {
			KalturaLog::info("NO MDP on partner [$partnerId] - cloning from partner 0");
	
			$md0 = MetadataProfilePeer::retrieveBySystemName('MRP', -2); //as template for the MR mechanism
			$newMDP = $md0->copy(true);
			$key = $md0->getSyncKey(MetadataProfile::FILE_SYNC_METADATA_DEFINITION);
			$newMDP->setXsdData(kFileSyncUtils::file_get_contents($key, true, false));
			$newMDP->setPartnerId($partnerId);
	
			$newMDP->save();
	}
}
}