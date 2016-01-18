<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kAccessControlServeRemoteEdgeServerAction extends kRuleAction 
{
	/**
	 * @var string
	 */
	protected $edgeServerIds;
	
	
	public function __construct() 
	{
		parent::__construct(RuleActionType::SERVE_FROM_REMOTE_SERVER);
	}
	
	/**
	 * @return array
	 */
	public function getEdgeServerIds() 
	{
		return $this->edgeServerIds;
	}
	/**
	 * @param array $edgeServerIds
	 */
	public function setEdgeServerIds($edgeServerIds) 
	{
		$this->edgeServerIds = $edgeServerIds;
	}
	
	public function getRegiteredNodeServers()
	{
		$edgeServerIds = explode(',', $this->getEdgeServerIds());
		$edgeServers = ServerNodePeer::retrieveRegisteredServerNodesArrayByPKs($edgeServerIds);
		
		return $edgeServers;
	}
	
	public function applyDeliveryProfileDynamicAttributes(DeliveryProfileDynamicAttributes $deliveryAttributes)
	{	
		$edgeServers = $this->getRegiteredNodeServers();
		
		if(!count($edgeServers))
			return false;
		
		foreach ($edgeServers as $edgeServer) 
			$activeEdgeServerIds[] = $edgeServer->getId();
		
		$deliveryAttributes->setEdgeServerIds($activeEdgeServerIds);
		return true;
	}
}
