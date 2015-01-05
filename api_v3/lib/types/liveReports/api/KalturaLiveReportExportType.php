<?php
 
/**
 * @package api
 * @subpackage model.enum
 */
class KalturaLiveReportExportType extends KalturaEnum implements BaseEnum
{
	const PARTNER_TOTAL_ALL = 1;
	const PARTNER_TOTAL_LIVE = 2;

	const ENTRY_TIME_LINE_ALL = 11;
	const ENTRY_TIME_LINE_LIVE = 12;

	const LOCATION_ALL = 21;
	const LOCATION_LIVE = 22;

	const SYNDICATION_ALL = 31;
	const SYNDICATION_LIVE = 32;
}