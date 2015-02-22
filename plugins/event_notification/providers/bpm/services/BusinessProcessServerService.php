<?php
/**
 * Business-Process server service lets you create and manage servers
 * @service businessProcessServer
 * @package plugins.businessProcessNotification
 * @subpackage api.services
 */
class BusinessProcessServerService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		
		$partnerId = $this->getPartnerId();
		if (!EventNotificationPlugin::isAllowedPartner($partnerId))
			throw new KalturaAPIException(KalturaErrors::FEATURE_FORBIDDEN, EventNotificationPlugin::PLUGIN_NAME);
			
		$this->applyPartnerFilterForClass('BusinessProcessServer');
	}

	protected function partnerGroup($peer = null) 		
	{ 		
		if($this->actionName == 'list' || $this->actionName == 'get')
		{
			return "0,$this->partnerGroup";
		}
		
		return $this->partnerGroup;
	}
	
	/**
	 * Allows you to add a new Business-Process server object
	 * 
	 * @action add
	 * @param KalturaBusinessProcessServer $businessProcessServer
	 * @return KalturaBusinessProcessServer
	 */
	public function addAction(KalturaBusinessProcessServer $businessProcessServer)
	{
		$dbBusinessProcessServer = $businessProcessServer->toInsertableObject();
		/* @var $dbBusinessProcessServer BusinessProcessServer */
		$dbBusinessProcessServer->setStatus(BusinessProcessServerStatus::ENABLED);
		$dbBusinessProcessServer->setPartnerId($this->impersonatedPartnerId);
		$dbBusinessProcessServer->save();
		
		// return the saved object
		$businessProcessServer = KalturaBusinessProcessServer::getInstanceByType($dbBusinessProcessServer->getType());
		$businessProcessServer->fromObject($dbBusinessProcessServer);
		return $businessProcessServer;
		
	}
	
	/**
	 * Retrieve an Business-Process server object by id
	 * 
	 * @action get
	 * @param int $id 
	 * @return KalturaBusinessProcessServer
	 * 
	 * @throws KalturaBusinessProcessNotificationErrors::BUSINESS_PROCESS_SERVER_NOT_FOUND
	 */		
	public function getAction($id)
	{
		// get the object
		$dbBusinessProcessServer = BusinessProcessServerPeer::retrieveByPK($id);
		if (!$dbBusinessProcessServer)
			throw new KalturaAPIException(KalturaBusinessProcessNotificationErrors::BUSINESS_PROCESS_SERVER_NOT_FOUND, $id);
			
		// return the found object
		$businessProcessServer = KalturaBusinessProcessServer::getInstanceByType($dbBusinessProcessServer->getType());
		$businessProcessServer->fromObject($dbBusinessProcessServer);
		return $businessProcessServer;
	}
	

	/**
	 * Update an existing Business-Process server object
	 * 
	 * @action update
	 * @param int $id
	 * @param KalturaBusinessProcessServer $businessProcessServer
	 * @return KalturaBusinessProcessServer
	 *
	 * @throws KalturaBusinessProcessNotificationErrors::BUSINESS_PROCESS_SERVER_NOT_FOUND
	 */	
	public function updateAction($id, KalturaBusinessProcessServer $businessProcessServer)
	{
		// get the object
		$dbBusinessProcessServer = BusinessProcessServerPeer::retrieveByPK($id);
		if (!$dbBusinessProcessServer)
			throw new KalturaAPIException(KalturaBusinessProcessNotificationErrors::BUSINESS_PROCESS_SERVER_NOT_FOUND, $id);
		
		// save the object
		$dbBusinessProcessServer = $businessProcessServer->toUpdatableObject($dbBusinessProcessServer);
		$dbBusinessProcessServer->save();
	
		// return the saved object
		$businessProcessServer = KalturaBusinessProcessServer::getInstanceByType($dbBusinessProcessServer->getType());
		$businessProcessServer->fromObject($dbBusinessProcessServer);
		return $businessProcessServer;
	}

	/**
	 * Update Business-Process server status by id
	 * 
	 * @action updateStatus
	 * @param int $id
	 * @param KalturaBusinessProcessServerStatus $status
	 * @return KalturaBusinessProcessServer
	 * 
	 * @throws KalturaBusinessProcessNotificationErrors::BUSINESS_PROCESS_SERVER_NOT_FOUND
	 */
	function updateStatusAction($id, $status)
	{
		// get the object
		$dbBusinessProcessServer = BusinessProcessServerPeer::retrieveByPK($id);
		if (!$dbBusinessProcessServer)
			throw new KalturaAPIException(KalturaBusinessProcessNotificationErrors::BUSINESS_PROCESS_SERVER_NOT_FOUND, $id);

		if($status == BusinessProcessServerStatus::ACTIVE)
		{
			//Check uniqueness of new object's system name
			$systemNameServers = BusinessProcessServerPeer::retrieveBySystemName($dbBusinessProcessServer->getSystemName());
			if (count($systemNameServers))
				throw new KalturaAPIException(KalturaBusinessProcessNotificationErrors::BUSINESS_PROCESS_SERVER_DUPLICATE_SYSTEM_NAME, $dbBusinessProcessServer->getSystemName());
		}	
		
		// save the object
		$dbBusinessProcessServer->setStatus($status);
		$dbBusinessProcessServer->save();
	
		// return the saved object
		$businessProcessServer = KalturaBusinessProcessServer::getInstanceByType($dbBusinessProcessServer->getType());
		$businessProcessServer->fromObject($dbBusinessProcessServer);
		return $businessProcessServer;
	}

	/**
	 * Delete an Business-Process server object
	 * 
	 * @action delete
	 * @param int $id 
	 *
	 * @throws KalturaBusinessProcessNotificationErrors::BUSINESS_PROCESS_SERVER_NOT_FOUND
	 */		
	public function deleteAction($id)
	{
		// get the object
		$dbBusinessProcessServer = BusinessProcessServerPeer::retrieveByPK($id);
		if (!$dbBusinessProcessServer)
			throw new KalturaAPIException(KalturaBusinessProcessNotificationErrors::BUSINESS_PROCESS_SERVER_NOT_FOUND, $id);

		// set the object status to deleted
		$dbBusinessProcessServer->setStatus(BusinessProcessServerStatus::DELETED);
		$dbBusinessProcessServer->save();
	}
	
	/**
	 * list Business-Process server objects
	 * 
	 * @action list
	 * @param KalturaBusinessProcessServerFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaBusinessProcessServerListResponse
	 */
	public function listAction(KalturaBusinessProcessServerFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaBusinessProcessServerFilter();
			
		if (!$pager)
			$pager = new KalturaFilterPager ();

		$businessProcessServerFilter = new BusinessProcessServerFilter();
		$filter->toObject($businessProcessServerFilter);

		$c = new Criteria();
		$businessProcessServerFilter->attachToCriteria($c);
		$count = BusinessProcessServerPeer::doCount($c);
		
		$pager->attachToCriteria ( $c );
		$list = BusinessProcessServerPeer::doSelect($c);
		
		$response = new KalturaBusinessProcessServerListResponse();
		$response->objects = KalturaBusinessProcessServerArray::fromDbArray($list);
		$response->totalCount = $count;
		
		return $response;
	}
}
