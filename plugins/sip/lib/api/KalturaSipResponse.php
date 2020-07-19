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

	/**
	 * @var string
	 */
	public $msg;

	private static $mapBetweenObjects = array
	(
		'action',
		'hostName',
		'sessionId',
		'msg'
	);

	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
}
