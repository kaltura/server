<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaBulkDownloadJobData extends KalturaJobData
{
	/**
	 * Comma separated list of entry ids
	 * 
	 * @var string
	 */
	public $entryIds;
	
	/**
	 * Flavor params id to use for conversion
	 * 
	 * @var int
	 */
	public $flavorParamsId;
	
	/**
	 * The id of the requesting user
	 * 
	 * @var string
	 */
	public $puserId;
	
    
	private static $map_between_objects = array
	(
		"entryIds",
		"flavorParamsId",
		"puserId",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toObject($dbData = null, $propsToSkip = array()) 
	{
		if(is_null($dbData))
			$dbData = new kBulkDownloadJobData();
			
		return parent::toObject($dbData);
	}
}

?>