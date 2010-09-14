<?php
/**
 * 
 * @package Scheduler
 * @subpackage Conversion
 *
 */
class KConversionEngineFlix  extends KJobConversionEngine
{
	const FLIX = "cli_encode";
	
	public function getName()
	{
		return self::FLIX;
	}
	
	public function getType()
	{
		return KalturaConversionEngineType::ON2;
	}

	public function getCmd ()
	{
		return $this->engine_config->params->on2Cmd;
	}

}
