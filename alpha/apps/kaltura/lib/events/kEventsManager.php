<?php

class kEventsManager
{
	const BASE_CONSUMER_INTERFACE = 'KalturaEventConsumer';
	
	protected static $consumers = array();
	
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
			@mkdir($cacheDir, 777, true);
			
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
			
		return ($a = $b ? 0 : ($a > $b ? 1 : -1)); 
	}
	
	protected static function getConsumers($interfaceType)
	{
		if(!count(self::$consumers))
			self::loadConsumers();
			
		if(isset(self::$consumers[$interfaceType]))
			return self::$consumers[$interfaceType];
			
		return array();
	}
	
	public static function raiseEvent(KalturaEvent $event)
	{
		$consumerInterface = $event->getConsumerInterface();
		KalturaLog::debug("Event [" . get_class($event) . "] raised looking for consumers [$consumerInterface]");

		$consumers = self::getConsumers($consumerInterface);
		foreach($consumers as $consumerClass)
		{
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

	public static function continueEvent(KalturaEvent $event, $lastConsumerClass)
	{
		if(!($event instanceof IKalturaContinualEvent))
		{
			KalturaLog::debug("Event [" . get_class($event) . "] is not continual event");
			return;			
		}
		
		$consumerInterface = $event->getConsumerInterface();
		KalturaLog::debug("Event [" . get_class($event) . "] raised looking for consumers [$consumerInterface]");

		$consumers = self::getConsumers($consumerInterface);
		$continue = false;
		foreach($consumers as $consumerClass)
		{
			if(!$continue && $consumerClass != $lastConsumerClass)
				continue;
			
//			KalturaLog::debug("Event consumer [$consumerClass] called");
			$continue = $event->consume(new $consumerClass());
			
			if(!$continue && $event instanceof IKalturaCancelableEvent)
				break;
		}
	}
}