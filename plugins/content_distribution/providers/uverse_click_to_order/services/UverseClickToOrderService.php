<?php
/**
 * Uverse Click To Order Service
 *
 * @service uverseClickToOrder
 * @package plugins.uverseClickToOrderDistribution
 * @subpackage api.services
 */
class UverseClickToOrderService extends ContentDistributionServiceBase
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
		$this->generateFeed(new ContentDistributionServiceContext(), $distributionProfileId, $hash);
	}
	
	public function getProfileClass() {
		return new UverseClickToOrderDistributionProfile();
	}
	
	protected function createFeedGenerator($context) {
		$feed = new UverseClickToOrderFeed('feed_template.xml');
		$feed->setDistributionProfile($this->profile);
		$this->initFeedGeneration($feed);
		return $feed;
	}
	
	protected function initFeedGeneration ($feed) {
		//setting background images
		$wideBackgroundImageEntryId = $this->profile->getBackgroundImageWide();
		$standardBackgroundImageEntryId = $this->profile->getBackgroundImageStandard();
		$widedBackgroundImageUrl = $this->getImageUrl($wideBackgroundImageEntryId, '854', '480');
		$standardBackgroundImageUrl = $this->getImageUrl($standardBackgroundImageEntryId, '640', '480');
		$feed->setBackgroudImage($widedBackgroundImageUrl,$standardBackgroundImageUrl);
	}
	
	protected function handleEntries($context, $feed, array $entries)
	{
		//getting array of all related entries (categories that will appear in the xml)
		$relatedEntriesArray = array();
		//going through all entries and preparing an array with all related entries (categories) directing to the entires 
		foreach($entries as $entry)
		{
			$entryDistribution = EntryDistributionPeer::retrieveByEntryAndProfileId($entry->getId(), $this->profile->getId());
			if (!$entryDistribution)
			{
				KalturaLog::err('Entry distribution was not found for entry ['.$entry->getId().'] and profile [' . $this->profile->getId() . ']');
				continue;
			}					
			$fields = $this->profile->getAllFieldValues($entryDistribution);
			$relatedEntryId = $fields[UverseClickToOrderDistributionField::CATEGORY_ENTRY_ID];
			
			if (!isset($relatedEntriesArray[$relatedEntryId])) {
				$relatedEntry = entryPeer::retrieveByPK($relatedEntryId);
				$relatedEntrySortValue = $this->getRelatedEntrySortValue($this->profile, $relatedEntryId);
				$relatedEntriesArray[$relatedEntryId] = array();
				$relatedEntriesArray[$relatedEntryId]['sortValue'] = $relatedEntrySortValue;
				$relatedEntriesArray[$relatedEntryId]['updatedAt'] = $relatedEntry->getUpdatedAt(); 
				$relatedEntriesArray[$relatedEntryId]['relatedEntryId'] = $relatedEntryId;
			}
			
			$flavorAssets = array_map('trim', explode(',', $entryDistribution->getFlavorAssetIds()));
			$flavorAssetId = isset($flavorAssets[0]) ? $flavorAssets[0] : null;
			$flavorAsset = assetPeer::retrieveById($flavorAssetId);
			$flavorUrl = $flavorAsset ? $flavorAsset->getDownloadUrl() : $entry->getDownloadUrl();
			
			$thumbAssets = array_map('trim', explode(',', $entryDistribution->getThumbAssetIds()));
			$thumbAssetId = isset($thumbAssets[0]) ? $thumbAssets[0] : null;
			$thumbAsset = assetPeer::retrieveById($thumbAssetId);
			$thumbUrl = $thumbAsset ? $thumbAsset->getDownloadUrl() : $entry->getThumbnailUrl();
			
			$relatedEntriesArray[$relatedEntryId][] = array(
				'id' => $entry->getId(),
				'thumbnailUrl' => $thumbUrl,
				'downloadUrl' => $flavorUrl,
				'updatedAt' =>  $entry->getUpdatedAt(),
				'sortValue' => $this->profile->getFieldValue($entryDistribution, UverseClickToOrderDistributionField::SORT_ITEMS_BY_FIELD),
			);
			
		}
		//sorting the related entries.
		usort($relatedEntriesArray, Array($this,'sortItems'));
		
		//removing the values that where used for sorting.
		foreach ($relatedEntriesArray as $key=>$relatedEntry){
			$relatedEntryId = $relatedEntry['relatedEntryId'];
			unset($relatedEntry['relatedEntryId']);
			unset($relatedEntry['sortValue']);
			unset($relatedEntry['updatedAt']);
			unset($relatedEntriesArray[$key]);
			$relatedEntriesArray[$relatedEntryId] = $relatedEntry;
		}
		
		//retreive each category and add it to the xml
		foreach ($relatedEntriesArray as $relatedEntryId => $entriesUnderCategory)
		{
			//getting the related entry id object
			$relatedEntryObject = entryPeer::retrieveByPK($relatedEntryId);	
			if (!$relatedEntryObject)
			{
				KalturaLog::err('Related Entry ['.$relatedEntryId.'] was not found');
				continue;
			}
			$categoryName = $relatedEntryObject->getName();
			$categoryFile = $relatedEntryObject->getThumbnailUrl().'/width/'.$fields[UverseClickToOrderDistributionField::CATEGORY_IMAGE_WIDTH].'/height/'.$fields[UverseClickToOrderDistributionField::CATEGORY_IMAGE_HEIGHT];
			$categoryNode = $feed->addCategory($categoryName, $categoryFile);
			usort($entriesUnderCategory, Array($this,'sortItems'));
			
			//getting all entries under a category
			foreach ($entriesUnderCategory as $entryInfo)
			{	
				$entryId = $entryInfo['id'];
				$thumbnailFile = $entryInfo['thumbnailUrl'];
				$flavorFile =  $entryInfo['downloadUrl'];
				//getting entry's fileds array
				$entryDistribution = EntryDistributionPeer::retrieveByEntryAndProfileId($entryId, $this->profile->getId());									
				$fields = $this->profile->getAllFieldValues($entryDistribution);
				$feed->addItem($fields, $categoryNode, $thumbnailFile, $flavorFile);
			}
		}		
	}	
	
	protected function handleEntry($context, $feed, entry $entry, Entrydistribution $entryDistribution) {
		// Empty implementation;
	}
		
	private function getRelatedEntrySortValue($profile , $relatedEntryId){
		$relatedEntrydistribution = new EntryDistribution();
		$relatedEntrydistribution->setEntryId($relatedEntryId);
		$relatedEntrydistribution->setPartnerId($profile->getPartnerId());
		$relatedEntrydistribution->setDistributionProfileId($profile->getId());
		return $profile->getFieldValue($relatedEntrydistribution, UverseClickToOrderDistributionField::SORT_ITEMS_BY_FIELD);
	}
	
	
	//sorting the entries inside a category using 'sortValue' as primary comparison and 'updatedAt' as secondary comparison. 
	private function sortItems($lEntryInfo,$rEntryInfo){
		if (isset($lEntryInfo['sortValue']) &&  isset($rEntryInfo['sortValue'])) {
            if ($lEntryInfo['sortValue'] == $rEntryInfo['sortValue'])
				return $this->sortItemsByUpdatedAt($lEntryInfo,$rEntryInfo);
    		return ($lEntryInfo['sortValue'] < $rEntryInfo['sortValue']) ? -1 : 1;
    	}
    	return $this->sortItemsByUpdatedAt($lEntryInfo,$rEntryInfo);
	}
	
	private function sortItemsByUpdatedAt($lEntryInfo,$rEntryInfo){
		if ($lEntryInfo['updatedAt'] == $rEntryInfo['updatedAt'])
			return 0;
    	return ($lEntryInfo['updatedAt'] < $rEntryInfo['updatedAt']) ? -1 : 1;
	}
	
	private function getImageUrl($entryId, $width = null, $height = null)
	{
		$backgroundImage = entryPeer::retrieveByPK($entryId);		
		if (!$backgroundImage)
		{
			KalturaLog::err('Image entry ['.$entryId.'] was not found');
			return '';
		}
		if ($backgroundImage->getMediaType() == KalturaMediaType::IMAGE){
			$backgroundImageUrl = $backgroundImage->getDownloadUrl();		
		}
		else{
			$backgroundImageUrl = $backgroundImage->getThumbnailUrl().'/width/'.$width.'/height/'.$height;
		}
		return $backgroundImageUrl;
	}
}
