<?php
require_once ( "kalturaSystemAction.class.php" );

class reconvertAction extends kalturaSystemAction
{
	/**
	 * Will reconvert an entry - should infact move to some utility function
	 */
	public function execute()
	{
		ini_set ( "max_execution_time" , 480 );
				
		$this->forceSystemAuthentication();

		myDbHelper::$use_alternative_con = null;//myDbHelper::DB_HELPER_CONN_PROPEL2;
		
		$this->conversion_profile_id = null;
		$entry_ids = trim($this->getP("entry_ids"));
		
		$this->priority = trim($this->getP ( "priority" , 3 ));
		
		$this->entry_ids = $entry_ids;
		$this->dbBatchJob = null;
		
		$result = array();
		$entry_id_arr = preg_split ( "/[, \n]/" , $entry_ids );

		foreach ( $entry_id_arr as $entry_id )
		{
			if ( ! trim($entry_id ) ) continue;
			$result[] = $this->reconvertEntry( trim($entry_id) , $this->conversion_profile_id , $this->priority );
//			$result[] = array ( $entry_id , null , null , "err" . $entry_id );
		}
		$this->entry_ids = $entry_ids;
		$this->result = $result;
		
	}
	
	
	private function reconvertEntry ( $entry_id , $conversion_profile_id , $job_priority )
	{
		$entry = entryPeer::retrieveByPK( $entry_id );
	
		$this->error = "";
		
		if ( ! $entry )
		{
			$error = "Cannot reconvert entry [$entry_id]. Might be a deleted entry";
			return array ( $entry_id , null , null , $error );	
		}
		
		$flavorAsset = flavorAssetPeer::retrieveOriginalByEntryId ( $entry_id );
		if ( ! $flavorAsset )
		{
			$flavorAsset = flavorAssetPeer::retrieveReadyWebByEntryId ( $entry_id );
			if ( ! $flavorAsset )
			{
				$flavorAssets = flavorAssetPeer::retrieveByEntryId ( $entry_id );
				if ( ! $flavorAssets )
				{
					$error = "Cannot find good enough flavor asset to re-convert from";
					return array ( $entry_id , $entry , null , $error );	
				}
				$flavorAsset = $flavorAssets[0]; // choose the first one
			}
		}
		
		$syncKey = $flavorAsset->getSyncKey ( flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET );
		$filePath = kFileSyncUtils::getReadyLocalFilePathForKey( $syncKey );
		if ( ! $filePath )
		{
			$error = "Cannot find a fileSync for the flavorAsset [" . $flavorAsset->getId() . "]" ;
			return array ( $entry_id , $entry , null , $error );
		}
		
		$dbBatchJob = new BatchJob();
		$dbBatchJob->setEntryId( $entry_id );
		$dbBatchJob->setPartnerId( $entry->getPartnerId() );
		$dbBatchJob->setStatus(BatchJob::BATCHJOB_STATUS_PENDING);
		$dbBatchJob->setDc( kDataCenterMgr::getCurrentDcId() );
		$dbBatchJob->setPriority ( $job_priority );
		$dbBatchJob->save();
		
		// creates a convert profile job
		$convertProfileData = new kConvertProfileJobData();
		$convertProfileData->setFlavorAssetId($flavorAsset->getId());
		$convertProfileData->setInputFileSyncLocalPath($filePath);
		kJobsManager::addJob($dbBatchJob, $convertProfileData, BatchJob::BATCHJOB_TYPE_CONVERT_PROFILE);
		// save again afget the addJob
		$dbBatchJob->save();

		return array ( $entry_id , $entry , $dbBatchJob , $error );
	}
}
?>