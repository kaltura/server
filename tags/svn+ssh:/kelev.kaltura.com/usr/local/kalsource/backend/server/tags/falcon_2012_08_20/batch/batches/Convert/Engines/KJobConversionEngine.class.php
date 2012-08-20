<?php
/**
 * base class for the real ConversionEngines in the system - ffmpeg,menconder and flix. 
 * 
 * @package Scheduler
 * @subpackage Conversion.engines
 */
abstract class KJobConversionEngine extends KConversionEngine
{
	/**
	 * @param KalturaConvertJobData $data
	 * @return array<KConversioEngineResult>
	 */
	protected function getExecutionCommandAndConversionString ( KalturaConvertJobData $data )
	{
		$tempPath = dirname($data->destFileSyncLocalPath);
		$this->logFilePath = $data->logFileSyncLocalPath;
		
		// assume there always will be this index
		$conv_params = $data->flavorParamsOutput;
 
		$cmd_line_arr = $this->getCmdArray($conv_params->commandLinesStr);

		$conversion_engine_result_list = array();
		
		foreach ( $cmd_line_arr as $type => $cmd_line )
		{
			if($type != $this->getType())
				continue;
				
			$cmdArr = explode(self::MILTI_COMMAND_LINE_SEPERATOR, $cmd_line);
			$lastIndex = count($cmdArr) - 1;
			
			foreach($cmdArr as $index => $cmd)
			{
				if($index == 0)
				{
					$this->inFilePath = $data->actualSrcFileSyncLocalPath;
				}
				else
				{
					$this->inFilePath = $this->outFilePath;
				}
			
				if($lastIndex > $index)
				{
					$uniqid = uniqid("tmp_convert_");
					$this->outFilePath = "$tempPath/$uniqid";
				}
				else
				{
					$this->outFilePath = $data->destFileSyncLocalPath;	
				}
				
				$cmd = trim($cmd);
				if($cmd == self::FAST_START_SIGN)
				{
					$exec_cmd = $this->getQuickStartCmdLine(true);
				}
				else
				{
					$exec_cmd = $this->getCmdLine ( $cmd , true );
				}
				$conversion_engine_result = new KConversioEngineResult( $exec_cmd , $cmd );
				$conversion_engine_result_list[] = $conversion_engine_result;
			}	
		}
		
		return $conversion_engine_result_list;			
	}	
	
	public function simulate ( KalturaConvartableJobData $data )
	{
		return  $this->simulatejob ( $data );
	}	
	
	private function simulatejob ( KalturaConvertJobData $data )
	{
		return  $this->getExecutionCommandAndConversionString ( $data );
	}
	
	public function convert ( KalturaConvartableJobData &$data )
	{
		return  $this->convertJob ( $data );
	}
	
	public function convertJob ( KalturaConvertJobData &$data )
	{

		$error_message = "";  
		if ( ! file_exists ( $data->actualSrcFileSyncLocalPath ) )
		{
			$error_message = "File [{$data->actualSrcFileSyncLocalPath}] does not exist";
			KalturaLog::err(  $error_message );
			return array ( false , $error_message );
		}

		if ( ! $data->logFileSyncLocalPath )
		{
			$data->logFileSyncLocalPath = $data->destFileSyncLocalPath . ".log";
		}
		
		$log_file = $data->logFileSyncLocalPath;
	
		// will hold a list of commands
		// there is a list (most probably holding a single command)
		// just incase there are multiple commands such as in FFMPEG's 2 pass
		$conversion_engine_result_list = $this->getExecutionCommandAndConversionString ( $data );
		
		$this->addToLogFile ( $log_file , "Executed by [" . $this->getName() . "] flavor params id [" . $data->flavorParamsOutput->flavorParamsId . "]" ) ;
		
		// add media info of source 
		$this->logMediaInfo ( $log_file , $data->actualSrcFileSyncLocalPath );
		
		$duration = 0;
		foreach ( $conversion_engine_result_list as $conversion_engine_result )
		{
			$execution_command_str = $conversion_engine_result->exec_cmd;
			$conversion_str = $conversion_engine_result->conversion_string; 
			
			$this->addToLogFile ( $log_file , $execution_command_str ) ;
			$this->addToLogFile ( $log_file , $conversion_str ) ;
				
			KalturaLog::info ( $execution_command_str );
	
			$start = microtime(true);
			// TODO add BatchEvent - before conversion + conversion engine 
			$output = system( $execution_command_str , $return_value );
			// TODO add BatchEvent - after conversion + conversion engine		
			$end = microtime(true);
	
			// 	TODO - find some place in the DB for the duration
			$duration += ( $end - $start );
						 
			KalturaLog::info ( $this->getName() . ": [$return_value] took [$duration] seconds" );
			
			$this->addToLogFile ( $log_file , $output ) ;
			
			if ( $return_value != 0 ) 
				return array ( false , "return value: [$return_value]"  );
		}
		// add media info of target
		$this->logMediaInfo ( $log_file , $data->destFileSyncLocalPath );
		
		
		return array ( true , $error_message );// indicate all was converted properly
	}	
}


