<?php
/**
 * Uverse Click To Order Service
 *
 * @service uverseClickToOrder
 * @package plugins.uverseClickToOrderDistribution
 * @subpackage api.services
 */
class UverseClickToOrderService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
	}
	
	/**
	 * @action getFeed
	 * @param int $distributionProfileId
	 * @param string $hash
	 * @return file
	 */
	public function getFeedAction($distributionProfileId, $hash)
	{
		if (!$this->getPartnerId() || !$this->getPartner())
			throw new KalturaAPIException(KalturaErrors::INVALID_PARTNER_ID, $this->getPartnerId());
			
		$profile = DistributionProfilePeer::retrieveByPK($distributionProfileId);
		if (!$profile || !$profile instanceof UverseClickToOrderDistributionProfile)
			throw new KalturaAPIException(ContentDistributionErrors::DISTRIBUTION_PROFILE_NOT_FOUND, $distributionProfileId);

		if ($profile->getStatus() != KalturaDistributionProfileStatus::ENABLED)
			throw new KalturaAPIException(ContentDistributionErrors::DISTRIBUTION_PROFILE_DISABLED, $distributionProfileId);

		if ($profile->getUniqueHashForFeedUrl() != $hash)
			throw new KalturaAPIException(UverseClickToOrderDistributionErrors::INVALID_FEED_URL);
		
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
		$entryFilter->setPartnerIdEquel($this->getPartnerId());
		$entryFilter->setAdvancedSearch($distributionAdvancedSearch);
		
		$baseCriteria = KalturaCriteria::create(entryPeer::OM_CLASS);
		$entryFilter->attachToCriteria($baseCriteria);
		$entries = entryPeer::doSelect($baseCriteria);

		$feed = new UverseClickToOrderFeed('feed_template.xml');
		$feed->setDistributionProfile($profile);
		
		//setting background images
		$wideBackgroundImageEntryId = $profile->getBackgroundImageWide();
		$standardBackgroundImageEntryId = $profile->getBackgroundImageStandard();	
		$widedBackgroundImageUrl = $this->getImageUrl($wideBackgroundImageEntryId, '854', '480');
		$standardBackgroundImageUrl = $this->getImageUrl($standardBackgroundImageEntryId, '640', '480');		
		$feed->setBackgroudImage($widedBackgroundImageUrl,$standardBackgroundImageUrl);
		
		//getting array of all related entries (categories that will appear in the xml)
		$relatedEntriesArray = array();
		//going through all entries and preparing an array with all related entries (categories) directing to the entires 
		foreach($entries as $entry)
		{
			$entryDistribution = EntryDistributionPeer::retrieveByEntryAndProfileId($entry->getId(), $profile->getId());
			if (!$entryDistribution)
			{
				KalturaLog::err('Entry distribution was not found for entry ['.$entry->getId().'] and profile [' . $profile->getId() . ']');
				continue;
			}					
			$fields = $profile->getAllFieldValues($entryDistribution);
			$relatedEntryId = $fields[UverseClickToOrderDistributionField::CATEGORY_ENTRY_ID];
			$relatedEntriesArray[$relatedEntryId] .= $entry->getId().'---'.$entry->getThumbnailUrl().'---'.$entry->getDownloadUrl().',';		
				
		}
		//retreive each category and add it to the xml
		foreach ($relatedEntriesArray as $relatedEntryId => $entriesList)
		{
			//getting the related entry id object
			$c = new Criteria();
			$c->addAnd(entryPeer::ID, $relatedEntryId, Criteria::EQUAL);
			$relatedEntryObject = entryPeer::doSelect($c);	
			if (!$relatedEntryObject)
			{
				KalturaLog::err('Related Entry ['.$relatedEntryId.'] was not found for entry ['.$entry->getId().'] and profile [' . $profile->getId() . ']');
				continue;
			}
			$categoryName = $relatedEntryObject[0]->getName();
			$categoryFile = $relatedEntryObject[0]->getThumbnailUrl();
			$categoryNode = $feed->addCategory($categoryName, $categoryFile);
			$entriesUnderCategory = explode(',', trim($entriesList, ','));
			//getting all entries under a category
			foreach ($entriesUnderCategory as $entryUnderCategory)
			{	
				//getting entry info				
				$entryInfo = explode('---', $entryUnderCategory);
				$entryId = $entryInfo[0];
				$thumbnailFile = $entryInfo[1];
				$flavorFile =  $entryInfo[2];
				//getting entry's fileds array
				$entryDistribution = EntryDistributionPeer::retrieveByEntryAndProfileId($entryId, $profile->getId());									
				$fields = $profile->getAllFieldValues($entryDistribution);
				$feed->addItem($fields, $categoryNode, $thumbnailFile, $flavorFile);
			}
		}		
		header('Content-Type: text/xml');
		echo $feed->getXml();
		die;
	}
	
	private function getImageUrl($entryId, $width = null, $height = null)
	{
		$c = new Criteria();
		$c->addAnd(entryPeer::ID, $entryId, Criteria::EQUAL);
		$backgroundImage = entryPeer::doSelect($c);		
		if (!$backgroundImage)
		{
			KalturaLog::err('Related Entry ['.$entryId.'] was not found ');
			return '';
		}
		if ($backgroundImage[0]->getMediaType() == KalturaMediaType::IMAGE ){
			$backgroundImageUrl = $backgroundImage[0]->getDownloadUrl();		
		}
		else{
			$backgroundImageUrl = $backgroundImage[0]->getThumbnailUrl().'/width/'.$width.'/height/'.$height;
		}
		return $backgroundImageUrl;
	}
}
