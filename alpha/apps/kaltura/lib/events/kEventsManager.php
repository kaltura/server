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
	
	/**
	 * When this flag is false, deferred events are raised synchronously.
	 * 
	 * @var bool
	 */
	protected static $deferredEventsEnabled = true;
	
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
	 * Enable or disable deferred events
	 * 
	 * @param bool $enable
	 */
	public static function enableDeferredEvents($enable)
	{
		self::$deferredEventsEnabled = $enable;
	}
	
	public static function flushEvents()
	{
		if (!self::$deferredEvents)
			return;
		
		KalturaLog::debug("started flushing deferred events");
		
		while (count(self::$deferredEvents))
		{
			$deferredEvent = self::popNextDeferredEvent();
			self::raiseEvent($deferredEvent);
		}
		
		KalturaLog::debug("finished flushing deferred events");
	}
	
	private static function popNextDeferredEvent()
	{
		$deferredEvent = null;
		$deferredEventKey = null;
		
		foreach(self::$deferredEvents as $key => $event)
		{
			if (!$deferredEvent || $deferredEvent->getPriority() > $event->getPriority())
			{
				$deferredEvent = $event;
				$deferredEventKey = $key;
			}
		}
		
		unset(self::$deferredEvents[$deferredEventKey]);
		return $deferredEvent;
	}
	
	public static function raiseEventDeferred(KalturaEvent $event)
	{
		$eventKey = $event->getKey();
		
		if(!self::$deferredEventsEnabled)
			return self::raiseEvent($event);
			
		if (!is_null($eventKey))
			self::$deferredEvents[$eventKey] = $event;
		else
			self::$deferredEvents['unkeyed_'.count(self::$deferredEvents)] = $event;
	}
	
	public static function raiseEvent(KalturaEvent $event)
	{
		$consumerInterface = $event->getConsumerInterface();

		$consumers = self::getConsumers($consumerInterface);
		foreach($consumers as $consumerClass)
		{
			if (!class_exists($consumerClass))
				continue;
			
			if($event->consume(new $consumerClass()) || !($event instanceof IKalturaCancelableEvent))
				continue;
				
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