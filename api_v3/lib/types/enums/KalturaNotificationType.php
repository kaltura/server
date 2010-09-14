<?php 
/**
 * @package api
 * @subpackage enum
 */
class KalturaNotificationType extends KalturaEnum 
{
    const ENTRY_ADD = 1;
	const ENTR_UPDATE_PERMISSIONS = 2;
	const ENTRY_DELETE = 3;
	const ENTRY_BLOCK = 4;
	const ENTRY_UPDATE = 5;
	const ENTRY_UPDATE_THUMBNAIL = 6;
	const ENTRY_UPDATE_MODERATION = 7;

	const USER_ADD = 21;
	const USER_BANNED = 26;
	
	public function getDescription()
	{
		return array(
			self::ENTRY_ADD => "The <i>entry_add</i> notification is being sent to notify that a new entry has been successfully added and ready for use.",
			self::ENTR_UPDATE_PERMISSIONS => "The <i>entry_update_permissions</i> notification is being sent to notify that the privacy settings of an entry have changed.",
			self::ENTRY_DELETE => "The <i>entry_delete</i> notification is being sent to notify that an entry has been deleted. ",
			self::ENTRY_BLOCK => "The <i>entry_block</i> notification is sent to notify that an entry has been blocked by a moderator or admin user.",
			self::ENTRY_UPDATE => "The <i>entry_update</i> notification is being sent to notify that an entry has been updated.",
			self::ENTRY_UPDATE_THUMBNAIL => "The <i>entry_update_thumbnail</i> notification is being sent to notify that thumbnail of an entry has been updated.",
			self::ENTRY_UPDATE_MODERATION => "The <i>entry_update_moderation</i> notification is being sent to notify that the moderation status of an entry has been updated.",
			self::USER_ADD => "The <i>user_add</i> notification is being sent to notify that a specific user was added to the Kaltura DB.",
			self::USER_BANNED => "The <i>user_banned</i> notification is being sent to notify that a specific user was banned.",
		);
	}
}