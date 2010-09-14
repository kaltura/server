<<?php
/**
 * 
 * @package Scheduler
 * @subpackage Conversion
 *
 */
class KConversionEngineQuickTimePlayerTools  extends KJobConversionEngine
{
	const QUICK_TIME_PLAYER_TOOLS = "quick_time_player_tools";
	
	public function getName()
	{
		return self::QUICK_TIME_PLAYER_TOOLS;
	}
	
	public function getType()
	{
		return KalturaConversionEngineType::QUICK_TIME_PLAYER_TOOLS;
	}
	
	public function getCmd ()
	{
		return $this->engine_config->params->quickTimePlayerToolsCmd;
	}
}
