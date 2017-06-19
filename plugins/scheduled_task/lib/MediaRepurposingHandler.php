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
			$templateMDPForMR = MetadataProfilePeer::retrieveBySystemName('MRP', MediaRepurposingUtils::ADMIN_CONSOLE_PARTNER);
			if ($templateMDPForMR) {
				$newMDP = $templateMDPForMR->copyToPartner($partnerId);
				$newMDP->save();
			}
		}
	}
}