<?php

/**
 * @package plugins.schedule_task
 * @subpackage Admin
 */

class ScheduleTaskUtils
{
	public static function getSchemeMap($object)
	{
		if (!$object)
			return array();

		switch(get_class($object))
		{
			case "Kaltura_Client_Reach_Type_EntryVendorTask":
				return array("id", "entryId", "userId", "status", "createdAt", "queueTime");
			case "Kaltura_Client_Type_MediaEntry":
				return array("id", "name", "userId", "views", "createdAt", "lastPlayedAt");
			default:
				return array();
		}
	}

}