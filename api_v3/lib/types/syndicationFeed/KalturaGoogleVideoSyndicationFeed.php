<?php
class KalturaGoogleVideoSyndicationFeed extends KalturaBaseSyndicationFeed
{
        /**
         *
         * @var KalturaGoogleSyndicationFeedAdultValues
         */
        public $adultContent;
	
	private static $mapBetweenObjects = array
	(
    	"adultContent",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
        
        function __construct()
	{
		$this->type = KalturaSyndicationFeedType::GOOGLE_VIDEO;
	}
}