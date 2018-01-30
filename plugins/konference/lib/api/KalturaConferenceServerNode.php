<?php
/**
 * @package plugins.konference
 * @subpackage api.objects
 */
class KalturaConferenceServerNode extends KalturaServerNode
{

	/* (non-PHPdoc)
	 * @see KalturaObject::toInsertableObject()
	 */
	public function toInsertableObject($object_to_fill = null, $props_to_skip = array())
	{
		if(is_null($object_to_fill))
			$object_to_fill = new ConferenceServerNode();
			
		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
			$dbObject = new ConferenceServerNode();
	
		return parent::toObject($dbObject, $skip);
	}
}
