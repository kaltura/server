<?php
/**
 * @package plugins.schedule
 */
class SchedulePlugin extends KalturaPlugin implements IKalturaServices, IKalturaEventConsumers, IKalturaVersion
{
	const PLUGIN_NAME = 'schedule';
	const PLUGIN_VERSION_MAJOR = 1;
	const PLUGIN_VERSION_MINOR = 0;
	const PLUGIN_VERSION_BUILD = 0;
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
	
	/* (non-PHPdoc)
	 * @see IKalturaVersion::getVersion()
	 */
	public static function getVersion()
	{
		return new KalturaVersion(
			self::PLUGIN_VERSION_MAJOR,
			self::PLUGIN_VERSION_MINOR,
			self::PLUGIN_VERSION_BUILD
		);		
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaServices::getServicesMap()
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
	
	/* (non-PHPdoc)
	 * @see IKalturaEventConsumers::getEventConsumers()
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
