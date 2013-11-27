<?php


/**
 * Skeleton subclass for representing a row from the 'scheduled_task_profile' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.scheduledTask
 * @subpackage model
 */
class ScheduledTaskProfile extends BaseScheduledTaskProfile
{
	public function setObjectFilter($v)
	{
		parent::setObjectFilter(serialize($v));
	}

	public function getObjectFilter()
	{
		return unserialize(parent::getObjectFilter());
	}

	public function setObjectTasks($v)
	{
		parent::setObjectTasks(serialize($v));
	}

	public function getObjectTasks()
	{
		return unserialize(parent::getObjectTasks());
	}
}
