<?php

/**
 * @package plugins.scheduledTask
 * @subpackage api.objects.objectTasks
 */
class KalturaRecycleBinCleanupObjectTask extends KalturaObjectTask
{
	public function __construct()
	{
		$this->type = ObjectTaskType::RECYCLE_BIN_CLEANUP;
	}
}