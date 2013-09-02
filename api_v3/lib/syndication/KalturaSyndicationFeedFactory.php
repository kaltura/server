<?php
/**
 * @package api
 * @subpackage objects.factory
 */
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
			case KalturaSyndicationFeedType::KALTURA:
				$obj = new KalturaGenericSyndicationFeed();
				break;
			case KalturaSyndicationFeedType::KALTURA_XSLT:
				$obj = new KalturaGenericXsltSyndicationFeed();
				break;
			default:
				$obj = new KalturaBaseSyndicationFeed();
				break;
		}
		
		return $obj;
	}
	
	static function getRendererByType($type)
	{
		switch ($type)
		{
			case KalturaSyndicationFeedType::GOOGLE_VIDEO:
				$obj = new GoogleVideoFeedRenderer();
				break;
			case KalturaSyndicationFeedType::YAHOO:
				$obj = new YahooFeedRenderer();
				break;
			case KalturaSyndicationFeedType::ITUNES:
				$obj = new ITunesFeedRenderer();
				break;
			case KalturaSyndicationFeedType::TUBE_MOGUL:
				$obj = new TubeMogulFeedRenderer();
				break;
			case KalturaSyndicationFeedType::KALTURA:
			case KalturaSyndicationFeedType::KALTURA_XSLT:
			default:
				return new KalturaFeedRenderer();
				break;
		}
		return $obj;
	}
}
