<?php
/**
 * 
 * @package Scheduler
 * @subpackage Conversion
 *
 */
class KConversionEngineMencoder  extends KJobConversionEngine
{
	const MENCODER = "mencoder";
		
	public function getName()
	{
		return self::MENCODER;
	}
	
	public function getType()
	{
		return KalturaConversionEngineType::MENCODER;
	}
	
	public function getCmd ()
	{
		return $this->engine_config->params->mencderCmd;
	}
}
