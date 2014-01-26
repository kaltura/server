<?php
/**
 * @package plugins.playReady
 * @subpackage api.objects
 */
class KalturaPlayReadyAnalogVideoOPIdHolder extends KalturaObject
{
	/**
	 * The type of the play enabler
	 * 
	 * @var KalturaPlayReadyAnalogVideoOPId
	 */
	public $type;
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		return $this->type;
	}
	
	private static $mapBetweenObjects = array
	(
		'type',
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
}