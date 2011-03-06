<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaGenericSyndicationFeed extends KalturaBaseSyndicationFeed
{
    function __construct()
	{
		$this->type = KalturaSyndicationFeedType::KALTURA;
	}
}