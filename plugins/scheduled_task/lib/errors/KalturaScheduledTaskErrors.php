<?php

/**
 * @package plugins.scheduledTask
 * @subpackage api.errors
 */
class KalturaScheduledTaskErrors extends KalturaErrors
{
	const SCHEDULED_TASK_PROFILE_NOT_FOUND = "SCHEDULED_TASK_PROFILE_NOT_FOUND;ID;Scheduled task profile [@ID@] not found";

	const SCHEDULED_TASK_PROFILE_NOT_ACTIVE = "SCHEDULED_TASK_PROFILE_NOT_ACTIVE;ID;Scheduled task profile [@ID@] not active";

	const DRY_RUN_NOT_READY = "DRY_RUN_NOT_READY;;Dry run results are not ready yet";

	const DRY_RUN_FAILED = "DRY_RUN_FAILED;;Dry run execution has failed";
}