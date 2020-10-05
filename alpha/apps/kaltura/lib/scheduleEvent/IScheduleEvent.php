<?php
interface IScheduleEvent
{
	/**
	 * @return int
	 */
	public function getStartTime();

	/**
	 * @return int
	 */
	public function getEndTime();
}