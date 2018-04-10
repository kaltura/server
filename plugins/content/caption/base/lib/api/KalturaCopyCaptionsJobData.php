<?php
/**
 * @package plugins.caption
 * @subpackage api.objects
 */
class KalturaCopyCaptionsJobData extends KalturaJobData
{

	/** entry Id
	 * @var string
	 */
	public $entryId = null;

	/**
	 *  an array of source start time and duration
	 * @var KalturaClipDescriptionArray
	 */
	public $clipsDescriptionArray;

	/**
	 * @var bool
	 */
	public $fullCopy;

	private static $map_between_objects = array
	(
		'entryId',
		'clipsDescriptionArray',
		'fullCopy',
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
			$dbData = new kCopyCaptionsJobData();

		return parent::toObject($dbData, $props_to_skip);
	}
}