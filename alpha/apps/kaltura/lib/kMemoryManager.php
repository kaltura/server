<?php

class kMemoryManager
{
	static protected $peerNames = array();
	
	public static function registerPeer($peerName)
	{
		self::$peerNames[$peerName] = true;
	}
	
	public static function clearMemory()
	{
		foreach (self::$peerNames as $peerName => $dontCare)
		{
			call_user_func(array($peerName, 'clearInstancePool'));
		}
		self::$peerNames = array();

		$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaMemoryCleaner');
		foreach($pluginInstances as $pluginInstance)
			$pluginInstance->cleanMemory();
					
		if(function_exists('gc_collect_cycles')) // php 5.3 and above
			gc_collect_cycles();
	}
}