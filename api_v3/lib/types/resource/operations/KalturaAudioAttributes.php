<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaAudioAttributes extends KalturaObject
{
	/**
	 * @var float
	 */
	public $volume;

	private static $map_between_objects = array
	(
		"volume"
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		$this->validateForUsage($object_to_fill, $props_to_skip);

		if(!$object_to_fill)
		{
			$object_to_fill = new kAudioAttributes();
		}

		return parent::toObject($object_to_fill, $props_to_skip);
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUsage($sourceObject, $propertiesToSkip)
	 */
	public function validateForUsage($sourceObject, $propertiesToSkip = array())
	{
		parent::validateForUsage($sourceObject, $propertiesToSkip);
		$this->validatePropertyNotNull('volume');
		$minVolume = 0;
		$maxVolume = 1;
		if($this->volume < $minVolume || $this->volume > $maxVolume)
		{
			throw new KalturaAPIException(KalturaErrors::PARAMETER_VALUE_OUT_OF_RANGE, "volume", $minVolume, $maxVolume);
		}
	}
}
