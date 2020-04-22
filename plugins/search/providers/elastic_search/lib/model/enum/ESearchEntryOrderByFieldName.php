<?php
/**
* @package plugins.elasticSearch
* @subpackage model.enum
*/
interface ESearchEntryOrderByFieldName extends BaseEnum
{
    const UPDATED_AT = 'updated_at';
    const CREATED_AT = 'created_at';
    const START_DATE = 'start_date';
    const END_DATE = 'end_date';
    const NAME = 'name.raw';
    const VOTES = 'votes';
	const RANK = 'rank';
    const VIEWS = 'views';
    const PLAYS = 'plays';
    const LAST_PLAYED_AT = 'last_played_at';
    const VIEWS_LAST_30_DAYS = 'views_30days';
    const PLAYS_LAST_30_DAYS = 'plays_30days';
    const VIEWS_LAST_7_DAYS = 'views_7days';
    const PLAYS_LAST_7_DAYS = 'plays_7days';
    const VIEWS_LAST_1_DAY = 'views_1day';
    const PLAYS_LAST_1_DAY = 'plays_1day';
}
