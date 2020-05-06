<?php
/**
 * @package Core
 * @subpackage model.enum
 */
interface kEntryFileSyncSubType extends BaseEnum
{
	const DATA = 1;
	const DATA_EDIT = 2;
	const THUMB = 3;
	const ARCHIVE = 4;
	const DOWNLOAD = 5;
	const OFFLINE_THUMB = 6;
	const ISM = 7;
	const ISMC = 8;
	const CONVERSION_LOG = 9;
	const LIVE_PRIMARY = 10;
	const LIVE_SECONDARY = 11;
	const INTERACTIVITY_DATA = 12;
	const VOLATILE_INTERACTIVITY_DATA = 13;
}
