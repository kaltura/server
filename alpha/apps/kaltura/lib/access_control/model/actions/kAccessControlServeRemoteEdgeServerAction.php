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
		
		$edgeServers = ServerNodePeer::retrieveByPKs($edgeServerIds);
		if(!count($edgeServers))
			return false;
		
		$deliveryAttributes->setEdgeServerIds($edgeServerIds);
		return true;
	}
}
