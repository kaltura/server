<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.enum
 */
class KalturaESearchEntryOrderByFieldName extends KalturaStringEnum
{
	const UPDATED_AT = 'updated_at';
	const CREATED_AT = 'created_at';
	const START_DATE = 'start_date';
	const END_DATE = 'end_date';
	const NAME = 'name';
	const VOTES = 'votes';
	const RANK = 'rank';
	const VIEWS = 'views';
	const PLAYS = 'plays';
	const LAST_PLAYED_AT = 'last_played_at';
	const VIEWS_LAST_30_DAYS = 'views_last_30_days';
	const PLAYS_LAST_30_DAYS = 'plays_last_30_days';
	const VIEWS_LAST_7_DAYS = 'views_last_7_days';
	const PLAYS_LAST_7_DAYS = 'plays_last_7_days';
	const VIEWS_LAST_1_DAY = 'views_last_1_day';
	const PLAYS_LAST_1_DAY = 'plays_last_1_day';
}
