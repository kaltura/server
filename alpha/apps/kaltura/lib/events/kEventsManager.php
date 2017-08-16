<?php
/**
 * @package Core
 * @subpackage events
 */
class kEventsManager
{
	const BASE_CONSUMER_INTERFACE = 'KalturaEventConsumer';
	const GENERIC_CONSUMER_INTERFACE = 'kGenericEventConsumer';
	
	protected static $consumers = array();
	
	protected static $deferredEvents = array();

	protected static $multiDeferredEvents = array();

	/**
	 * When this flag is false, all raised events are ignored.
	 * 
	 * @var bool
	 */
	protected static $eventsEnabled = true;

	/**
	 * When this flag is false, deferred events are raised synchronously.
	 * 
	 * @var bool
	 */
	protected static $deferredEventsEnabled = true;

	/**
	 * When set to true all raised events will be sent as deferred until flushEvents is called
	 * @var bool
	 */
	protected static $forceDeferredEvents = false;

	/**
	 * When this flag is false, multiDeferred events are raised as deferred
	 *
	 * @var bool
	 */
	protected static $multiDeferredEventsEnabled = false;
	
	protected static function loadConsumers()
	{
		$cachePath = kConf::get('cache_root_path') . '/EventConsumers.cache';
		if(file_exists($cachePath))
		{
			self::$consumers = unserialize(file_get_contents($cachePath));
			return;
		}
		
		$coreConsumers = kConf::get('event_consumers');
		
		$pluginConsumers = array();
		$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaEventConsumers');
		foreach($pluginInstances as $pluginInstance)
			foreach($pluginInstance->getEventConsumers() as $pluginConsumer)
				$pluginConsumers[] = $pluginConsumer;
		
		$consumers = array_merge($coreConsumers, $pluginConsumers);
		$consumersLists = array();
		foreach($consumers as $consumer)
		{
			if(!class_exists($consumer))
				continue;
				
			$clazz = new ReflectionClass($consumer);
			$interfaces = $clazz->getInterfaces();
			
			foreach($interfaces as $interface)
			{
				if($interface->name == self::BASE_CONSUMER_INTERFACE)
					continue;
					
				if(!$interface->implementsInterface(self::BASE_CONSUMER_INTERFACE))
					continue;
				
				if(!isset($consumersLists[$interface->name]))
					$consumersLists[$interface->name] = array();
					
				$consumersLists[$interface->name][] = $consumer;
			}
		}
		
		foreach($consumersLists as $interfaceName => $interfaceConsumersArray)
		{
			usort($interfaceConsumersArray, array('kEventsManager', 'compareConsumers'));
			self::$consumers[$interfaceName] = $interfaceConsumersArray;
		}
	
		$cacheDir = dirname($cachePath);
		if(!file_exists($cacheDir))
			kFile::fullMkfileDir($cacheDir, 0777, true);
			
		@file_put_contents($cachePath, serialize(self::$consumers));
	}
	
	protected static function compareConsumers($consumerA, $consumerB)
	{
		$priorities = kConf::get('event_consumers_priorities');
		$a = $b = kConf::get('event_consumers_default_priority');
		
		if(isset($priorities[$consumerA]))
			$a = $priorities[$consumerA];
		if(isset($priorities[$consumerB]))
			$b = $priorities[$consumerB];
		
		if($a == $b)
			return strcmp($consumerA, $consumerB);
		return ($a < $b ? 1 : -1);
	}
	
	protected static function getConsumers($interfaceType)
	{
		if(!count(self::$consumers))
		{
			self::loadConsumers();
		}
		$consumers = array();
		if(isset(self::$consumers[$interfaceType]))
		{
			$consumers = self::$consumers[$interfaceType];
		}
			
		if(isset(self::$consumers[self::GENERIC_CONSUMER_INTERFACE]))
		{
			foreach(self::$consumers[self::GENERIC_CONSUMER_INTERFACE] as $consumer)
				$consumers[] = $consumer;
		}
			
		return $consumers;
	}
	
	/**
	 * Enable or disable events
	 *
	 * @param bool $enable
	 */
	public static function enableEvents($enable)
	{
		self::$eventsEnabled = $enable;
	}

	/**
	 * Enable or disable deferred events
	 * 
	 * @param bool $enable
	 */
	public static function enableDeferredEvents($enable)
	{
		self::$deferredEventsEnabled = $enable;
	}

