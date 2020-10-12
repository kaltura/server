<?php
interface IScheduleEvent
{
	/**
	 * @return int - epoch time
	 */
	public function getCalculatedStartTime();

	/**
	 * @return int - epoch time
	 */
	public function getCalculatedEndTime();
}