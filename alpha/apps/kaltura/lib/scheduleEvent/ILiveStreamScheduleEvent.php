<?php


interface ILiveStreamScheduleEvent extends IScheduleEvent
{
	/**
	 * @return string
	 */
	public function getSourceEntryId();
}