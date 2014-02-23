<?php


/**
 * Skeleton subclass for performing query and update operations on the 'scheduled_task_profile' table.
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
class ScheduledTaskProfilePeer extends BaseScheduledTaskProfilePeer
{
	/* (non-PHPdoc)
	 * @see BaseScheduledTaskProfilePeer::setDefaultCriteriaFilter()
	 */
	public static function setDefaultCriteriaFilter()
	{
		if (self::$s_criteria_filter == null)
			self::$s_criteria_filter = new criteriaFilter ();

		$c = new Criteria ();
		$c->add(ScheduledTaskProfilePeer::STATUS, ScheduledTaskProfileStatus::DELETED, Criteria::NOT_EQUAL);

		self::$s_criteria_filter->setFilter($c);
	}
}
