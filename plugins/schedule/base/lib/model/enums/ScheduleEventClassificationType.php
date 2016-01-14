<?php
/**
 * @package plugins.schedule
 * @subpackage model.enum
 */
interface ScheduleEventClassificationType extends BaseEnum
{
	const PUBLIC_EVENT = 1;
	const PRIVATE_EVENT = 2;
	const CONFIDENTIAL_EVENT = 3;
}