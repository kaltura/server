<?php


/**
 * Skeleton subclass for representing a row from the 'server_node' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package Core
 * @subpackage model
 */
class ServerNode extends BaseServerNode {

	const SERVER_NODE_TTL_TIME = 120;

	public function getCacheInvalidationKeys()
	{
		return array("serverNode:id".strtolower($this->getId()), "serverNode:hostName=".strtolower($this->getHostName()));
	}
	
	public function getParentIdsArray()
	{
		$parentIds = array();
	
		$ids = $this->getParentId();
		if($ids)
		{
			$parentIds = explode(",", $ids);
		}
	
		return $parentIds;
	}
	
} // ServerNode
