<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaLiveStreamDetails extends KalturaObject
{
	/**
	 * The status of the primary stream
	 *
	 * @var KalturaEntryServerNodeStatus
	 */
	public $primaryStreamStatus = KalturaEntryServerNodeStatus::STOPPED;

	/**
	 * The status of the secondary stream
	 *
	 * @var KalturaEntryServerNodeStatus
	 */
	public $secondaryStreamStatus = KalturaEntryServerNodeStatus::STOPPED;

	/**
	 * @var KalturaViewMode
	 */
	public $viewMode = KalturaViewMode::PREVIEW;

	/**
	 * @var bool
	 */
	public $wasPublished = false;

	private static $map_between_objects = array
	(
	);
	
	/* (non-PHPdoc)
	 * @see KalturaMediaEntry::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

}
