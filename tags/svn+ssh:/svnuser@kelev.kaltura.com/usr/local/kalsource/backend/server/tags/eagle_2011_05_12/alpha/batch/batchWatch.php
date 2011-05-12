#!/usr/bin/php
<?php
/*
 * Created on Nov 25, 2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
require_once(realpath(dirname(__FILE__)).'/../config/sfrootdir.php');
define('SF_APP',         'kaltura');
define('SF_ENVIRONMENT', 'batch');
define('SF_DEBUG',       true);

$PHP_CMD = "php" ;

require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');

$batchwatch_job_list = array ( "batchConvertClient" ,
	"batchConvertServer", 
	"batchBulkUpload",
	"batchDownloadVideoServer", 
	"batchEmailServer",
	"batchEntryDeleteServer", 
	"batchMobileUploadServer",
	"batchFlattenServer",
	"batchImportServer",
	"batchNotificationServer", 
	"newBatchCommercialConvertServer", 
	"newBatchConvertClient",
	"newBatchConvertServer" , );

$count = 0;
$sleep_time = 1;
$write_to_the_log_seconds = 120;
while ( 1 )
{
	try
	{
		$files = glob ( myBatchBase::getBatchwatchPath() . "/*");
		
		SET_CONTEXT ("batchWatch" );
		
		// the files in this directory are supposed to be batch names to be started/stopped or restarted
		foreach ( $files as $file )
		{
			$batch_name = pathinfo( $file , PATHINFO_FILENAME );
			if ( $batch_name == myBatchBase::REGISTERED_BATCHS ) continue; // skip if the special file holding the registered batchs
			if ( $batch_name[0] == "_" ) continue; //myBatchBase::IGNORE_PREFIX  ) continue;
			if ( in_array ( $batch_name , $batchwatch_job_list )) 
			{
				$command = file_get_contents( $file );
		// TODO - change to fit the way we start / stop / restart  
//				$cmd_line = "service $batch_name $command";
				$cmd_line = "$PHP_CMD runBatch.php $command $batch_name ";
				TRACE ( "$cmd_line");
				$output = array();
				exec ( $cmd_line , $output , $return_var );
				TRACE ( "Result: [$return_var]\n" . print_r ( $output , true ));
				if ( $command == "stop")
				{
					$path = batchStatus::batchEnd( $batch_name );
				}
			}
			else
			{
				TRACE ( "VERY BAD!!: tying to manipulate invalid batch [$batch_name]. Ignoring..." );
			}
			
			// remove the file 
			unlink ( $file );
		}

		if ( $count > $write_to_the_log_seconds )
		{
			$count = 0;
		}
		if ( $count == 0 )
			TRACE ( "Sleeping for [$sleep_time] second. Will write to the log in ($write_to_the_log_seconds]" );
		
		sleep ( $sleep_time  );			
		$count ++;
	}
	catch ( Exception $ex )
	{
		TRACE ( "Error:" . $ex->getTrace() );
	}
}
?>
