<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaLiveEntryServerNode extends KalturaEntryServerNode 
{
	const MAX_BITRATE_PERCENTAGE_DIFF_ALLOWED = 10;
	
	/**
	 * parameters of the stream we got
	 * @var KalturaLiveStreamParamsArray
	 */
	public $streams;

	private static $map_between_objects = array
	(
		"streams"
	);

	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (is_null($object_to_fill))
		{
			$object_to_fill = new LiveEntryServerNode();
		}
		return parent::toObject($object_to_fill, $props_to_skip);
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUpdate()
	 */
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		$dbStreamsInfo = $sourceObject ? $sourceObject->getStreams() : array();
		$inputStreamsInfo = isset($this->streams) ? $this->streams : array();
		
		if(count($dbStreamsInfo) === count($inputStreamsInfo))
		{
			$this->clearInputStreamInfoIfoNotChanged($dbStreamsInfo, $inputStreamsInfo->toObjectsArray());
		}
		
		return parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}
	
	private function clearInputStreamInfoIfoNotChanged($dbStreams, $inputStreamInfo)
	{
		$clearInputStreamInfo = true;
		$dbStreamsBitrateInfo = $this->buildStreamInfoKeyValueArray($dbStreams);
		$inputStreamsBitarteInfo = $this->buildStreamInfoKeyValueArray($inputStreamInfo);
		
		foreach ($inputStreamsBitarteInfo as $flavorId => $inputBitarte)
		{
			if(!isset($dbStreamsBitrateInfo[$flavorId]))
			{
				$clearInputStreamInfo = false;
				break;
			}
		
			$precentageDiff = $this->getBitaretPercentageDiff($inputBitarte, $dbStreamsBitrateInfo[$flavorId]);
			if($precentageDiff > self::MAX_BITRATE_PERCENTAGE_DIFF_ALLOWED)
			{
				$clearInputStreamInfo = false;
				break;
			}
		}
		
		if($clearInputStreamInfo)
			$this->streams = null;
	}
	
	private function getBitaretPercentageDiff($inputBitarte, $dbBitarte)
	{
		$percentChange = (1 - $dbBitarte/$inputBitarte) * 100;
		return abs(round($percentChange, 0));
	}
	
	private function buildStreamInfoKeyValueArray($streamInfo = array())
	{
		$result = array();
		foreach($streamInfo as $info)
		{
			/* @var $info kLiveStreamParams */
			$bitrate = $info->getBitrate();
			$flavorId = $info->getFlavorId();
			$result[$flavorId] = $bitrate;
		}
		
		return $result;
	}
}