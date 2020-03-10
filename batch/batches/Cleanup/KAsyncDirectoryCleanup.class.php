<?php
/**
 * @package Scheduler
 * @subpackage Cleanup
 */

/**
 * Will run periodically and cleanup directories from old files that have a specific pattern (older than x days)
 *
 * @package Scheduler
 * @subpackage Cleanup
 */
class KAsyncDirectoryCleanup extends KPeriodicWorker
{
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::CLEANUP;
	}

	/* (non-PHPdoc)
	 * @see KBatchBase::run()
	*/
	public function run($jobs = null)
	{
		$path = $this->getAdditionalParams("path");
		$pattern = $this->getAdditionalParams("pattern");
		$minDepth = $this->getAdditionalParams("minDepth");
		$simulateOnly = $this->getAdditionalParams("simulateOnly");
		$minutesOld = $this->getAdditionalParams("minutesOld");
		$searchPath = $path . $pattern;
		KalturaLog::info("Searching [$searchPath]");

		if($this->getAdditionalParams("usePHP"))
		{
			$this->deleteFilesPHP($searchPath, $minutesOld, $simulateOnly);
		}
		else
		{
			$this->deleteFilesLinux($searchPath, $minutesOld, $simulateOnly, $minDepth);
		}
	}

	// XXX - If this function forces deletion of files in the given directory. If it is used with params
	// given from the user - Please add input validation.
	protected function deleteFilesLinux($searchPath, $minutesOld, $simulateOnly, $minDepth = null)
	{
		$command = "find $searchPath ";
		if($minDepth)
		{
			$command .= "-mindepth $minDepth ";
		}
		
		$command .= "-mmin +$minutesOld -exec rm -rf {} \;";
		KalturaLog::info("Executing command: $command");

		$returnedValue = null;
		passthru($command, $returnedValue);
		KalturaLog::info("Returned value [$returnedValue]");
	}

	protected function deleteFilesPHP($searchPath, $minutesOld, $simulateOnly)
	{
		$secondsOld = $minutesOld * 60;

		$files = glob ( $searchPath);
		KalturaLog::info("Found [" . count ( $files ) . "] to scan");

		$now = time();
		KalturaLog::info("Deleting files that are " . $secondsOld ." seconds old (modified before " . date('c', $now - $secondsOld) . ")");
		$deletedCount = 0;
		foreach ( $files as $file )
		{
			$filemtime = filemtime($file);
			if ($filemtime > $now - $secondsOld)
				continue;

			if ( $simulateOnly )
			{
				KalturaLog::info( "Simulating: Deleting file [$file], it's last modification time was " . date('c', $filemtime));
				continue;
			}

			KalturaLog::info("Deleting file [$file], it's last modification time was " . date('c', $filemtime));
			$res = @unlink ( $file );
			if ( ! $res ){
				KalturaLog::err("Error: problem while deleting [$file]");
				continue;
			}
			$deletedCount++;
		}
	}
}
