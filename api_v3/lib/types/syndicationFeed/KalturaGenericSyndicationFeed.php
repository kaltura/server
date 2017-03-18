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
	
	/**
	 * entry filter
	 *
	 * @var KalturaBaseEntryFilter
	 */
	public $entryFilter;

	/**
	 * page size
	 *
	 * @var int
	 */
	public $pageSize;
        
    function __construct()
	{
		$this->type = KalturaSyndicationFeedType::KALTURA;
	}
	
	private static $mapBetweenObjects = array
	(
		"feedDescription",
		"feedLandingPage",
		"entryFilter",
		"pageSize",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
}
