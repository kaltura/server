<?php

/**
 * @package plugins.scheduledTask
 * @subpackage api.objects.objectTasks
 */
class KalturaDeleteEntryObjectTask extends KalturaObjectTask
{
	public function __construct()
	{
		$this->type = ObjectTaskType::DELETE_ENTRY;
	}
}