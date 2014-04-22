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
		

		// Play-Server and Media-Server list entries of all partners
		// This is not too expensive as the requests are cached conditionally and performed on sphinx
		$allowedSystemPartners = array(
			Partner::MEDIA_SERVER_PARTNER_ID,
			Partner::PLAY_SERVER_PARTNER_ID,
		);
		
		if(in_array($this->getPartnerId(), $allowedSystemPartners) && $actionName == 'list')
		{
			myPartnerUtils::resetPartnerFilter('entry');
		}
		else 
		{	
			$this->applyPartnerFilterForClass('CuePoint');
		}

		// when session is not admin, allow access to user entries only
		if (!$this->getKs() || !$this->getKs()->isAdmin())
			CuePointPeer::setDefaultCriteriaFilterByKuser();
		
		if(!CuePointPlugin::isAllowedPartner($this->getPartnerId()))
			throw new KalturaAPIException(KalturaErrors::FEATURE_FORBIDDEN, CuePointPlugin::PLUGIN_NAME);
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
		
		if($cuePoint->systemName)
		{
			$existingCuePoint = CuePointPeer::retrieveBySystemName($cuePoint->entryId, $cuePoint->systemName);
			if($existingCuePoint)
				throw new KalturaAPIException(KalturaCuePointErrors::CUE_POINT_SYSTEM_NAME_EXISTS, $cuePoint->systemName, $existingCuePoint->getId());
		}
		
		/* @var $dbCuePoint CuePoint */
		$dbCuePoint->setPartnerId($this->getPartnerId());
		$dbCuePoint->setPuserId(is_null($cuePoint->userId) ? $this->getKuser()->getPuserId() : $cuePoint->userId);
		$dbCuePoint->setStatus(CuePointStatus::READY); 
					
		if($this->getCuePointType())
			$dbCuePoint->setType($this->getCuePointType());
			
		$created = $dbCuePoint->save();
		if(!$created)
		{
			KalturaLog::err("Cue point not created");
			return null;
		}
		
		$cuePoint = KalturaCuePoint::getInstance($dbCuePoint->getType());
		if(!$cuePoint)
		{
			KalturaLog::err("API Cue point not instantiated");
			return null;
		}
			
		$cuePoint->fromObject($dbCuePoint);
		return $cuePoint;
	}
	
	/**
	 * Allows you to add multiple cue points objects by uploading XML that contains multiple cue point definitions
	 * 
	 * @action addFromBulk
	 * @param file $fileData
	 * @return KalturaCuePointListResponse
	 * @throws KalturaCuePointErrors::XML_FILE_NOT_FOUND
	 * @throws KalturaCuePointErrors::XML_INVALID
	 */
	function addFromBulkAction($fileData)
	{
		try
		{
			$list = kCuePointManager::addFromXml($fileData['tmp_name'], $this->getPartnerId());
		}
		catch (kCoreException $e)
		{
			throw new KalturaAPIException($e->getCode());
		}
		
		$response = new KalturaCuePointListResponse();
		$response->objects = KalturaCuePointArray::fromDbArray($list);
		$response->totalCount = count($list);
	
		return $response;
	}
	
	/**
	 * Download multiple cue points objects as XML definitions
	 * 
	 * @action serveBulk
	 * @param KalturaCuePointFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return file
	 */
	function serveBulkAction(KalturaCuePointFilter $filter = null, KalturaFilterPager $pager = null)
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
		$xml = kCuePointManager::generateXml($list);
		
		header("Content-Type: text/xml; charset=UTF-8");
		echo $xml;
		kFile::closeDbConnections();
		exit(0);
	}
	
	/**
	 * Retrieve an CuePoint object by id
	 * 
	 * @action get
	 * @param string $id 
	 * @return KalturaCuePoint
	 * @throws KalturaCuePointErrors::INVALID_CUE_POINT_ID
	 */		
	function getAction($id)
	{
		$dbCuePoint = CuePointPeer::retrieveByPK( $id );

		if(!$dbCuePoint)
			throw new KalturaAPIException(KalturaCuePointErrors::INVALID_CUE_POINT_ID, $id);
			
		if($this->getCuePointType() && $dbCuePoint->getType() != $this->getCuePointType())
			throw new KalturaAPIException(KalturaCuePointErrors::INVALID_CUE_POINT_ID, $id);
			
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

		$entryIds = null;
		if ($filter->entryIdEqual) {
			$entryIds = array($filter->entryIdEqual);
		} else if ($filter->entryIdIn) {
			$entryIds = explode(',', $filter->entryIdIn);
		}
		
		if (! is_null ( $entryIds )) {
			$entryIds = entryPeer::filterEntriesByPartnerOrKalturaNetwork ( $entryIds, $this->getPartnerId () );
			if (! $entryIds) {
				$response = new KalturaCuePointListResponse ();
				$response->objects = array ();
				$response->totalCount = 0;
				return $response;
			}
			
			$filter->entryIdEqual = null;
			$filter->entryIdIn = implode ( ',', $entryIds );
		}
			
		
		
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
	 * @throws KalturaCuePointErrors::INVALID_CUE_POINT_ID
	 * @validateUser CuePoint id editcuepoint
	 */
	function updateAction($id, KalturaCuePoint $cuePoint)
	{
		$dbCuePoint = CuePointPeer::retrieveByPK($id);
		
		if (!$dbCuePoint)
			throw new KalturaAPIException(KalturaCuePointErrors::INVALID_CUE_POINT_ID, $id);
			
		if($this->getCuePointType() && $dbCuePoint->getType() != $this->getCuePointType())
			throw new KalturaAPIException(KalturaCuePointErrors::INVALID_CUE_POINT_ID, $id);
		
		if($cuePoint->systemName)
		{
			$existingCuePoint = CuePointPeer::retrieveBySystemName($dbCuePoint->getEntryId(), $cuePoint->systemName);
			if($existingCuePoint && $existingCuePoint->getId() != $id)
				throw new KalturaAPIException(KalturaCuePointErrors::CUE_POINT_SYSTEM_NAME_EXISTS, $cuePoint->systemName, $existingCuePoint->getId());
		}
		
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
	 * @throws KalturaCuePointErrors::INVALID_CUE_POINT_ID
	 * @validateUser CuePoint id editcuepoint
	 */		
	function deleteAction($id)
	{
		$dbCuePoint = CuePointPeer::retrieveByPK( $id );
		
		if(!$dbCuePoint)
			throw new KalturaAPIException(KalturaCuePointErrors::INVALID_CUE_POINT_ID, $id);
			
		if($this->getCuePointType() && $dbCuePoint->getType() != $this->getCuePointType())
			throw new KalturaAPIException(KalturaCuePointErrors::INVALID_CUE_POINT_ID, $id);
		
		$dbCuePoint->setStatus(CuePointStatus::DELETED);
		$dbCuePoint->save();
	}
}
