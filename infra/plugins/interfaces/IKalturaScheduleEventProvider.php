<?php
/**
 * enable the plugin to get schedule events
 * @package infra
 * @subpackage Plugins
 */

interface IKalturaScheduleEventProvider extends IKalturaBase
{
	/**
	 * @param string $entryId
	 * @param array $types
	 * @param int $startTime
	 * @param int $endTime
	 * @return array<IScheduleEvent>
	 */
	public function getScheduleEvents($entryId, $types, $startTime, $endTime);
}