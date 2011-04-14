<?php
class kContentDistributionObjectCopiedHandler implements kObjectCopiedEventConsumer
{
	/**
	 * @param BaseObject $fromObject
	 * @param BaseObject $toObject
	 * @return bool true if should continue to the next consumer
	 */
	public function objectCopied(BaseObject $fromObject, BaseObject $toObject)
	{
		if($fromObject instanceof Partner)
			$this->copyDistributionProfiles($fromObject->getId(), $toObject->getId());
		
		return true;
	}
	
	/**
	 * @param int $fromPartnerId
	 * @param int $toPartnerId
	 */
	protected function copyDistributionProfiles($fromPartnerId, $toPartnerId)
	{
		KalturaLog::debug("Copy distribution profiles from [$fromPartnerId] to [$toPartnerId]");
		
 		$c = new Criteria();
 		$c->add(DistributionProfilePeer::PARTNER_ID, $fromPartnerId);
 		
 		$distributionProfiles = DistributionProfilePeer::doSelect($c);
 		foreach($distributionProfiles as $distributionProfile)
 		{
 			$newDistributionProfile = $distributionProfile->copy();
 			$newDistributionProfile->setPartnerId($toPartnerId);
 			$newDistributionProfile->save();
 			
 			kFileSyncUtils::createSyncFileLinkForKey(
 				$newDistributionProfile->getSyncKey(DistributionProfile::FILE_SYNC_DISTRIBUTION_PROFILE_CONFIG),
 				$distributionProfile->getSyncKey(DistributionProfile::FILE_SYNC_DISTRIBUTION_PROFILE_CONFIG),
 				false
 			);
 		}
	}
}