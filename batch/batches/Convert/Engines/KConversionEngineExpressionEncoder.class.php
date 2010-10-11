<?php
/**
 * @package Scheduler
 * @subpackage Conversion
 *
 */
class KConversionEngineExpressionEncoder  extends KJobConversionEngine
{
	const EXPRESSION_ENCODER = "expression_encoder";
	
	public function getName()
	{
		return self::EXPRESSION_ENCODER;
	}
	
	public function getType()
	{
		return KalturaConversionEngineType::EXPRESSION_ENCODER;
	}
	
	public function getCmd ()
	{
		return $this->engine_config->params->expressionEncoderCmd;
	}
}
