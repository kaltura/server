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
		$deliveryAttributes->setEdgeServerIds($edgeServerIds);
	
		//Check if there are any edge server that override the delivery profiles
		$edgeServers = EdgeServerPeer::retrieveByPKs($edgeServerIds);
		if(!count($edgeServers))
			return false;
	
		$edgeDeliveryProfilesIds = array();
		foreach ($edgeServers as $edgeServer)
		{
			if(!$edgeServer->getDeliveryProfileIds())
				continue;
			$edgeDeliveryProfilesIds = array_merge($edgeDeliveryProfilesIds, explode(",", $edgeServer->getDeliveryProfileIds()));
		}
	
		if(count($edgeDeliveryProfilesIds))
			$deliveryAttributes->setDeliveryProfileIds($edgeDeliveryProfilesIds, false);
		
		return true;
	}
}
