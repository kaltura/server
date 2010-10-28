<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaEntryStatus extends KalturaEnum
{
	const ERROR_IMPORTING = -2;
	const ERROR_CONVERTING = -1;
	const IMPORT = 0;
	const PRECONVERT = 1;
	const READY = 2;
	const DELETED = 3;
	const PENDING = 4;
	
	/**
	 * @deprecated This status is deprecated and will be removed in the future, entry {@link ?object=kalturaEntryModerationStatus moderationStatus} should be used instead
	 */
	const MODERATE = 5;
	
	/**
	 * @deprecated This status is deprecated and will be removed in the future, entry {@link ?object=kalturaEntryModerationStatus moderationStatus} should be used instead
	 */
	const BLOCKED = 6;
}