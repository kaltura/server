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
			KalturaLog::info("NO MDP on partner [$partnerId] - cloning from admin-console partner");
			$adminConsolePartner = MediaRepurposingUtils::ADMIN_CONSOLE_PARTNER;
			$templateMDPForMR = MetadataProfilePeer::retrieveBySystemName('MRP', $adminConsolePartner); //as template for the MR mechanism
			$newMDP = $templateMDPForMR->copy(true);
			$key = $templateMDPForMR->getSyncKey(MetadataProfile::FILE_SYNC_METADATA_DEFINITION);
			$newMDP->setXsdData(kFileSyncUtils::file_get_contents($key, true, false));
			$newMDP->setPartnerId($partnerId);
	
			$newMDP->save();
	}
}
}