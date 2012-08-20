<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaYahooSyndicationFeed extends KalturaBaseSyndicationFeed
{
        /**
         *
         * @var KalturaYahooSyndicationFeedCategories
         * @readonly
         */
        public $category;

        /**
         *
         * @var KalturaYahooSyndicationFeedAdultValues
         */
        public $adultContent;
        
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
        
	private static $mapBetweenObjects = array
	(
                "adultContent",
                "feedDescription",
                "feedLandingPage",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}    
	function __construct()
	{
		$this->type = KalturaSyndicationFeedType::YAHOO;
	}
}