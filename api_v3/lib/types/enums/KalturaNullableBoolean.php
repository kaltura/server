<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaNullableBoolean extends KalturaEnum
{
	const NULL_VALUE = -1;
	const FALSE_VALUE = 0;
	const TRUE_VALUE = 1;
	const TRUE_VALUE_STRING = 'true';
	const FALSE_VALUE_STRING = 'false';
	
	public static function toBoolean($value)
	{
		switch($value)
		{
		    case self::TRUE_VALUE_STRING:
		        return true;
		    
		    case self::FALSE_VALUE_STRING:
		        return false;
		        
			case self::FALSE_VALUE:
				return false;
				
			case self::TRUE_VALUE:
				return true;
				
			default:
				return null;
		}
	}
}