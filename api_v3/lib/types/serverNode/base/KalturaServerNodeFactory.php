<?php
/**
 * @package api
 * @subpackage objects.factory
 */
class KalturaServerNodeFactory
{
	/**
	 * @param int $type
	 * @return KalturaServerNode
	 */
	static function getInstanceByType ($type)
	{
		switch ($type) 
		{
			case KalturaServerNodeType::EDGE:
				$obj = new KalturaEdgeServer();
				break;
				
			case KalturaServerNodeType::MEDIA_SERVER:
				$obj = new KalturaMediaServer();
				break;
				
			default:
				$obj = KalturaPluginManager::loadObject('KalturaServerNode', $type);
				
				if(!$obj)
					$obj = new KalturaServerNode();
					
				break;
		}
		
		return $obj;
	}
}