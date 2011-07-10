<?php
/**
 * Cue Point service
 *
 * @service cuePoint
 * @package plugins.cuePoint
 * @subpackage api.services
 * @throws KalturaErrors::SERVICE_FORBIDDEN
 */
class CuePointService extends KalturaBaseService
{
	/**
	 * @return CuePointType or null to limit the service type
	 */
	protected function getCuePointType()
	{
		return null;
	}
	
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		myPartnerUtils::addPartnerToCriteria(new CuePointPeer(), $this->getPartnerId(), $this->private_partner_data, $this->partnerGroup());

		// when session is not admin, allow access to user entries only
		if (!$this->getKs() || !$this->getKs()->isAdmin())
			CuePointPeer::setDefaultCriteriaFilterByKuser();
		
		if(!CuePointPlugin::isAllowedPartner($this->getPartnerId()))
			throw new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN, $this->serviceName.'->'.$this->actionName);
	}
	
	/**
	 * Allows you to add an cue point object associated with an entry
	 * 
	 * @action add
	 * @param KalturaCuePoint $cuePoint
	 * @return KalturaCuePoint
	 */
	function addAction(KalturaCuePoint $cuePoint)
	{
		$dbCuePoint = $cuePoint->toInsertableObject();
		$dbCuePoint->setPartnerId($this->getPartnerId());
		$dbCuePoint->setStatus(CuePointStatus::READY); 
		$dbCuePoint->setKuserId($this->getKuser()->getId());
					
		if($this->getCuePointType())
			$dbCuePoint->setType($this->getCuePointType());
			
		$created = $dbCuePoint->save();
		if(!$created)
			return null;
		
		$cuePoint = KalturaCuePoint::getInstance($dbCuePoint->getType());
		if(!$cuePoint)
			return null;
			
		$cuePoint->fromObject($dbCuePoint);
		return $cuePoint;
	}
	
	/**
	 * Allows you to add multiple cue points objects by uploading XML that contains multiple cue point definitions
	 * 
	 * @action addFromBulk
	 * @param file $fileData
	 * @return KalturaCuePointListResponse
	 * @todo
	 */
	function addFromBulkAction($fileData)
	{
	}
	
	/**
	 * Download multiple cue points objects as XML definitions
	 * 
	 * @action serveBulk
	 * @param KalturaCuePointFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return file
	 * @todo
	 */
	function serveBulkAction(KalturaCuePointFilter $filter = null, KalturaFilterPager $pager = null)
	{
	}
	
	/**
	 * Retrieve an CuePoint object by id
	 * 
	 * @action get
	 * @param string $id 
	 * @return KalturaCuePoint
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */		
	function getAction($id)
	{
		$dbCuePoint = CuePointPeer::retrieveByPK( $id );

		if(!$dbCuePoint)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $id);
			
		if($this->getCuePointType() && $dbCuePoint->getType() != $this->getCuePointType())
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $id);
			
		$cuePoint = KalturaCuePoint::getInstance($dbCuePoint->getType());
		if(!$cuePoint)
			return null;
			
		$cuePoint->fromObject($dbCuePoint);
		return $cuePoint;
	}
	
	/**
	 * List cue point objects by filter and pager
	 * 
	 * @action list
	 * @param KalturaCuePointFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaCuePointListResponse
	 */
	function listAction(KalturaCuePointFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaCuePointFilter();
			
		$c = KalturaCriteria::create(CuePointPeer::OM_CLASS);
		if($this->getCuePointType())
			$c->add(CuePointPeer::TYPE, $this->getCuePointType());
		
		$cuePointFilter = $filter->toObject();
		
		$cuePointFilter->attachToCriteria($c);
		if ($pager)
			$pager->attachToCriteria($c);
			
		$list = CuePointPeer::doSelect($c);
		
		$response = new KalturaCuePointListResponse();
		$response->objects = KalturaCuePointArray::fromDbArray($list);
		$response->totalCount = $c->getRecordsCount();
	
		return $response;
	}
	
	/**
	 * count cue point objects by filter
	 * 
	 * @action count
	 * @param KalturaCuePointFilter $filter
	 * @return int
	 */
	function countAction(KalturaCuePointFilter $filter = null)
	{
		if (!$filter)
			$filter = new KalturaCuePointFilter();
						
		$c = KalturaCriteria::create(CuePointPeer::OM_CLASS);
		if($this->getCuePointType())
			$c->add(CuePointPeer::TYPE, $this->getCuePointType());
		
		$cuePointFilter = $filter->toObject();
		$cuePointFilter->attachToCriteria($c);
		
		$c->applyFilters();
		return $c->getRecordsCount();
	}
	
	/**
	 * Update cue point by id 
	 * 
	 * @action update
	 * @param string $id
	 * @param KalturaCuePoint $cuePoint
	 * @return KalturaCuePoint
	 */
	function updateAction($id, KalturaCuePoint $cuePoint)
	{
		$dbCuePoint = CuePointPeer::retrieveByPK($id);
		
		if (!$dbCuePoint)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $id);
			
		if($this->getCuePointType() && $dbCuePoint->getType() != $this->getCuePointType())
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $id);
		
		$dbCuePoint = $cuePoint->toUpdatableObject($dbCuePoint);
				
		$dbCuePoint->setKuserId($this->getKuser()->getId()); 
		$dbCuePoint->save();
		
		$cuePoint->fromObject($dbCuePoint);
		return $cuePoint;
	}
	
	/**
	 * delete cue point by id, and delete all children cue points
	 * 
	 * @action delete
	 * @param string $id 
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */		
	function deleteAction($id)
	{
		$dbCuePoint = CuePointPeer::retrieveByPK( $id );
		
		if(!$dbCuePoint)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $id);
			
		if($this->getCuePointType() && $dbCuePoint->getType() != $this->getCuePointType())
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $id);
		
		$dbCuePoint->setStatus(CuePointStatus::DELETED);
		$dbCuePoint->save();
	}
}
