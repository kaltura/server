<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaEntryModerationStatus extends KalturaEnum
{
	const PENDING_MODERATION = 1; 
	const APPROVED = 2;   
	const REJECTED = 3;   
	const FLAGGED_FOR_REVIEW = 5;
	const AUTO_APPROVED = 6;
}