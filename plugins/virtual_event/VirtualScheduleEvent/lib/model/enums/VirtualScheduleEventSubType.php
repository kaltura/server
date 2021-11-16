<?php
/**
 * @package plugins.virtualEvent
 * @subpackage model.enum
 */
interface VirtualScheduleEventSubType extends BaseEnum
{
	const AGENDA = 1;
	const REGISTRATION = 2;
	const MAIN_EVENT = 3;
}