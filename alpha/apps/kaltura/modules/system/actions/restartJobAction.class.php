<?php
require_once ( "model/genericObjectWrapper.class.php" );
require_once ( "kalturaSystemAction.class.php" );

class restartJobAction extends kalturaSystemAction
{
	/**
	 * Will investigate a single entry
	 */
	public function execute()
	{
		$this->forceSystemAuthentication();

		$batchjob_id = @$_REQUEST["batchjob_id"];
		$entry_id = @$_REQUEST["entry_id"];
		
		$job = BatchJobPeer::retrieveByPK($batchjob_id);
		if ($job)
		{
			$job->setStatus(BatchJob::BATCHJOB_STATUS_PENDING);
			$job->save();
		}
		
		$this->redirect ( "/system/investigate?entry_id=$entry_id" );
	}
}
?>