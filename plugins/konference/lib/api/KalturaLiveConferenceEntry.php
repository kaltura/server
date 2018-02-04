<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaLiveConferenceEntry extends KalturaLiveEntry
{

	private static $map_between_objects = array
	(
	);

	public function __construct()
	{
		parent::__construct();
		
		$this->type = KonferencePlugin::getApiValue(ConferenceEntryType::CONFERENCE);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaMediaEntry::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

}
