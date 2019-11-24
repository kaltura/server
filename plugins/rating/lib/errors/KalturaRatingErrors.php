<?php
/**
 * @package plugins.rating
 * @subpackage api.errors
 */

class KalturaRatingErrors
{
	const USER_RATING_FOR_ENTRY_NOT_FOUND = "USER_RATING_FOR_ENTRY_NOT_FOUND;;This user has not rated this entry";
	
	const MUST_FILTER_BY_RANK = "MUST_FILTER_BY_RANK;;Must pass either rankIn or rankEqual on filter";
}