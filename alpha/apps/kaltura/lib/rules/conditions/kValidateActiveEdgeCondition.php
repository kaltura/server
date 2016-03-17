<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kValidateActiveEdgeCondition extends kCondition
{
	/* (non-PHPdoc)
	 * @see kCondition::__construct()
	 */
	public function __construct($not = false)
	{
		$this->setType(ConditionType::ACTIVE_EDGE_VALIDATE);
		parent::__construct($not);
	}

	/**
	 * The edge nodes to validate are active
	 * 
	 * @var array
	 */
	protected $edgeServerIds = array();
	
	/**
	 * @param string $edgeServerIds
	 */
	public function setEdgeServerIds($edgeServerIds)
	{
		$this->edgeServerIds = explode(',', $edgeServerIds);
	}
	
	/**
	 * @return array
	 */
	function getEdgeServerIds()
	{
		return implode(',', $this->edgeServerIds);
	}
	
	/* (non-PHPdoc)
	 * @see kCondition::internalFulfilled()
	 */
	protected function internalFulfilled(kScope $scope)
	{
		KalturaLog::debug("Validating edge server [{$this->getEdgeServerIds()}] are active");
		$edgeServers = ServerNodePeer::retrieveRegisteredServerNodesArrayByPKs($this->edgeServerIds);
		
		if(!count($edgeServers)) 
		{
			KalturaLog::debug("Unable to find active edge in list, condition is false");
			return false;
		}
		
		$isFulfilled = false;
		foreach ($edgeServers as $edgeServer)
		{
			/* @var $edgeServer EdgeServerNode */
			if($edgeServer->validateEdgeTreeRegistered())
			{
				$isFulfilled = true;
				KalturaLog::debug("Found active edge in list, condition is true");
				break;
				
			}
		}
		
		return $isFulfilled;
	}
}
