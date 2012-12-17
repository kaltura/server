<?php
/**
 * Att Uverse Service
 *
 * @service attUverse
 * @package plugins.attUverseDistribution
 * @subpackage api.services
 */
class AttUverseService extends KalturaBaseService
{
	/**
	 * @action getFeed
	 * @disableTags TAG_WIDGET_SESSION,TAG_ENTITLEMENT_ENTRY,TAG_ENTITLEMENT_CATEGORY
	 * @param int $distributionProfileId
	 * @param string $hash
	 * @return file
	 */
	public function getFeedAction($distributionProfileId, $hash)
	{
		if (!$this->getPartnerId() || !$this->getPartner())
			throw new KalturaAPIException(KalturaErrors::INVALID_PARTNER_ID, $this->getPartnerId());
			
		$profile = DistributionProfilePeer::retrieveByPK($distributionProfileId);
		if (!$profile || !$profile instanceof AttUverseDistributionProfile)
			throw new KalturaAPIException(ContentDistributionErrors::DISTRIBUTION_PROFILE_NOT_FOUND, $distributionProfileId);

		if ($profile->getStatus() != KalturaDistributionProfileStatus::ENABLED)
			throw new KalturaAPIException(ContentDistributionErrors::DISTRIBUTION_PROFILE_DISABLED, $distributionProfileId);

		if ($profile->getUniqueHashForFeedUrl() != $hash)
			throw new KalturaAPIException(AttUverseDistributionErrors::INVALID_FEED_URL);

		// "Creates advanced filter on distribution profile
		$distributionAdvancedSearch = new ContentDistributionSearchFilter();
		$distributionAdvancedSearch->setDistributionProfileId($profile->getId());
		$distributionAdvancedSearch->setDistributionSunStatus(EntryDistributionSunStatus::AFTER_SUNRISE);
		$distributionAdvancedSearch->setEntryDistributionStatus(EntryDistributionStatus::READY);
		$distributionAdvancedSearch->setEntryDistributionFlag(EntryDistributionDirtyStatus::NONE);
		$distributionAdvancedSearch->setHasEntryDistributionValidationErrors(false);
			
		//Creates entry filter with advanced filter
		$entryFilter = new entryFilter();
		$entryFilter->setStatusEquel(entryStatus::READY);
		$entryFilter->setModerationStatusNot(entry::ENTRY_MODERATION_STATUS_REJECTED);
		$entryFilter->setPartnerSearchScope($this->getPartnerId());
		$entryFilter->setAdvancedSearch($distributionAdvancedSearch);
		
		$baseCriteria = KalturaCriteria::create(entryPeer::OM_CLASS);
		$baseCriteria->add(entryPeer::DISPLAY_IN_SEARCH, mySearchUtils::DISPLAY_IN_SEARCH_SYSTEM, Criteria::NOT_EQUAL);
		$entryFilter->attachToCriteria($baseCriteria);
		$entries = entryPeer::doSelect($baseCriteria);
		
		$feed = new AttUverseDistributionFeedHelper('feed_template.xml',$profile );
		$channelTitle = $profile->getChannelTitle();	
		$counter = 0;
		$profileUpdatedAt = $profile->getUpdatedAt(null);
		$cacheDir = kConf::get("global_cache_dir")."/feeds/dist_$distributionProfileId/";	
		foreach($entries as $entry)
		{
			// check cache
			$cacheFileName = $cacheDir.myContentStorage::dirForId($entry->getIntId(), $entry->getId().".xml");
			$updatedAt = max($profileUpdatedAt,  $entry->getUpdatedAt(null));
			if (file_exists($cacheFileName) && $updatedAt < filemtime($cacheFileName))
			{
				$xml = file_get_contents($cacheFileName);
			}
			else
			{
				/* @var $entry entry */
				/* @var $entryDistribution Entrydistribution */
				$entryDistribution = EntryDistributionPeer::retrieveByEntryAndProfileId($entry->getId(), $profile->getId());
				if (!$entryDistribution)
				{
					KalturaLog::err('Entry distribution was not found for entry ['.$entry->getId().'] and profile [' . $profile->getId() . ']');
					continue;
				}
				$fields = $profile->getAllFieldValues($entryDistribution);
				
				//flavors assets and remote flavor asset file urls			
				$flavorAssets = assetPeer::retrieveByIds(explode(',', $entryDistribution->getFromCustomData(AttUverseEntryDistributionCustomDataField::DISTRIBUTED_FLAVOR_IDS)));
				$remoteAssetFileUrls = unserialize($entryDistribution->getFromCustomData(AttUverseEntryDistributionCustomDataField::REMOTE_ASSET_FILE_URLS));
				
				//thumb assets and remote thumb asset file urls			
				$thumbAssets = assetPeer::retrieveByIds(explode(',', $entryDistribution->getFromCustomData(AttUverseEntryDistributionCustomDataField::DISTRIBUTED_THUMBNAIL_IDS)));
				$remoteThumbailFileUrls = unserialize($entryDistribution->getFromCustomData(AttUverseEntryDistributionCustomDataField::REMOTE_THUMBNAIL_FILE_URLS));
				
				//thumb assets and remote thumb asset file urls			
				$captionAssets = assetPeer::retrieveByIds(explode(',', $entryDistribution->getFromCustomData(AttUverseEntryDistributionCustomDataField::DISTRIBUTED_CAPTION_IDS)));
				$xml = $feed->getItemXml($fields, $flavorAssets, $remoteAssetFileUrls, $thumbAssets, $remoteThumbailFileUrls, $captionAssets);
				mkdir(dirname($cacheFileName), 0750, true);
				file_put_contents($cacheFileName, $xml);
			}
			$feed->addItemXml($xml);
			$counter++;
			//to avoid the cache exceeding the memory size 
			if ($counter >= 100){
				kMemoryManager::clearMemory();
				$counter = 0;
			}
		}
		
		//set channel title
		if (isset($fields))
		{
			$channelTitle = $fields[AttUverseDistributionField::CHANNEL_TITLE];
		}
		$feed->setChannelTitle($channelTitle);
		header('Content-Type: text/xml');
		echo str_replace('&','&amp;',html_entity_decode($feed->getXml(),ENT_QUOTES,'UTF-8'));
		die;
	}
}
