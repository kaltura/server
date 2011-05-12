<?php
/**
 * @package Scheduler
 * @subpackage Debug
 */
require_once("bootstrap.php");

/**
 * Will import a single URL and store it in the file system.
 * The state machine of the job is as follows:
 * 	 	parse URL	(youTube is a special case) 
 * 		fetch heraders (to calculate the size of the file)
 * 		fetch file (update the job's progress - 100% is when the whole file as appeared in the header)
 * 		move the file to the archive
 * 		set the entry's new status and file details  (check if FLV) 
 *
 * @package Scheduler
 * @subpackage Debug
 */
class KSleep extends KBatchBase
{
	public static function getType()
	{
		return -1;
	}
	
	protected function init()
	{
		
	}
	
	protected function updateExclusiveJob($jobId, KalturaBatchJob $job){}
	protected function freeExclusiveJob(KalturaBatchJob $job){}
	
	public function run()
	{
//		print_r ( $this->kClient );
		$r = rand ( 2,5);
	TRACE ( "Slppeing for [$r]");
		sleep ( $r );		
	TRACE ( "Bye!");		
	}
}

?>