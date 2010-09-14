<?php
class KalturaITunesSyndicationFeed extends KalturaBaseSyndicationFeed
{
        /**
         * feed description
         * 
         * @var string
         */
        public $feedDescription;
        
        /**
         * feed language
         * 
         * @var string
         */
        public $language;
        
        /**
         * feed landing page (i.e publisher website)
         * 
         * @var string
         */
        public $feedLandingPage;
        
        /**
         * author/publisher name
         * 
         * @var string
         */
        public $ownerName;
        
        /**
         * publisher email
         * 
         * @var string
         */
        public $ownerEmail;
        
        /**
         * podcast thumbnail
         * 
         * @var string
         */
        public $feedImageUrl;

        /**
         *
         * @var KalturaITunesSyndicationFeedCategories
         * @readonly
         */
        public $category;        

        /**
         *
         * @var KalturaITunesSyndicationFeedAdultValues
         */
        public $adultContent;
        
        /**
         *
         * @var string
         */
        public $feedAuthor;
        
	function __construct()
	{
		$this->type = KalturaSyndicationFeedType::ITUNES;
        }

	private static $mapBetweenObjects = array
	(
                "feedDescription",
                "language",
                "feedLandingPage",
                "ownerName",
                "ownerEmail",
                "feedImageUrl",
                "adultContent",
                "feedAuthor",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
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