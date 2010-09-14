<?php
require_once("bootstrap.php");
/**
 * Will run periodically and cleanup directories from old files that have a specific pattern (older than x days) 
 *
 * @package Scheduler
 * @subpackage Cleanup
 */
class KAsyncDirectoryCleanup extends KBatchBase
{
	/**
	 * @return number
	 */
	public static function getType()
	{
		return KalturaBatchJobType::CLEANUP;
	}
	
	protected function init()
	{
		
	}
	
	/**
	 * @param int $jobId
	 * @param KalturaBatchJob $job
	 * @param int $entryStatus
	 */
	protected function updateExclusiveJob($jobId, KalturaBatchJob $job, $entryStatus = null){}
	
	/**
	 * @param KalturaBatchJob $job
	 */
	protected function freeExclusiveJob(KalturaBatchJob $job){}
	
	public function run()
	{
		KalturaLog::info("Directory Cleanup is running");
		
		$path = $this->getAdditionalParams( "path" );
		$pattern = $this->getAdditionalParams( "pattern" );
		$simulateOnly = $this->getAdditionalParams( "simulateOnly" );
		$minutesOld = $this->getAdditionalParams( "minutesOld" );
		$secondsOld = $minutesOld * 60;
		
		$path_to_search = $path . $pattern ;
		KalturaLog::debug("Searching [$path_to_search]");
		$files = glob ( $path_to_search);
		KalturaLog::debug("Found [" . count ( $files ) . "] to scan");
		
		$now = time();
		$deletedCount = 0;
		foreach ( $files as $file )
		{
			if ( filemtime ( $file ) > $now - $secondsOld ) 
				continue;
			
			$deletedCount++;
			
			if ( $simulateOnly )
			{
				KalturaLog::debug( "Simulating: Deleting file [$file]"); 
				continue;
			}
			
			KalturaLog::debug("Deleting file [$file]");
			$res = @unlink ( $file );
			if ( ! $res )
				KalturaLog::debug("Error: problem while deleting [$file]");
		}
		KalturaLog::debug("Deleted $deletedCount files");
	}
}
?>