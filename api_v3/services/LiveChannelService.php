<?php

/**
 * Live Channel service lets you manage live channels
 *
 * @service liveChannel
 * @package api
 * @subpackage services
 */
class LiveChannelService extends KalturaLiveEntryService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);

		if($this->getPartnerId() > 0 && !PermissionPeer::isValidForPartner(PermissionName::FEATURE_LIVE_CHANNEL, $this->getPartnerId()))
			throw new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN, $this->serviceName.'->'.$this->actionName);
	}
	
	/**
	 * Adds new live channel.
	 * 
	 * @action add
	 * @param KalturaLiveChannel $liveChannel Live channel metadata  
	 * @return KalturaLiveChannel The new live channel
	 */
	function addAction(KalturaLiveChannel $liveChannel)
	{
		$dbEntry = $this->prepareEntryForInsert($liveChannel);
		$dbEntry->save();
		
		$te = new TrackEntry();
		$te->setEntryId($dbEntry->getId());
		$te->setTrackEventTypeId(TrackEntry::TRACK_ENTRY_EVENT_TYPE_ADD_ENTRY);
		$te->setDescription(__METHOD__ . ":" . __LINE__ . "::LIVE_CHANNEL");
		TrackEntry::addTrackEntry($te);
		
		$liveChannel = new KalturaLiveChannel();
		$liveChannel->fromObject($dbEntry, $this->getResponseProfile());
		return $liveChannel;
	}

	
	/**
	 * Get live channel by ID.
	 * 
	 * @action get
	 * @param string $id Live channel id
	 * @return KalturaLiveChannel The requested live channel
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 */
	function getAction($id)
	{
		return $this->getEntry($id, -1, KalturaEntryType::LIVE_CHANNEL);
	}

	
	/**
	 * Update live channel. Only the properties that were set will be updated.
	 * 
	 * @action update
	 * @param string $id Live channel id to update
	 * @param KalturaLiveChannel $liveChannel Live channel metadata to update
	 * @return KalturaLiveChannel The updated live channel
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @validateUser entry id edit
	 */
	function updateAction($id, KalturaLiveChannel $liveChannel)
	{
		return $this->updateEntry($id, $liveChannel, KalturaEntryType::LIVE_CHANNEL);
	}

	/**
	 * Delete a live channel.
	 *
	 * @action delete
	 * @param string $id Live channel id to delete
	 * 
 	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
 	 * @validateUser entry id edit
	 */
	function deleteAction($id)
	{
		$this->deleteEntry($id, KalturaEntryType::LIVE_CHANNEL);
	}
	
	/**
	 * List live channels by filter with paging support.
	 * 
	 * @action list
     * @param KalturaLiveChannelFilter $filter live channel filter
	 * @param KalturaFilterPager $pager Pager
	 * @return KalturaLiveChannelListResponse Wrapper for array of live channels and total count
	 */
	function listAction(KalturaLiveChannelFilter $filter = null, KalturaFilterPager $pager = null)
	{
	    if (!$filter)
			$filter = new KalturaLiveChannelFilter();
			
	    $filter->typeEqual = KalturaEntryType::LIVE_CHANNEL;
	    list($list, $totalCount) = parent::listEntriesByFilter($filter, $pager);
	    
	    $newList = KalturaLiveChannelArray::fromDbArray($list, $this->getResponseProfile());
		$response = new KalturaLiveChannelListResponse();
		$response->objects = $newList;
		$response->totalCount = $totalCount;
		return $response;
	}
	
	/**
	 * Delivering the status of a live channel (on-air/offline)
	 * 
	 * @action isLive
	 * @param string $id ID of the live channel
	 * @return bool
	 * @ksOptional
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 */
	public function isLiveAction ($id)
	{
		$dbEntry = entryPeer::retrieveByPK($id);

		if (!$dbEntry || $dbEntry->getType() != KalturaEntryType::LIVE_CHANNEL)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $id);

		return $dbEntry->isCurrentlyLive();
	}
}