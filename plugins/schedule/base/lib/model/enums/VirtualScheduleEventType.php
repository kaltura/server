<?php
/**
 * @package plugins.schedule
 * @subpackage model.enum
 */
interface VirtualScheduleEventType extends BaseEnum
{
	const AGENDA = 1;
	const REGISTRATION = 2;
	const EVENT = 3;
}