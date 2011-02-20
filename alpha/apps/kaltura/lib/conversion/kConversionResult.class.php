<?php
/**
 * Will hold the result for a specific kConversionCommand.
 * This will include as much data about the kConversionEngines used and errors that occured during the conversion process
 * 
 * @package Core
 * @subpackage Conversion
 * @deprecated
 */
class kConversionResult
{
	const INDICATOR_SUFFIX = ".indicator";
	
	public $status_ok = true;
	public $conv_cmd;
	
	private $result_str;
	private $result_info = array();
	
	public function kConversionResult ( $conv_cmd )
	{
		$this->conv_cmd = $conv_cmd;	
	}

	public static function fromFile ( $file_name )
	{
		if ( ! $file_name ) throw new Exception ( "Cannot create kConversionResult from non-existing file [$file_name]" );
		return self::fromString( file_get_contents( $file_name));
	}
	public static function fromString ( $cmd_str )
	{
		return unserialize( $cmd_str );
	}
	
	public function toFile ( $file_name , $create_indicator = false )
	{
TRACE ( "Setting file [$file_name] indicator [$create_indicator]" )	;		
		if ( file_exists ( $file_name ) ) { @unlink( $file_name ); }
		file_put_contents( $file_name , $this->toString() ); // sync - OK
		if ( $create_indicator ) $this->createIndicator( $file_name );
	}
	
	public function createIndicator ( $file_name )
	{
		touch( $file_name . self::INDICATOR_SUFFIX  );	
	}
	
	public function toString ( )
	{
		return serialize( $this );
	}
		
	// the string is arbitrary info about the process 
	public function appendResult ( $str )
	{
		$this->result_str .= $str . "\n";
	}
	
	public function appendResInfo ( kConvResInfo $inf )
	{
		$this->result_info[] = $inf;
	}
	
	public function getResultInfo ( )
	{
		return $this->result_info;
	}
}

// this class will be used to extract data for reports
class kConvResInfo
{
	public $engine;
	public $target;
	public $conv_str;
	public $res;  			// ended OK or not
	public $index;			// original index in the command
	public $duration;
	public $conv_params_name;
}
?>