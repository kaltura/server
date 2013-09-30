<?php
/**
 * @package api
 * @subpackage objects
 */
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
        
        /**
		 * true in case you want to enfore the palylist order on the 
		 * @var KalturaNullableBoolean
		 */
		public $enforceOrder;
        
        
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
				"enforceOrder",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
}