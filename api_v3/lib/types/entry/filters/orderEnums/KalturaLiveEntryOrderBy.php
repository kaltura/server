<?php
/**
 * @package api
 * @subpackage filters.enum
 */
class KalturaLiveEntryOrderBy extends KalturaMediaEntryOrderBy
{
	const FIRST_BROADCAST_ASC = "+firstBroadcast";
	const FIRST_BROADCAST_DESC = "-firstBroadcast";
	const LAST_BROADCAST_ASC = "+lastBroadcast";
	const LAST_BROADCAST_DESC = "-lastBroadcast";
}
