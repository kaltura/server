<?php

/**
 * Manage live channel segments
 *
 * @service liveChannelSegment
 */
class LiveChannelSegmentService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		$this->applyPartnerFilterForClass('LiveChannelSegment'); 	
		
		if(!PermissionPeer::isValidForPartner(PermissionName::FEATURE_LIVE_CHANNEL, $this->getPartnerId()))
			throw new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN, $this->serviceName.'->'.$this->actionName);
	}
	
	/**
	 * Add new live channel segment
	 * 
	 * @action add
	 * @param KalturaLiveChannelSegment $liveChannelSegment
	 * @return KalturaLiveChannelSegment
	 */
	function addAction(KalturaLiveChannelSegment $liveChannelSegment)
	{
		$dbLiveChannelSegment = $liveChannelSegment->toInsertableObject();
		$dbLiveChannelSegment->setPartnerId($this->getPartnerId());
		$dbLiveChannelSegment->setStatus(LiveChannelSegmentStatus::ACTIVE);
		$dbLiveChannelSegment->save();
		
		$liveChannelSegment = new KalturaLiveChannelSegment();
		$liveChannelSegment->fromObject($dbLiveChannelSegment, $this->getResponseProfile());
		return $liveChannelSegment;
	}
	
	/**
	 * Get live channel segment by id
	 * 
	 * @action get
	 * @param bigint $id
	 * @return KalturaLiveChannelSegment
	 * 
	 * @throws KalturaErrors::LIVE_CHANNEL_SEGMENT_ID_NOT_FOUND
	 */
	function getAction($id)
	{
		$dbLiveChannelSegment = LiveChannelSegmentPeer::retrieveByPK($id);
		if (!$dbLiveChannelSegment)
			throw new KalturaAPIException(KalturaErrors::LIVE_CHANNEL_SEGMENT_ID_NOT_FOUND, $id);
			
		$liveChannelSegment = new KalturaLiveChannelSegment();
		$liveChannelSegment->fromObject($dbLiveChannelSegment, $this->getResponseProfile());
		return $liveChannelSegment;
	}
	
	/**
	 * Update live channel segment by id
	 * 
	 * @action update
	 * @param bigint $id
	 * @param KalturaLiveChannelSegment $liveChannelSegment
	 * @return KalturaLiveChannelSegment
	 * 
	 * @throws KalturaErrors::LIVE_CHANNEL_SEGMENT_ID_NOT_FOUND
	 */
	function updateAction($id, KalturaLiveChannelSegment $liveChannelSegment)
	{
		$dbLiveChannelSegment = LiveChannelSegmentPeer::retrieveByPK($id);
		if (!$dbLiveChannelSegment)
			throw new KalturaAPIException(KalturaErrors::LIVE_CHANNEL_SEGMENT_ID_NOT_FOUND, $id);
		
		$liveChannelSegment->toUpdatableObject($dbLiveChannelSegment);
		$dbLiveChannelSegment->save();
		
		$liveChannelSegment = new KalturaLiveChannelSegment();
		$liveChannelSegment->fromObject($dbLiveChannelSegment, $this->getResponseProfile());
		return $liveChannelSegment;
	}
	
	/**
	 * Delete live channel segment by id
	 * 
	 * @action delete
	 * @param bigint $id
	 * 
	 * @throws KalturaErrors::LIVE_CHANNEL_SEGMENT_ID_NOT_FOUND
	 */
	function deleteAction($id)
	{
		$dbLiveChannelSegment = LiveChannelSegmentPeer::retrieveByPK($id);
		if (!$dbLiveChannelSegment)
			throw new KalturaAPIException(KalturaErrors::LIVE_CHANNEL_SEGMENT_ID_NOT_FOUND, $id);

		$dbLiveChannelSegment->setStatus(LiveChannelSegmentStatus::DELETED);
		$dbLiveChannelSegment->save();
	}
	
	/**
	 * List live channel segments by filter and pager
	 * 
	 * @action list
	 * @param KalturaFilterPager $filter
	 * @param KalturaLiveChannelSegmentFilter $pager
	 * @return KalturaLiveChannelSegmentListResponse
	 */
	function listAction(KalturaLiveChannelSegmentFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaLiveChannelSegmentFilter();
			
		if (!$pager)
			$pager = new KalturaFilterPager();
			
		return $filter->getListResponse($pager, $this->getResponseProfile());
	}
}