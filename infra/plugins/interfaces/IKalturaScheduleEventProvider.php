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
	
	/**
	 * @param $entryId - template entry ID
	 * @param $context - binding string between the caller and the final
	 * executor
	 * @param $output - the new output value
	 * @return bool - continue execute true/false
	 */
	public function applyEvents($entryId,$context,&$output) : bool;
}