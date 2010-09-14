<?php
class KalturaSyndicationFeedFactory
{
	static function getInstanceByType ($type)
	{
		switch ($type) 
		{
			case KalturaSyndicationFeedType::GOOGLE_VIDEO:
				$obj = new KalturaGoogleVideoSyndicationFeed();
				break;
			case KalturaSyndicationFeedType::YAHOO:
				$obj = new KalturaYahooSyndicationFeed();
				break;
			case KalturaSyndicationFeedType::ITUNES:
				$obj = new KalturaITunesSyndicationFeed();
				break;
			case KalturaSyndicationFeedType::TUBE_MOGUL:
				$obj = new KalturaTubeMogulSyndicationFeed();
				break;
			default:
				$obj = new KalturaBaseSyndicationFeed();
				break;
		}
		
		return $obj;
	}
}
?>