	/**
	 * force / cancel enforcement of deferred events
	 * Will work only if enableDeferredEvents = true
	 * @param bool $force
	 */
	public static function setForceDeferredEvents($force)
	{
		self::$forceDeferredEvents = $force;
	}

	/**
	 * Enable or disable multi deferred events
	 *
	 * @param bool $enable
	 */
	public static function enableMultiDeferredEvents($enable)
	{
		self::$multiDeferredEventsEnabled = $enable;
	}
	
	public static function flushEvents( $flushMultiDeferred = false )
	{
		self::$forceDeferredEvents = false;

		if (!self::$deferredEvents && !$flushMultiDeferred)
			return;
		
		KalturaLog::log("started flushing deferred events");

		while (count(self::$deferredEvents))
		{
			$deferredEvent = self::popNextDeferredEvent(self::$deferredEvents);
			self::raiseEvent($deferredEvent);
		}
		if ( $flushMultiDeferred )
		{
			self::$multiDeferredEventsEnabled = false;
			while (count(self::$multiDeferredEvents))
			{
				$multiDeferredEvent = self::popNextDeferredEvent(self::$multiDeferredEvents);
				self::raiseEvent($multiDeferredEvent);
			}
		}
		
		KalturaLog::log("finished flushing deferred events");
	}
	
	private static function popNextDeferredEvent(&$events)
	{
		$deferredEvent = null;
		$deferredEventKey = null;
		
		foreach($events as $key => $event)
		{
			if (!$deferredEvent || $deferredEvent->getPriority() > $event->getPriority())
			{
				$deferredEvent = $event;
				$deferredEventKey = $key;
			}
		}
		
		unset($events[$deferredEventKey]);
		return $deferredEvent;
	}
	
	public static function raiseEventDeferred(KalturaEvent $event)
	{
		if (!self::$eventsEnabled)
			return;

		$eventKey = $event->getKey();
		
		if(!self::$deferredEventsEnabled)
			return self::raiseEvent($event);

		$deferredEventsArray = &self::$deferredEvents;
		if ( self::$multiDeferredEventsEnabled && ($event instanceof IKalturaMultiDeferredEvent) )
		{
			$event->setPartnerCriteriaParams(myPartnerUtils::getAllPartnerCriteriaParams());
			$deferredEventsArray = &self::$multiDeferredEvents;
		}

		if (!is_null($eventKey))
			$deferredEventsArray[$eventKey] = $event;
		else
			$deferredEventsArray['unkeyed_'.count($deferredEventsArray)] = $event;
	}
	
	public static function raiseEvent(KalturaEvent $event)
	{
		if (!self::$eventsEnabled)
			return;

		if ( self::$deferredEventsEnabled && self::$forceDeferredEvents ) {
			return self::raiseEventDeferred($event);
		}

		$consumerInterface = $event->getConsumerInterface();

		$consumers = self::getConsumers($consumerInterface);
		foreach($consumers as $consumerClass)
		{
			if (!class_exists($consumerClass))
				continue;

			try{
				if($event->consume(new $consumerClass()) || !($event instanceof IKalturaCancelableEvent))
					continue;
			}
			catch (Exception $e){
				KalturaLog::err($e);
			}
				
			KalturaLog::notice("Event [" . get_class($event) . "] paused by consumer [$consumerClass]");
			break;
		}
	}

	public static function continueEvent(KalturaEvent $event, $lastConsumerClass)
	{
		if(!($event instanceof IKalturaContinualEvent))
		{
			KalturaLog::debug("Event [" . get_class($event) . "] is not continual event");
			return;
		}
		
		$consumerInterface = $event->getConsumerInterface();
		KalturaLog::debug("Event [" . get_class($event) . "] continued by [$lastConsumerClass] looking for consumers [$consumerInterface]");

		$consumers = self::getConsumers($consumerInterface);
		
		$lastConsumerFound = false;		
		foreach($consumers as $consumerClass)
		{
			if(!$lastConsumerFound && $consumerClass != $lastConsumerClass)
			{
				continue;
			}
				
			if ($consumerClass == $lastConsumerClass)
			{
				$lastConsumerFound = true;
				continue;
			}
			
//			KalturaLog::debug("Event consumer [$consumerClass] called");
			$continue = $event->consume(new $consumerClass());
			
			if(!$continue)
			{
				if($event instanceof IKalturaCancelableEvent)
				{
					break;
				}
				else
				{
					KalturaLog::debug("Event [" . get_class($event) . "] is not cancelable event");
				}
			}
		}
	}
}