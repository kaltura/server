<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaLiveParams extends KalturaFlavorParams 
{
	/**
	 * Suffix to be added to the stream name after the entry id {entry_id}_{stream_suffix}, e.g. for entry id 0_kjdu5jr6 and suffix 1, the stream name will be 0_kjdu5jr6_1
	 * 
	 * @var string
	 */
	public $streamSuffix;

	private static $map_between_objects = array
	(
		'streamSuffix',
	);
	
	/* (non-PHPdoc)
	 * @see KalturaFlavorParams::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($object = null, $skip = array())
	{
		if(is_null($object))
			$object = new liveParams();
			
		return parent::toObject($object, $skip);
	}
}