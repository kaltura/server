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
    
	/**
	* feed landing page (i.e publisher website)
	* 
	* @var string
	*/
	public $feedLandingPage;
        
    function __construct()
	{
		$this->type = KalturaSyndicationFeedType::KALTURA;
	}
	
	private static $mapBetweenObjects = array
	(
		"feedDescription",
		"feedLandingPage",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
}