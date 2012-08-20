<?php 
/**
 * @package api
 * @subpackage enum
 */
class KalturaNotificationStatus extends KalturaEnum 
{
	const PENDING = 1;
	const SENT = 2;
	const ERROR = 3;
	const SHOULD_RESEND = 4; // ??
	const ERROR_RESENDING = 5;
	const SENT_SYNCH = 6;
	const QUEUED = 7;
}