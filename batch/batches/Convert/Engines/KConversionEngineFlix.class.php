<?php
/**
 * @package Scheduler
 * @subpackage Conversion.engines
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
		return KBatchBase::$taskConfig->params->on2Cmd;
	}

}
