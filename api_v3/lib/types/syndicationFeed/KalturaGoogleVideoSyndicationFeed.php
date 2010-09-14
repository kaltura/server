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
        
        public function fromObject($source_object)
        {
            parent::fromObject($source_object);
            if(isset($this->id) && $this->id)
            {
                $this->feedUrl = 'http://'.kConf::get('www_host').'/api_v3/getFeed.php?feedId='.$this->id;
            }
        }
}