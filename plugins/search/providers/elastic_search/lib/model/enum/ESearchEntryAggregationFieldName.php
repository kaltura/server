<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.enum
 */

interface ESearchEntryAggregationFieldName extends BaseEnum
{
	const ENTRY_TYPE = 'entry_type';
	const MEDIA_TYPE = 'media_type';
	const TAGS = 'tags.raw';
	const ACCESS_CONTROL_PROFILE = 'access_control_id';
}