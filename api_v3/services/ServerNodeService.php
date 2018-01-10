<?php
/**
 * Server Node service
 *
 * @service serverNode
 * @package api
 * @subpackage services
 */
class ServerNodeService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		
		$partnerId = $this->getPartnerId();
		if(!$this->getPartner()->getEnabledService(PermissionName::FEATURE_SERVER_NODE) && $partnerId != PARTNER::BATCH_PARTNER_ID)
			throw new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN, $this->serviceName.'->'.$this->actionName);
			
		$this->applyPartnerFilterForClass('serverNode');
	}
	
	/**
	 * Adds a server node to the Kaltura DB.
	 *
	 * @action add
	 * @param KalturaServerNode $serverNode
	 * @return KalturaServerNode
	 */
	function addAction(KalturaServerNode $serverNode)
	{	
		$dbServerNode = $this->addNewServerNode($serverNode);
		
		$serverNode = KalturaServerNode::getInstance($dbServerNode, $this->getResponseProfile());
		return $serverNode;
	}
	
	/**
	 * Get server node by id
	 * 
	 * @action get
	 * @param int $serverNodeId
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 * @return KalturaServerNode
	 */
	function getAction($serverNodeId)
	{
		$dbServerNode = ServerNodePeer::retrieveByPK($serverNodeId);
		if (!$dbServerNode)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $serverNodeId);
		
		$serverNode = KalturaServerNode::getInstance($dbServerNode, $this->getResponseProfile());
		return $serverNode;
	}
	
	/**
	 * Update server node by id 
	 * 
	 * @action update
	 * @param int $serverNodeId
	 * @param KalturaServerNode $serverNode
	 * @return KalturaServerNode
	 */
	function updateAction($serverNodeId, KalturaServerNode $serverNode)
	{
		$dbServerNode = ServerNodePeer::retrieveByPK($serverNodeId);
		if (!$dbServerNode)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $serverNodeId);
			
		$dbServerNode = $serverNode->toUpdatableObject($dbServerNode);
		$dbServerNode->save();
		
		$serverNode = KalturaServerNode::getInstance($dbServerNode, $this->getResponseProfile());
		return $serverNode;
	}
	
	/**
	 * delete server node by id
	 *
	 * @action delete
	 * @param string $serverNodeId
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */
	function deleteAction($serverNodeId)
	{
		$dbServerNode = ServerNodePeer::retrieveByPK($serverNodeId);
		if(!$dbServerNode)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $serverNodeId);
	
		$dbServerNode->setStatus(ServerNodeStatus::DELETED);
		$dbServerNode->save();
	}
	
	/**
	 * Disable server node by id
	 *
	 * @action disable
	 * @param string $serverNodeId
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 * @return KalturaServerNode
	 */
	function disableAction($serverNodeId)
	{
		$dbServerNode = ServerNodePeer::retrieveByPK($serverNodeId);
		if(!$dbServerNode)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $serverNodeId);
	
		$dbServerNode->setStatus(ServerNodeStatus::DISABLED);
		$dbServerNode->save();
		
		$serverNode = KalturaServerNode::getInstance($dbServerNode, $this->getResponseProfile());
		return $serverNode;
	}
	
	/**
	 * Enable server node by id
	 *
	 * @action enable
	 * @param string $serverNodeId
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 * @return KalturaServerNode
	 */
	function enableAction($serverNodeId)
	{
		$dbServerNode = ServerNodePeer::retrieveByPK($serverNodeId);
		if(!$dbServerNode)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $serverNodeId);
	
		$dbServerNode->setStatus(ServerNodeStatus::ACTIVE);
		$dbServerNode->save();
		
		$serverNode = KalturaServerNode::getInstance($dbServerNode, $this->getResponseProfile());
		return $serverNode;
	}
	
	/**	
	 * @action list
	 * @param KalturaServerNodeFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaServerNodeListResponse
	 */
	public function listAction(KalturaServerNodeFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if(!$filter)
			$filter = new KalturaServerNodeFilter();
			
		if(!$pager)
			$pager = new KalturaFilterPager();
		
		return $filter->getTypeListResponse($pager, $this->getResponseProfile(), null);
	}
	
	/**
	 * Update server node status
	 *
	 * @action reportStatus
	 * @param string $hostName
	 * @return KalturaServerNode
	 */
	function reportStatusAction($hostName, KalturaServerNode $serverNode = null)
	{
		$dbServerNode = ServerNodePeer::retrieveActiveServerNode($hostName, $this->getPartnerId());
		
		//Allow serverNodes auto registration without calling add
		if (!$dbServerNode)
		{
			if($serverNode)
			{
				$dbServerNode = $this->addNewServerNode($serverNode);
			}
			else 
				throw new KalturaAPIException(KalturaErrors::SERVER_NODE_NOT_FOUND, $hostName);
		}
	
		$dbServerNode->setHeartbeatTime(time());
		if ($dbServerNode->getStatus() == ServerNodeStatus::NOT_REGISTERED)
			$dbServerNode->setStatus(ServerNodeStatus::ACTIVE);
		$dbServerNode->save();
	
		$serverNode = KalturaServerNode::getInstance($dbServerNode, $this->getResponseProfile());
		return $serverNode;
	}
	
	private function addNewServerNode(KalturaServerNode $serverNode)
	{
		$dbServerNode = $serverNode->toInsertableObject();
		/* @var $dbServerNode ServerNode */
		$dbServerNode->setPartnerId($this->getPartnerId());
		$dbServerNode->setStatus(ServerNodeStatus::DISABLED);
		$dbServerNode->save();
		
		return $dbServerNode;
	}

	/**
	 * Unregister server node by id
	 *
	 * @action unregister
	 * @param string $serverNodeId
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 * @return KalturaServerNode
	 * @throws KalturaAPIException
	 */
	function unregisterAction($serverNodeId)
	{
		$dbServerNode = ServerNodePeer::retrieveByPK($serverNodeId);
		if(!$dbServerNode)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $serverNodeId);

		$dbServerNode->setStatus(ServerNodeStatus::NOT_REGISTERED);
		$dbServerNode->save();

		$serverNode = KalturaServerNode::getInstance($dbServerNode, $this->getResponseProfile());
		return $serverNode;
	}
}
