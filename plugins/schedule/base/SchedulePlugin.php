<?php
/**
 * @package plugins.schedule
 */
class SchedulePlugin extends KalturaPlugin implements IKalturaServices, IKalturaEventConsumers
{
	const PLUGIN_NAME = 'schedule';
	const SCHEDULE_EVENTS_CONSUMER = 'kScheduleEventsConsumer';
	
	public static function dependsOn()
	{
		$metadataDependency = new KalturaDependency(self::METADATA_PLUGIN_NAME);
		
		return array($metadataDependency);
	}
	
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/**
	 * @return array<string,string> in the form array[serviceName] = serviceClass
	 */
	public static function getServicesMap()
	{
		$map = array(
			'scheduleEvent' => 'ScheduleEventService',
			'scheduleResource' => 'ScheduleResourceService',
			'scheduleEventResource' => 'ScheduleEventResourceService',
		);
		return $map;
	}
	
	/**
	 * @return array
	 */
	public static function getEventConsumers()
	{
		return array(
			self::SCHEDULE_EVENTS_CONSUMER,
		);
	}
	
	public static function getScheduleEventmaxDuration()
	{
		$maxDuration = 60 * 60 * 24 * 365 * 2; // two years
		return kConf::get('max_schedule_event_duration', 'local', $maxDuration);
	}
	
	public static function getScheduleEventmaxRecurrences()
	{
		$maxRecurrences = 1000;
		return kConf::get('max_schedule_event_recurrences', 'local', $maxRecurrences);
	}
}
