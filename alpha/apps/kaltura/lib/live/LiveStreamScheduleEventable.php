<?php


interface LiveStreamScheduleEventable
{
	/**
	 * @return int
	 */
	public function getStartTime();

	/**
	 * @return int
	 */
	public function getEndTime();

	/**
	 * @return string
	 */
	public function getSourceEntryId();
}