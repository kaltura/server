<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface UserEntryPermissionLevel extends BaseEnum
{
	const SPEAKER = 1;
	const MODERATOR = 2;
	const ATTENDEE = 3;
	const ADMIN = 4;
	const PREVIEW_ONLY = 5;
	const CHAT_MODERATOR = 6;
}
