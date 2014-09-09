<?php

/**
 * Stats Service
 *
 * @service stats
 * @package api
 * @subpackage services
 */
class LiveStatsService extends KalturaBaseService 
{
	protected function partnerRequired($actionName)
	{
		if ($actionName === 'collect') {
			return false;
		}
		
		return parent::partnerRequired($actionName);
	}
	
	
	/**
	 * Will write to the event log a single line representing the event
	 * 
	 * 
 	* 
 
	 * KalturaStatsEvent $event
	 * 
	 * @action collect
	 * @return bool
	 */
	function collectAction( KalturaLiveStatsEvent $event )
	{
		return true;
	}

	
	
}
