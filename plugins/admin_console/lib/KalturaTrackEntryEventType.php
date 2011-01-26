<?php
/**
 * @package plugins.adminConsole
 * @subpackage api.enums
 */
class KalturaTrackEntryEventType extends KalturaEnum
{
	const UPLOADED_FILE = 1;
	const WEBCAM_COMPLETED = 2;
	const IMPORT_STARTED = 3;
	const ADD_ENTRY = 4;
	const UPDATE_ENTRY = 5;
	const DELETED_ENTRY = 6;
}