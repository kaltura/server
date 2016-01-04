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
	
	public function applyDeliveryProfileDynamicAttributes(DeliveryProfileDynamicAttributes $deliveryAttributes)
	{	
		$edgeServerIds = explode(',', $this->getEdgeServerIds());
		
		$edgeServers = ServerNodePeer::retrieveRegisteredServerNodesArrayByPKs($edgeServerIds);
		if(!count($edgeServers))
			return false;
		
		foreach ($edgeServers as $edgeServer) 
			$activeEdgeServerIds[] = $edgeServer->getId();
		
		$deliveryAttributes->setEdgeServerIds($activeEdgeServerIds);
		return true;
	}
}
