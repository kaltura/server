<?php

/**
 * @package plugins.scheduledTask
 * @subpackage model.enum
  */
interface ScheduledTaskAddOrRemoveType extends BaseEnum
{
	const ADD = 1;
	const REMOVE = 2;
	const MOVE = 3;
}