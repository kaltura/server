<?php
/**
 * Additional scope options for related service 
 * 
 * @author Roman
 */
class KalturaRelatedScope extends KalturaObject
{
	/**
	 * Optional, related videos search will be based on this playlist id
	 * 
	 * @var string
	 */
	public $playlistId;
	
	/**
	 * Optional, list of metadata fields to be used to search for related videos
	 * 
	 * @var string
	 */
	public $metadataFields;
	
	private static $map_between_objects = array
	(
		'playlistId',
		'metadataFields'
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}
