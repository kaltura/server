<?php

class kEventsManager
{
	const BASE_CONSUMER_INTERFACE = 'KalturaEventConsumer';
	
	protected static $consumers = array();
	
	protected static function loadConsumers()
	{
		$coreConsumers = kConf::get('event_consumers');
		
		$pluginConsumers = array();
		$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaEventConsumers');
		foreach($pluginInstances as $pluginInstance)
			foreach($pluginInstance->getEventConsumers() as $pluginConsumer)
			$pluginConsumers[] = $pluginConsumer;
		
		$consumers = array_merge($coreConsumers, $pluginConsumers);
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
				
				if(!isset(self::$consumers[$interface->name]))
					self::$consumers[$interface->name] = array();
					
				self::$consumers[$interface->name][] = $consumer;
			}
		}
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
			$event->consume(new $consumerClass());
		}
	}
}