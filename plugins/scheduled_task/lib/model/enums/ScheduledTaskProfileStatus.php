<?php
/**
 * @package plugins.scheduledTask
 * @subpackage model.enum
 */ 
interface ScheduledTaskProfileStatus extends BaseEnum
{
	/**
	 * Disabled status, won't be executed and dry run is not allow
	 */
	const DISABLED = 1;

	/**
	 * Active status, scheduled task profile will be executed on next job execution
	 */
	const ACTIVE = 2;

	/**
	 * Deleted status
	 */
	const DELETED = 3;

	/**
	 * Suspended by the system, the profile will get into suspended state when too many objects were returned for execution
	 * Scheduled task profile in suspended state won't be executed
	 * Monitoring system should monitor this state and a manual intervention will be required
	 */
	const SUSPENDED = 4;

	/**
	 * Dry run is allowed but the profile will not be executed
	 */
	const DRY_RUN_ONLY = 5;
}