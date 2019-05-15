<?php
/**
 * @package plugins.sip
 * @subpackage api.objects
 */
class KalturaSipResponse extends KalturaObject{

	/**
	 * @var string
	 */
	public $action;

	private static $mapBetweenObjects = array
	(
		'action'
	);

	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
}
