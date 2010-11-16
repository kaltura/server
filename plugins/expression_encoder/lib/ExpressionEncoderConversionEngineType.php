<?php
/**
 * @package api
 * @subpackage enum
 */
class ExpressionEncoderConversionEngineType extends KalturaConversionEngineType
{
	const EXPRESSION_ENCODER = 'ExpressionEncoder';
	
	/**
	 * @var ExpressionEncoderConversionEngineType
	 */
	protected static $instance;
	
	/**
	 * @return ExpressionEncoderConversionEngineType
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new ExpressionEncoderConversionEngineType();
			
		return self::$instance;
	}
	
	public static function getAdditionalValues()
	{
		return array(
			'EXPRESSION_ENCODER' => self::EXPRESSION_ENCODER
		);
	}
	
	public function getPluginName()
	{
		return ExpressionEncoderPlugin::getPluginName();
	}
}
