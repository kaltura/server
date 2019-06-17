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

	/**
	 * @var bool
	 */
	protected $seamlessFallbackEnabled;

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
	
	public function getRegisteredNodeServers()
	{
		$edgeServerIds = explode(',', $this->getEdgeServerIds());
		$edgeServers = ServerNodePeer::retrieveRegisteredServerNodesArrayByPKs($edgeServerIds);
		
		return $edgeServers;
	}
	
	public function applyDeliveryProfileDynamicAttributes(DeliveryProfileDynamicAttributes $deliveryAttributes)
	{	
		$edgeServers = $this->getRegisteredNodeServers();
		
		if(!count($edgeServers))
			return false;
		
		$activeEdgeServerIds = array();
		foreach ($edgeServers as $edgeServer)
		{
			/* @var $edgeServer EdgeServerNode */
			if($edgeServer->validateEdgeTreeRegistered())
				$activeEdgeServerIds[] = $edgeServer->getId();
		}
		
		if(!count($activeEdgeServerIds))
			return false;
		
		$deliveryAttributes->setEdgeServerIds($activeEdgeServerIds);
		$deliveryAttributes->setEdgeServerFallback($this->getSeamlessFallbackEnabled());
		return true;
	}

	/**
	 * @return bool
	 */
	public function getSeamlessFallbackEnabled()
	{
		return $this->seamlessFallbackEnabled;
	}

	/**
	 * @param bool $seamlessFallbackEnabled
	 */
	public function setSeamlessFallbackEnabled($seamlessFallbackEnabled)
	{
		$this->seamlessFallbackEnabled = $seamlessFallbackEnabled;
	}


}
