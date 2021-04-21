<?php

interface ILiveStreamScheduleEvent extends IScheduleEvent
{
	/**
	 * @param $context - binding string from the caller to the action
	 * @param $output - the new output value
	 * @return bool - stop processing true / false
	 */
	public function decoratorExecute($context,&$output) : bool;
}