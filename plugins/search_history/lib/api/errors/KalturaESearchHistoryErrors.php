<?php
/**
 * @package plugins.searchHistory
 * @subpackage api.errors
 */
class KalturaESearchHistoryErrors extends KalturaESearchErrors
{
	const EMPTY_DELETE_SEARCH_TERM_NOT_ALLOWED = 'EMPTY SEARCH TERM IS NOT ALLOWED;;empty search term is not allowed';
	const TIME_RANGE_EXCEEDED_LIMIT = 'TIMESTAMP RANGE EXCEEDED LIMIT;TIMESTAMP_RANGE;timestamp range should be limited to [@TIMESTAMP_RANGE@] months range';
	const AGGREGATION_SIZE_EXCEEDED_LIMIT = 'AGGREGATION SIZE EXCEEDED LIMIT;AGGREGATION_SIZE;aggregation size should be limited to [@AGGREGATION_SIZE@]';
}
