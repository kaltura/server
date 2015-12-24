<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kAccessControlServeRemoteEdgeServerAction extends kRuleAction 
{
	/**
	 * @var array
	 */
	protected $edgeServerIds = array();
	
	
	public function __construct() 
	{
		parent::__construct(RuleActionType::SERVE_FROM_REMOTE_SERVER);
	}
	
	/**
	 * @return array
	 */
	public function getEdgeServerIds() 
	{
		return implode(',', $this->edgeServerIds);
	}
	/**
	 * @param array $edgeServerIds
	 */
	public function setEdgeServerIds($edgeServerIds) 
	{
		$this->edgeServerIds = explode(',', $edgeServerIds);
	}
	
	public function applyDeliveryProfileDynamicAttributes(DeliveryProfileDynamicAttributes $deliveryAttributes)
	{	
		$edgeServers = ServerNodePeer::retrieveRegisteredServerNodesArrayByPKs($this->edgeServerIds);
		if(!count($edgeServers))
			return false;
		
		foreach ($edgeServers as $edgeServer) 
			$activeEdgeServerIds[] = $edgeServer->getId();
		
		$deliveryAttributes->setEdgeServerIds($activeEdgeServerIds);
		return true;
	}
}
