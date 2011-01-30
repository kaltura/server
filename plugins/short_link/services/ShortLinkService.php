<?php
/**
 * Short link service
 *
 * @service shortLink
 * @package plugins.shortLink
 * @subpackage api.services
 */
class ShortLinkService extends KalturaBaseService
{
	public function initService($serviceName, $actionName)
	{
		parent::initService($serviceName, $actionName);

		myPartnerUtils::addPartnerToCriteria(new ShortLinkPeer(), $this->getPartnerId(), $this->private_partner_data, $this->partnerGroup());
		myPartnerUtils::addPartnerToCriteria(new kuserPeer(), $this->getPartnerId(), $this->private_partner_data, $this->partnerGroup());
	}
	
	/**
	 * List short link objects by filter and pager
	 * 
	 * @action list
	 * @param KalturaShortLinkFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaShortLinkListResponse
	 */
	function listAction(KalturaShortLinkFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaShortLinkFilter;
			
		$shortLinkFilter = $filter->toFilter($this->getPartnerId());
		
		$c = new Criteria();
		$shortLinkFilter->attachToCriteria($c);
		$count = ShortLinkPeer::doCount($c);
		
		if ($pager)
			$pager->attachToCriteria($c);
		$list = ShortLinkPeer::doSelect($c);
		
		$response = new KalturaShortLinkListResponse();
		$response->objects = KalturaShortLinkArray::fromDbArray($list);
		$response->totalCount = $count;
		
		return $response;
	}
	
	/**
	 * Allows you to add a short link object
	 * 
	 * @action add
	 * @param KalturaShortLink $shortLink
	 * @return KalturaShortLink
	 */
	function addAction(KalturaShortLink $shortLink)
	{
		$shortLink->validatePropertyNotNull('systemName');
		$shortLink->validatePropertyMinLength('systemName', 3);
		$shortLink->validatePropertyNotNull('fullUrl');
		$shortLink->validatePropertyMinLength('fullUrl', 10);
		
		if(!$shortLink->status)
			$shortLink->status = KalturaShortLinkStatus::ENABLED;
			
		if(!$shortLink->userId)
			$shortLink->userId = $this->getKuser()->getPuserId();
			
		$dbShortLink = new ShortLink();
		$dbShortLink = $shortLink->toInsertableObject($dbShortLink, array('userId'));
		$dbShortLink->setPartnerId($this->getPartnerId());
		$dbShortLink->setPuserId($shortLink->userId);
		$dbShortLink->save();
		
		$shortLink = new KalturaShortLink();
		$shortLink->fromObject($dbShortLink);
		
		return $shortLink;
	}
	
	/**
	 * Retrieve an short link object by id
	 * 
	 * @action get
	 * @param string $id 
	 * @return KalturaShortLink
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */		
	function getAction($id)
	{
		$dbShortLink = ShortLinkPeer::retrieveByPK($id);
		
		if(!$dbShortLink)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $id);
			
		$shortLink = new KalturaShortLink();
		$shortLink->fromObject($dbShortLink);
		
		return $shortLink;
	}


	/**
	 * Update exisitng short link
	 * 
	 * @action update
	 * @param string $id
	 * @param KalturaShortLink $shortLink
	 * @return KalturaShortLink
	 *
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */	
	function updateAction($id, KalturaShortLink $shortLink)
	{
		$dbShortLink = ShortLinkPeer::retrieveByPK($id);
	
		if (!$dbShortLink)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $id);
		
		$dbShortLink = $shortLink->toUpdatableObject($dbShortLink);
		$dbShortLink->save();
	
		$shortLink->fromObject($dbShortLink);
		
		return $shortLink;
	}

	/**
	 * Mark the short link as deleted
	 * 
	 * @action delete
	 * @param string $id 
	 * @return KalturaShortLink
	 *
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */		
	function deleteAction($id)
	{
		$dbShortLink = ShortLinkPeer::retrieveByPK($id);
	
		if (!$dbShortLink)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $id);
		
		$dbShortLink->setStatus(KalturaShortLinkStatus::DELETED);
		$dbShortLink->save();
			
		$shortLink = new KalturaShortLink();
		$shortLink->fromObject($dbShortLink);
		
		return $shortLink;
	}

	/**
	 * Serves short link
	 * 
	 * @action goto
	 * @serverOnly
	 * @param string $id
	 * @param bool $proxy proxy the response instead of redirect
	 * 
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 * @throws ShortLinkErrors::VIRTUAL_PROXY_FAILED
	 */		
	function gotoAction($id, $proxy = false)
	{
		$dbShortLink = ShortLinkPeer::retrieveByPK($id);
	
		if (!$dbShortLink)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $id);

		if($proxy)
		{
			$success = virtual($dbShortLink->getFullUrl());
			if($success)
			{
				ob_end_flush();
				die;
			}
				
			throw new KalturaAPIException(ShortLinkErrors::VIRTUAL_PROXY_FAILED, $dbShortLink->getFullUrl());
		}
		
		header('Location: ' . $dbShortLink->getFullUrl());
	}
}
