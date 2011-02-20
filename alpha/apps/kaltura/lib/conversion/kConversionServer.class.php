<?php
/**
 * Will be incharge of
 * 1. fetching a kConversionCommand exclusively (so no other server will redundantly work process it), 
 * 2. envoking the kConversionEngineMgr
 * 3. setting the kConversionResult in the correct place.
 * For version 1 - the  kConversionCommand, indicators and kConversionResult will be placed in directories.
 * For version 2 - will be able to use services to accuire the  kConversionCommand and report the kConversionResult
 * 
 * @package Core
 * @subpackage Conversion
 * @deprecated
 */
class kConversionServer extends myBatchBase
{
	public $in_path;
	private $cmd_path , $res_path, $server_id;
	private $commercial = false;
	
	public function kConversionServer ( $script_name , $cmd_path , $res_path , $commercial = false , $server_id = 1 )
	{
		$this->script_name = $script_name;
		$this->register( $script_name , $cmd_path );
		
		$this->in_path = $cmd_path;
		$this->cmd_path = $cmd_path;	
		$this->res_path = $res_path;
		$this->commercial = $commercial;
		$this->server_id = $server_id;
	}
	  
	public static function getBatchStatus( $args )	
	{	
		$batch_status = new batchStatus();
		$batch_status->batch_name = $args[0];
		$batch_status->addToPending( "Disk:" . $args[1] . "*" . kConversionHelper::INDICATOR_SUFFIX , 
			$batch_status->getDiskStatsCount( $args[0] , $args[1] ,  "*" . kConversionHelper::INDICATOR_SUFFIX ) );
		$batch_status->addToInProc( "Disk:" . $args[1] . "*" . kConversionHelper::INPROC_SUFFIX , 	
			$batch_status->getDiskStatsCount( $args[0] , $args[1] ,  "*" . kConversionHelper::INPROC_SUFFIX ) );
		
		list ( $a, $batch_status->last_log_time  ) =  $batch_status->getLogData( $batch_status->batch_name );
		return $batch_status; 
	}
	
	
	public function convert (  )
	{
		SET_CONTEXT ( "KCS($this->server_id)");
		TRACE ( "----------------- kConversionServer ------------------- ");
		TRACE ( "------ cmd_path [{$this->cmd_path}] ------");
		TRACE ( "------ res_path [{$this->res_path}] ------");
//		self::init();

		list ( $sleep_between_cycles ,
		$number_of_times_to_skip_writing_sleeping ) = self::getSleepParams( 'app_conversion_server_' );

		$temp_count = 0 ;
		while ( true )
		{
			self::exitIfDone();

			try
			{
				$this->convertAllFilesInQueue ( $temp_count == 0 ) ;
			}
			catch ( Exception  $ex )
			{
				// TODO - log exceptions !!!
				// try to recover !!
				TRACE ( $ex->getTraceAsString() );
			}

			if ( $temp_count == 0 )
			{
				TRACE ( "Ended conversion of all files in queue. Resting for a while (" . $sleep_between_cycles . ") seconds. " .
				"Will write to the log in (" . ( $sleep_between_cycles * $number_of_times_to_skip_writing_sleeping ) . ") seconds");
			}

			$temp_count++;
			if ($temp_count >= $number_of_times_to_skip_writing_sleeping ) $temp_count = 0;

			sleep ( $sleep_between_cycles );
		}
	}

	private function convertAllFilesInQueue ( $write_to_log = true )
	{
		@list ( $cmd_file , $file_name , $in_proc )= $this->getConversionCommand ( $write_to_log );
		
		if ( $cmd_file )
		{
			$conv_cmd = kConversionCommand::fromFile ( $cmd_file );
			$conv_res = kConversionEngineMgr::convert ( $conv_cmd , $this->commercial );
			$this->setConversionResult ( $file_name , $conv_cmd , $conv_res );
			$this->removeConversionCommand ( $cmd_file );
		}
		
		$this->removeInProc( $in_proc );
	}	
	
	// will return a conversion command depending on the fetch-mechanism
	// this command will be exclusive for this server ! 
	protected function getConversionCommand (  $write_to_log = true )
	{
		return kConversionHelper::getExclusiveFile( $this->cmd_path , $this->server_id , $write_to_log  );
	}

	
	private function setConversionResult ( $file_name , kConversionCommand $conv_cmd , kConversionResult $conv_res )
	{
		// the result is placed either in the default resuls path of the server OR depending on the conv_cmd 
		if ( $conv_cmd->result_path )
			$path = $conv_cmd->result_path . "/" . $file_name;
		else
			$path = $this->res_path . "/" . $file_name;
			
		TRACE ( "Writing ConvResult to [$path]\n" . print_r ( $conv_res ,true ) );			
		$conv_res->toFile ( $path , true );
	}
	
	private function removeConversionCommand ( $cmd_file )
	{
		//unlink( $cmd_file );
	}
	
	protected function removeInProc ( $in_proc )
	{
		kConversionHelper::removeInProc( $in_proc );
	}
	
}
?>