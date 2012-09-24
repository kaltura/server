<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaBatchHistoryData extends KalturaObject 
{
	/**
	 * @var int
	 */
	public $schedulerId;
	
	/**
	 * @var int
	 */
	public $workerId;
	
	/**
	 * @var int
	 */
	public $batchIndex;
	
	/**
	 * @var string
	 */
	public $timeStamp;
	
	/**
	 * @var string
	 */
	public $message;
	
	/**
	 * @var int
	 */
	public $errType;
	
	/**
	 * @var int
	 */
	public $errNumber;
	
	private static $mapBetweenObjects = array
	(
			'schedulerId',
			'workerId',
			'batchIndex',
			'timeStamp',
			'message',
			'errType',
			'errNumber',
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
	
}