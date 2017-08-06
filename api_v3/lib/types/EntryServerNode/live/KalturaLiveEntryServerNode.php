<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaLiveEntryServerNode extends KalturaEntryServerNode 
{
	const MAX_BITRATE_PERCENTAGE_DIFF_ALLOWED = 10;
	const MAX_FRAMERATE_PERCENTAGE_DIFF_ALLOWED = 15;
	
	/**
	 * parameters of the stream we got
	 * @var KalturaLiveStreamParamsArray
	 */
	public $streams;

	/**
	 * @var KalturaLiveEntryServerNodeRecrodedProperties
	 */
	public $recordedProperties;


	private static $map_between_objects = array
	(
		"streams",
		"recordedProperties",
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
		$dbStreamsInfo = $this->buildStreamInfoKeyValueArray($dbStreams);
		$inputStreamsInfo = $this->buildStreamInfoKeyValueArray($inputStreamInfo);
		
		foreach ($inputStreamsInfo as $flavorId => $flavorInfo)
		{
			$dbStreamInfo = $dbStreamsInfo[$flavorId] ? $dbStreamsInfo[$flavorId] : null;
			/* @var $dbStreamInfo kLiveStreamParams */
			/* @var $flavorInfo kLiveStreamParams */
			if(!$dbStreamInfo)
			{
				$clearInputStreamInfo = false;
				break;
			}
		
			$bitratePrecentageDiff = $this->getPercentageDiff($flavorInfo->getBitrate(), $dbStreamInfo->getBitrate());
			$frameRatePrecentageDiff = $this->getPercentageDiff($flavorInfo->getFrameRate(), $dbStreamInfo->getFrameRate());
			if($bitratePrecentageDiff > self::MAX_BITRATE_PERCENTAGE_DIFF_ALLOWED || $frameRatePrecentageDiff > self::MAX_FRAMERATE_PERCENTAGE_DIFF_ALLOWED)
			{
				$clearInputStreamInfo = false;
				break;
			}
		}
		
		if($clearInputStreamInfo)
			$this->streams = null;
	}
	
	private function getPercentageDiff($newValue, $oldValue)
	{
		$percentChange = (1 - $oldValue/$newValue) * 100;
		return abs(round($percentChange, 0));
	}
	
	private function buildStreamInfoKeyValueArray($streamInfo = array())
	{
		$result = array();
		foreach($streamInfo as $info)
		{
			/* @var $info kLiveStreamParams */
			$result[$info->getFlavorId()] = $info;
		}
		
		return $result;
	}

	public function toUpdatableObject($object_to_fill, $props_to_skip = array())
	{
		$object_to_fill = parent::toUpdatableObject($object_to_fill, $props_to_skip);
		if ($this->recordedProperties->duration > 0) {
			/** @var LiveEntryServerNode $dbEntryServerNode */
			$liveEntry = entryPeer::retrieveByPK($object_to_fill->getEntryId());
			if (!$liveEntry)
				throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $this->entryId);
			/** @var LiveEntry $liveEntry */
			$recordedEntryId = $liveEntry->getRecordedEntryId();
			$object_to_fill->setRecordedEntryDuration($recordedEntryId, $this->recordedProperties->duration);
		}
		return $object_to_fill;
	}


}