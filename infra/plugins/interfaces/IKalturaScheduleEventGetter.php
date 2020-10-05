<?php
/**
 * enable the plugin to get schedule events
 * @package infra
 * @subpackage Plugins
 */

interface IKalturaScheduleEventGetter extends IKalturaBase
{
	/**
	 * @param string $entryId
	 * @param array $types
	 * @param int $startTime
	 * @param int $endTime
	 * @return array<ILiveStreamScheduleEvent>
	 */
	public function getScheduleEvents($entryId, $types, $startTime, $endTime);
}