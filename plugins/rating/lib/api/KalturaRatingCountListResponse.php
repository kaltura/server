<?php
/**
 * @package plugins.rating
 * @subpackage api.objects
 * @relatedService RatingService
 */

class KalturaRatingCountListResponse extends KalturaListResponse
{
	/**
	 * @var KalturaRatingCountArray
	 * @readonly
	 */
	public $objects;
}