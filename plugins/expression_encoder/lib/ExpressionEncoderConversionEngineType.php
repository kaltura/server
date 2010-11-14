<?php
/**
 * @package api
 * @subpackage enum
 */
class ExpressionEncoderConversionEngineType extends KalturaPluginEnum implements conversionEngineType
{
	const EXPRESSION_ENCODER = 'ExpressionEncoder';
	
	/**
	 * @var ExpressionEncoderConversionEngineType
	 */
	protected static $instance;

	private function __construct(){}
	
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
	
	public function getEnumClass()
	{
		return 'conversionEngineType';
	}
	
	public function getPluginName()
	{
		return ExpressionEncoderPlugin::getPluginName();
	}
}
