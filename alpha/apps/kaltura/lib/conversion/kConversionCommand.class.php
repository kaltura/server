<?php
/**
 *  will hold an object that represents a conversion command for the kConversionServer / conversion manager.
 *  
 * @package Core
 * @subpackage Conversion
 * @deprecated
 */
class kConversionCommand
{
	const INDICATOR_SUFFIX = ".indicator";
	
	public $source_file;
	public $target_file;
	public $log_file;
	public $result_path;
	public $conversion_params_list;
	public $entry_id = null;
	public $commercial_transcoder = 0;
	public $forceOn2 = false;

	
	public static function fromFile ( $file_name )
	{
		if ( ! $file_name ) throw new Exception ( "Cannot create kConversionCommand from non-existing file [$file_name]" );
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
	
	public function toString ( )
	{
		return serialize( $this );
	}

	public function createIndicator ( $file_name )
	{
		touch( $file_name . self::INDICATOR_SUFFIX  );	
	}
	
	// will return the fixed target file name after combining it with a specific params->file_suffix
	// use the target's path and name, but before the extension - add the sufix - then append the extension
	public function getTargetFileWithSuffix ( $index )
	{
		return $this->combineFileWithSuffix ( $this->target_file , $index , true );
	}

	public function getLogFileWithSuffix ( $index )
	{
		if ( ! $this->log_file ) 
			return $this->getTargetFileWithSuffix ( $index );
		return $this->combineFileWithSuffix ( $this->log_file , $index , false );
	}
	
	private function combineFileWithSuffix ( $file_name , $index , $use_extension_from_suffix_if_exists = false )
	{
		$conversion_params = $this->conversion_params_list[$index];
		if ( ! $conversion_params ) return   $file_name;
		if ( ! $conversion_params->file_suffix ) return   $file_name;
/*		$combined = dirname( $file_name ) . "/" . 
					pathinfo ( $file_name , PATHINFO_FILENAME ) . 
					$conversion_params->file_suffix . "." . 
					pathinfo ( $file_name , PATHINFO_EXTENSION ) ;
*/
		$combined = $this->getFileName( $file_name , $conversion_params->file_suffix , $use_extension_from_suffix_if_exists );
		return $combined;		
		
	}
	
	public function getFileName ( $file_name , $suffix , $use_extension_from_suffix_if_exists = false )
	{
		$basename = basename( $file_name );
		if (strpos($basename, '.') !== false) 
		{ 
			$arr= explode('.', $file_name);
			$extension = end($arr); 
			$filename = substr($basename, 0, strlen($basename) - strlen($extension) - 1); 
	    } 
	    else 
	    { 
			$extension = ''; 
			$filename = $basename; 
	    } 
			
	    if ( $use_extension_from_suffix_if_exists && pathinfo($suffix, PATHINFO_EXTENSION))
	    {
	    	// in this case the suffix has an extension which should override the one of the file
			$combined = dirname( $file_name ) . "/" . 
					$filename . 
					$suffix ;	    	
	    }
	    else
	    {
			$combined = dirname( $file_name ) . "/" . 
					$filename . 
					$suffix . "." . 
					$extension ;
	    }
		return $combined;		
	}
}
?>