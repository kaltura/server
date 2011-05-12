<?php
/**
 * base class for the real ConversionEngines in the system - ffmpeg,menconder and flix.
 * 
 * @package Core
 * @subpackage Conversion
 * @deprecated
 */
abstract class kConversionEngine
{
	const ENGINE_TYPE_KALTURACOM = 0;
	const ENGINE_TYPE_FFMPEG = 1;
	const ENGINE_TYPE_MENCODER = 2;
	const ENGINE_TYPE_FLIX = 3;
	const ENGINE_TYPE_ENCODINGCOM = 4;
	
	public static function getInstance ( $type )
	{
		if ( $type == self::ENGINE_TYPE_FFMPEG ) $engine = new kConversionEngineFfmpeg();
		if ( $type == self::ENGINE_TYPE_MENCODER ) $engine = new kConversionEngineMencoder();
		if ( $type == self::ENGINE_TYPE_FLIX ) $engine = new kConversionEngineFlix();
		if ( $type == self::ENGINE_TYPE_ENCODINGCOM ) $engine = new kConversionEngineEncodingCom();
		
		TRACE ( "Using conversion engine: [" . $engine->getName() . "]" );
		return $engine;
	}

	
	abstract public function getName() ;
	
	public static function getCmd() {}

	/*
	 * should return the name to display in the  result
	 */
	abstract protected function getExecutionCommandAndConversionString ( kConversionCommand $conv_cmd , $params_index );		
	
	
	public function simulate ( kConversionCommand $conv_cmd , $index = 0)
	{
		return  $this->getExecutionCommandAndConversionString ( $conv_cmd , $index );
	}
	
	// $start_params_index - the index of the kConversionParams in the kConversionCommand from which to start from. might not start at 0
	// $end_params_index - the index of the kConversionParams in the kConversionCommand to which to end at. -1 - the end
	public function convert ( kConversionCommand $conv_cmd , kConversionResult $conv_result ,
								 $start_params_index = 0, $end_params_index = -1)
	{
		if ( ! file_exists ( $conv_cmd->source_file ) )
		{
			TRACE ( "File [{$conv_cmd->source_file} does not exist" );
			return array ( false , 0 );
		}

		// make sure all the output directories exist - if not create
		kFile::fullMkdir( $conv_cmd->target_file );
		kFile::fullMkdir( $conv_cmd->log_file );
		
		self::fixConvParams ( $conv_cmd );
		
		$conv_params_list = $conv_cmd->conversion_params_list;
		
		if ( $end_params_index == -1 ) $end_params_index = count($conv_params_list);
		$start_i = max ( $start_params_index , 0 );
		$end_i = min ( $end_params_index , count($conv_params_list) );
		for ( $i=$start_i ; $i< $end_i ; ++$i )
		{
			$conv_res_info = new kConvResInfo();
			$conv_res_info->engine =  $this->getName();
			$conv_res_info->index = $i;
			
			$conv_params = @$conv_cmd->conversion_params_list[$i];
			if ( $conv_params )
			{
				$conv_res_info->conv_params_name = $conv_params->name;
			}
			
			$log_file = $conv_cmd->getLogFileWithSuffix( $i );
			$conv_res_info->target = $conv_cmd->getTargetFileWithSuffix($i);
			list ( $execution_command_str , $conversion_str ) = $this->getExecutionCommandAndConversionString ( $conv_cmd , $i );
			
			$conv_res_info->conv_str = $conversion_str;

			// assume there always will be this index
 			
			self::logMediaInfo ( $conv_cmd->source_file );
			
			self::addToLogFile ( $log_file , $execution_command_str ) ;
			self::addToLogFile ( $log_file , $conversion_str ) ;
			
			$return_value = "";

			$conv_result->appendResult( $this->getName() . ": " . $execution_command_str );
	TRACE ( $execution_command_str );
			$start = microtime(true);
			exec ( $execution_command_str , $output , $return_value );
			$end = microtime(true);
	TRACE ( $this->getName() . ": [$return_value]" );
			// $return_value == 0 is success. if not - return the index of the failed conversion 
			$conv_result->appendResult( $this->getName() . ": [$return_value]" );
			$conv_res_info->duration = ( $end - $start );
			$conv_res_info->res = $return_value;
			
			$conv_result->appendResInfo( $conv_res_info );
			if ( $return_value != 0 ) return array ( false , $i );

			self::logMediaInfo ( $conv_cmd->getTargetFileWithSuffix ( $i ) );
		}
		
		return array ( true , -1 );// indicate all was converted properly
	}	
	
	
	
	protected static function logMediaInfo ( $log_file , $file )
	{
		try
		{			
			if ( file_exists ( $file ))
			{
				$media_info = shell_exec("mediainfo ".realpath($file));
				self::addToLogFile ( $log_file ,$media_info ) ;
			}
			else
			{
				self::addToLogFile ( $log_file ,"Cannot find file [$file]" ) ;
			}
		}
		catch ( Exaption $ex ) { /* do nothing */ }		
	}
	
	// ne = not- empty 
	protected static function ne ( $param_name , $param_value )
	{
		if ( $param_value ) return $param_name . $param_value;
		else return "";
	}

	private static function addToLogFile ( $file_name , $str )
	{
		// TODO - append text to file, don't read it all and then write it again
		if ( file_exists ( $file_name ))		$log_content = @file_get_contents( $file_name ) ;
		else $log_content = "";
		$extra_content = "\n\n----------------------\n$str\n----------------------\n\n";
		file_put_contents( $file_name , $log_content . $extra_content );
	}

	protected static function fixConvParams ( kConversionCommand $conv_cmd )
	{
		$conv_params_list = $conv_cmd->conversion_params_list;
		foreach ( $conv_params_list as $conv_params )
		{
TRACE ( "Before fix: " . print_r ( $conv_params , true ));			
			kConversionHelper::fillConversionParams( $conv_cmd->source_file , $conv_params );
TRACE ( "After fix: " . print_r ( $conv_params , true ));			
		}
	}
}


?>