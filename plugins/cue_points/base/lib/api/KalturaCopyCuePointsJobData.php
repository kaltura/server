<?php
/**
 * @package plugins.cue_points
 * @subpackage api.objects
 */
class KalturaCopyCuePointsJobData extends KalturaJobData
{
	/**
	 * destination Entry
	 * @var string
	 */
	public $destinationEntryId = null;

	/**
	 *  an array of source start time and duration
	 * @var KalturaClipDescriptionArray
	 */
	public $clipsDescriptionArray;

	private static $map_between_objects = array
	(
		'destinationEntryId',
		'clipsDescriptionArray',
	);

	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbData = null, $props_to_skip = array())
	{
		if(is_null($dbData))
			$dbData = new kCopyCuePointsJobData();

		return parent::toObject($dbData, $props_to_skip);
	}
}