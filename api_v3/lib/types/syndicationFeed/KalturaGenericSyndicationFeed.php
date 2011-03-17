<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaGenericSyndicationFeed extends KalturaBaseSyndicationFeed
{
    /**
    * feed description
    * 
    * @var string
    */
    public $feedDescription;
        
    function __construct()
	{
		$this->type = KalturaSyndicationFeedType::KALTURA;
	}
	
	private static $mapBetweenObjects = array
	(
		"feedDescription",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
}