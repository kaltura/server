<?php
class dateUtils
{
	const KALTURA_FORMAT = "K";
	const MODEL_DEFAULT_FORMAT = "D";

	
	const SECOND =  1; // in seconds
	const MINUTE = 60; // in seconds
	const HOUR = 3600; // in seconds
	const DAY = 86400; // in seconds
	
	// the result date fits the DB and the calendar object that comes with symfony 
	public static function convertFromPhpDate ( $original_date , $new_format = "Y-m-d" )
	{
		return $converted_date = date ( $new_format , strtotime($original_date) );
	}
	
	/**
	 * return current time 
	 *
	 */
	public static function now()
	{
		return time();
	}
	
	public static function today()
	{
		return date ( "Y-m-d" , time() );
	}
	
	public static function todayOffset ( $delta_in_days )
	{
		$calculated_day = dateUtils::DAY * $delta_in_days + time();
		return date ( "Y-m-d" , $calculated_day  );
	}
	
	public static function nowWithMilliseconds ( )
	{
		$time = ( microtime(true) );
		$milliseconds = (int)(($time - (int)$time) * 1000);  
		return strftime( "%d/%m %H:%M:%S." , time() ) . $milliseconds ;
	}

	/**
	 * Enter description here...
	 *
	 * @param BaseObject $obj - the object to be invoked with method $date_method_str
	 * @param unknown_type $format
	 * @return formated date according to kaltura's string rules
	 */
	public static function formatKalturaDate ( BaseObject $obj , $date_method_str , $format = self::KALTURA_FORMAT )
	{
		// prepare an array with the object to invoke & the date_method
		$f = array ( $obj , $date_method_str );
		if ( $format == self::KALTURA_FORMAT )
		{
			// call parent with NULL so there will be no formating of the original date  
			$params = array ( NULL );
			$date = call_user_func_array ( $f , $params );
			 
			// now - we'll format it our way
			return kString::formatDate( $date );
		}
		else if ( $format == self::MODEL_DEFAULT_FORMAT )
		{
			// get default value from obj and pass no values
			return  call_user_func ( $f ) ;
		}
		else
		{
			// get the value from obj and use the given format
			return  call_user_func (  $f , $format ) ;
		}
	}
	
	/**
	 * Format a string in HH:MM:SS from milliseconds
	 */
	
	public static function formatDuration ( $time_in_msecs )
	{
		$time_in_secs = (int)($time_in_msecs / 1000);
		$hours =   (int)( $time_in_secs / 3600);
		$minutes = (int)(( $time_in_secs - $hours * 3600 ) / 60);
		$seconds = (int)( $time_in_secs - $minutes * 60 - $hours * 3600 ) ;
		$decimal = (int)(($time_in_msecs%1000) / 100 );
		$str = ( $hours > 10 ? "$hours:" : $hours > 0 ? "0$hours:" : "" ) .  
			( $minutes > 10 ? $minutes : "0$minutes" ) . ":" . 
			( $seconds > 10 ? $seconds : "0$seconds" ) . ".$decimal";
		return $str ;
	}
}
?>