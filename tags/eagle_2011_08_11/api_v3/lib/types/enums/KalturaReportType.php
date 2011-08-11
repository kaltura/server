<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaReportType extends KalturaEnum
{
	/**
	 * same as myReportsMgr::REPORT_TYPE_... 
	 *
	 */
	const TOP_CONTENT = 1;
	const CONTENT_DROPOFF = 2;
	const CONTENT_INTERACTIONS = 3;
	const MAP_OVERLAY = 4;
	const TOP_CONTRIBUTORS = 5;
	const TOP_SYNDICATION = 6;
	const CONTENT_CONTRIBUTIONS = 7;
//	const ADMIN_CONSOLE = 10;		// shouldn't be accessable to users through the API
}
?>