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
	 * @var int
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
	
	/**
	 * @var string
	 */
	public $hostName;
	
	/**
	 * @var string
	 */
	public $sessionId;
	
	private static $mapBetweenObjects = array
	(
			'schedulerId',
			'workerId',
			'batchIndex',
			'message',
			'errType',
			'errNumber',
			'hostName',
			'sessionId',
			'timeStamp',
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
	
	public function toObject($object_to_fill = null, $props_to_skip = array()) {
		if(is_null($object_to_fill)) 
			$object_to_fill = new kBatchHistoryData();
		
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}