<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaOperaSyndicationFeed extends KalturaConstantXsltSyndicationFeed
{

    function __construct()
	{
		$this->type = KalturaSyndicationFeedType::OPERA_TV_SNAP;
		$this->xsltPath =  __DIR__."/xslt/opera_syndication.xslt";
	}
}