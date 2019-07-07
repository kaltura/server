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

	/**
	 * @var string
	 */
	public $sessionId;

	/**
	 * @var string
	 */
	public $hostName;

	private static $mapBetweenObjects = array
	(
		'action',
		'hostName',
		'sessionId'
	);

	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
}
