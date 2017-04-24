<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaRokuSyndicationFeed extends KalturaConstantXsltSyndicationFeed
{

    function __construct()
	{
		$this->type = KalturaSyndicationFeedType::ROKU_DIRECT_PUBLISHER;
		$this->xsltPath =  __DIR__."/xslt/roku_syndication.xslt";
	}
}