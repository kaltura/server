<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaEdgeServerNode extends KalturaDeliveryServerNode
{
	/* (non-PHPdoc)
	 * @see KalturaObject::toInsertableObject()
	 */
	public function toInsertableObject($object_to_fill = null, $props_to_skip = array())
	{
		if(is_null($object_to_fill))
			$object_to_fill = new EdgeServerNode();
			
		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}
}