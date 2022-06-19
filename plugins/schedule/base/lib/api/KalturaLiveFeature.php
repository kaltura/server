<?php
/**
 * @package plugins.schedule
 * @subpackage api.objects
 * @abstract
 */
abstract class KalturaLiveFeature extends KalturaObject {

	/**
	 * @var string
	 */
	public $systemName;

	/**
	 * @var int
	 */
	public $preStartTime;

	/**
	 * @var int
	 */
	public $postEndTime;

	private static $map_between_objects = array(
		'systemName',
		'preStartTime',
		'postEndTime'
	);

	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}