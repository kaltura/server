<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaSyndicationFeedType extends KalturaDynamicEnum implements syndicationFeedType
{
	public static function getEnumClass()
	{
		return 'syndicationFeedType';
	}
}