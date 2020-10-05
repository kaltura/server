<?php
/**
 * @package plugins.schedule
 */
class SchedulePlugin extends KalturaPlugin implements IKalturaServices, IKalturaEventConsumers, IKalturaVersion, IKalturaObjectLoader, IKalturaScheduleEventGetter
{
	const PLUGIN_NAME = 'schedule';
	const PLUGIN_VERSION_MAJOR = 1;
	const PLUGIN_VERSION_MINOR = 0;
	const PLUGIN_VERSION_BUILD = 0;
	const SCHEDULE_EVENTS_CONSUMER = 'kScheduleEventsConsumer';
	const ICAL_RESPONSE_TYPE = 'ical';
	
	public static function dependsOn()
	{
		$metadataDependency = new KalturaDependency(self::METADATA_PLUGIN_NAME);
		
		return array($metadataDependency);
	}
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/*
	 * (non-PHPdoc)
	 * @see IKalturaVersion::getVersion()
	 */
	public static function getVersion()
	{
		return new KalturaVersion(self::PLUGIN_VERSION_MAJOR, self::PLUGIN_VERSION_MINOR, self::PLUGIN_VERSION_BUILD);
	}
	
	/*
	 * (non-PHPdoc)
	 * @see IKalturaServices::getServicesMap()
	 */
	public static function getServicesMap()
	{
		$map = array('scheduleEvent' => 'ScheduleEventService', 'scheduleResource' => 'ScheduleResourceService', 'scheduleEventResource' => 'ScheduleEventResourceService');
		return $map;
	}
	
	/*
	 * (non-PHPdoc)
	 * @see IKalturaEventConsumers::getEventConsumers()
	 */
	public static function getEventConsumers()
	{
		return array(self::SCHEDULE_EVENTS_CONSUMER);
	}
	
	/*
	 * (non-PHPdoc)
	 * @see IKalturaObjectLoader::loadObject()
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		if($baseClass == 'KalturaSerializer' && $enumValue == self::ICAL_RESPONSE_TYPE)
			return new KalturaICalSerializer();
		
		return null;
	}
	
	/*
	 * (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if($baseClass == 'KalturaSerializer' && $enumValue == self::ICAL_RESPONSE_TYPE)
			return 'KalturaICalSerializer';
		
		return null;
	}

	public static function getSingleScheduleEventMaxDuration()
	{
		$maxSingleScheduleEventDuration = 60 * 60 * 24; // 24 hours
		return kConf::get('max_single_schedule_event_duration', 'local', $maxSingleScheduleEventDuration);
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

	/**
	 * @param string $entryId
	 * @param array $types
	 * @param int $startTime
	 * @param int $endTime
	 * @return array<ILiveStreamScheduleEvent>
	 */
	public function getScheduleEvents($entryId, $types, $startTime, $endTime)
	{
		$events = array();
		$scheduleEvents = ScheduleEventPeer::retrieveByTemplateEntryIdAndTypes($entryId, $types, $startTime);
		foreach ($scheduleEvents as $scheduleEvent)
		{
			/* @var LiveStreamScheduleEvent $scheduleEvent*/
			if ($scheduleEvent->isTimeInEvent($startTime, $endTime))
			{
				$events[] = $scheduleEvent;
			}
		}
		return $events;
	}
}
