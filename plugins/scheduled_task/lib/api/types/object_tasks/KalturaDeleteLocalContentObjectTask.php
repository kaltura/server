<?php

/**
 * @package plugins.scheduledTask
 * @subpackage api.objects.objectTasks
 */
class KalturaDeleteLocalContentObjectTask extends KalturaObjectTask
{
	public function __construct()
	{
		$this->type = ObjectTaskType::DELETE_LOCAL_CONTENT;
	}
}