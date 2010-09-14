<?php
class KalturaTubeMogulSyndicationFeed extends KalturaBaseSyndicationFeed
{
        /**
         *
         * @var KalturaTubeMogulSyndicationFeedCategories
         * @readonly
         */
        public $category;
        
	function __construct()
	{
		$this->type = KalturaSyndicationFeedType::TUBE_MOGUL;
	}
        
	private static $mapBetweenObjects = array
	(
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
        
        public function toObject($object_to_fill = null , $props_to_skip = array())
        {
            $categories = explode(',', $this->categories);
            $numCategories = array();
            foreach($categories as $category)
            {
                $numCategories[] = $this->getCategoryId($category);
            }
            $this->categories = implode(',', $numCategories);
            parent::toObject($object_to_fill);
            $this->categories = implode(',', $categories);
        }
        
        public function fromObject($source_object)
        {
            parent::fromObject($source_object);
            $categories = explode(',', $this->categories);
            $strCategories = array();
            foreach($categories as $category)
            {
                $strCategories[] = $this->getCategoryName($category);
            }
            $this->categories = implode(',', $strCategories);
            if(isset($this->id) && $this->id)
            {
                $this->feedUrl = 'http://'.kConf::get('www_host').'/api_v3/getFeed.php?feedId='.$this->id;
            }
        }
        
        private static $mapCategories = array(
            KalturaTubeMogulSyndicationFeedCategories::ARTS_AND_ANIMATION => 1,
            KalturaTubeMogulSyndicationFeedCategories::COMEDY => 3,
            KalturaTubeMogulSyndicationFeedCategories::ENTERTAINMENT => 4,
            KalturaTubeMogulSyndicationFeedCategories::MUSIC => 5,
            KalturaTubeMogulSyndicationFeedCategories::NEWS_AND_BLOGS => 6,
            KalturaTubeMogulSyndicationFeedCategories::SCIENCE_AND_TECHNOLOGY => 7,
            KalturaTubeMogulSyndicationFeedCategories::SPORTS => 8,
            KalturaTubeMogulSyndicationFeedCategories::TRAVEL_AND_PLACES => 9,
            KalturaTubeMogulSyndicationFeedCategories::VIDEO_GAMES => 10,
            KalturaTubeMogulSyndicationFeedCategories::ANIMALS_AND_PETS => 11,
            KalturaTubeMogulSyndicationFeedCategories::AUTOS => 12,
            KalturaTubeMogulSyndicationFeedCategories::VLOGS_PEOPLE => 13,
            KalturaTubeMogulSyndicationFeedCategories::HOW_TO_INSTRUCTIONAL_DIY => 14,
            KalturaTubeMogulSyndicationFeedCategories::COMMERCIALS_PROMOTIONAL => 15,
            KalturaTubeMogulSyndicationFeedCategories::FAMILY_AND_KIDS => 16,
        );
	public static function getCategoryId( $category )
	{
            return self::$mapCategories[$category];
	}
        
        public static function getCategoryName( $id )
        {
            $arrCategories = array_flip(self::$mapCategories);
            return $arrCategories[$id];
        }
}