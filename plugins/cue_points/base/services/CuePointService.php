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
		if (!$this->getKs() || !$this->getKs()->isAdmin()) {
			KalturaCriterion::enableTag(KalturaCriterion::TAG_USER_SESSION);
			CuePointPeer::setUserContentOnly(true);
		}
		
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
		
		$cuePoint = KalturaCuePoint::getInstance($dbCuePoint, $this->getResponseProfile());
		if(!$cuePoint)
		{
			KalturaLog::err("API Cue point not instantiated");
			return null;
		}
			
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
		$response->objects = KalturaCuePointArray::fromDbArray($list, $this->getResponseProfile());
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
			
		$cuePoint = KalturaCuePoint::getInstance($dbCuePoint, $this->getResponseProfile());
		if(!$cuePoint)
			return null;
			
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
		if (!$pager)
		{
			$pager = new KalturaFilterPager();
			$pager->pageSize = baseObjectFilter::getMaxInValues();			// default to the max for compatibility reasons
		}

		if (!$filter)
			$filter = new KalturaCuePointFilter();
			
		return $filter->getTypeListResponse($pager, $this->getResponseProfile(), $this->getCuePointType());
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

		$this->validateUserLog($dbCuePoint);
		
		$dbCuePoint->setKuserId($this->getKuser()->getId()); 
		$dbCuePoint->save();
		
		$cuePoint->fromObject($dbCuePoint, $this->getResponseProfile());
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
		
		$this->validateUserLog($dbCuePoint);
		
		$dbCuePoint->setStatus(CuePointStatus::DELETED);
		$dbCuePoint->save();
	}
	
	/*
	 * Track delete and update api calls to identify if enabling validateUser annotation will 
	 * break any existing functionality
	 */
	private function validateUserLog($dbObject)
	{
		$log = 'validateUserLog: action ['.$this->actionName.'] client tag ['.kCurrentContext::$client_lang.'] ';
		if (!$this->getKs()){
			$log = $log.'Error: No KS ';
			KalturaLog::err($log);
			return;
		}		

		$log = $log.'ks ['.$this->getKs()->getOriginalString().'] ';
		// if admin always allowed
		if (kCurrentContext::$is_admin_session)
			return;

		if (strtolower($dbObject->getPuserId()) != strtolower(kCurrentContext::$ks_uid)) 
		{
			$log = $log.'Error: User not an owner ';
			KalturaLog::err($log);
		}
	}
}
