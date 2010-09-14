<?php
require_once('myBatchBase.class.php');

class myBatchSyncFiles extends myBatchBase
{
	const SLEEP_TIME = 5;
	
	const SYNC_LIMIT  = 50;
	
	private static $s_spawned = array();
	
	public static function getBatchStatus( $args )	
	{	
		$batch_status = new batchStatus();
		$batch_status->batch_name = $args[0];
		$stats = $batch_status->getDbStats( $batch_status->batch_name , BatchJob::BATCHJOB_TYPE_BULKUPLOAD );
		$batch_status->addToPending( "DB:batch_job, type=" . BatchJob::BATCHJOB_TYPE_BULKUPLOAD . " status=" . BatchJob::BATCHJOB_STATUS_PENDING , @$stats["full_stats"][BatchJob::BATCHJOB_STATUS_PENDING]["count"]);
		$batch_status->addToInProc( "DB:batch_job, type=" . BatchJob::BATCHJOB_TYPE_BULKUPLOAD . " status=" . BatchJob::BATCHJOB_STATUS_PROCESSING , @$stats["full_stats"][BatchJob::BATCHJOB_STATUS_PROCESSING]["count"] );
		$batch_status->succeedded_in_period = @$stats["full_stats"][BatchJob::BATCHJOB_STATUS_FINISHED]["count"];
		$batch_status->failed_in_period = @$stats["full_stats"][BatchJob::BATCHJOB_STATUS_FAILED]["count"];
		
		 
		$batch_status->last_log_time  = @$stats["log_timestamp"];
		return $batch_status; 
	}

	
	public function myBatchSyncFiles( $script_name )
	{
		$this->script_name = $script_name;
		$this->register( $script_name );
		
		self::initDb();

		$dc = kDataCenterMgr::getCurrentDc();
		$dc_id = $dc["id"];
		 		
		TRACE("Starting SyncFiles batch for DC [$dc_id]" );
					
		while(1)
		{
			self::exitIfDone();
			self::syncFiles();
			self::clearSpawned(); 
			// sleep
			sleep(self::SLEEP_TIME);
		}
	}

	// TODO - remove ! this is only to simulate another DataCenter
	public static function setCurrentDc( $current_dc )
	{
		kDataCenterMgr::setCurrentDc( $current_dc );
	}
	
	public static function syncFiles()
	{
		 $dc = kDataCenterMgr::getCurrentDc();
		 $dc_id = $dc["id"];
		 
		 $c = new Criteria();
		 $c->add ( FileSyncPeer::DC , $dc_id );
		 $c->add ( FileSyncPeer::STATUS , FileSync::FILE_SYNC_STATUS_PENDING );
		 $c->addAscendingOrderByColumn ( FileSyncPeer::CREATED_AT );
		 $c->add ( FileSyncPeer::ID , array_keys( self::$s_spawned ) , Criteria::NOT_IN ); // don't fetch ids that are already being worked on
		 $c->setLimit ( self::SYNC_LIMIT );
		 
		 $file_sync_list = FileSyncPeer::doSelect( $c );

		 $count = count ( $file_sync_list );
		 if  ($count == 0 )
		 	TRACE ( "Have no files to sync");
		 else
		 	TRACE ( "Have at least [$count] files to sync");
		 
		 foreach ( $file_sync_list as $file_sync )
		 {
		 	$file_sync_id = $file_sync->getId();
		 	$remote_file_sync = new remoteFileSync ( $file_sync );
		 	$remote_file_sync->syncAndUpdate();
		 	self::$s_spawned[$file_sync_id] = $remote_file_sync;
		 }
	}
	
	public static function clearSpawned()
	{
		foreach ( self::$s_spawned as $id => $remote_file_sync )
		{
			if ( $remote_file_sync->status == remoteFileSync::STATUS_ENDED )
			{
				TRACE ( "Ended id [$id]" );
				unset ( self::$s_spawned[$id] );
			}
		}		 
	}
}

class remoteFileSync 
{
	const STATUS_SPAWNED = 1;
	const STATUS_ENDED = 2;
	/**
	 * Enter description here...
	 *
	 * @var FileSync
	 */
	public $file_sync;
	public $handle;
	public $status = null;
	
	public function remoteFileSync($file_sync)
	{
		register_shutdown_function(array($this, "cleanup"));
		$this->file_sync = $file_sync;
	}

	// will spawn the process and fetch the file
	// when done will update the FileSync object's status
	public function syncAndUpdate()
	{
		$obj = kFileSyncUtils::retrieveObjectForFileSync ( $this->file_sync );
		$sync_key = $obj->getSyncKey( $this->file_sync->getObjectSubType() , $this->file_sync->getVersion() );
		// will hold the better+updated file_sync 
		$file_sync = kFileSyncUtils::createLocalPathForKey( $sync_key );
		$local_path = kFileSyncUtils::getLocalFilePathForKey( $sync_key );
		
		list ( $ready_file_sync , $local ) = kFileSyncUtils::getReadyFileSyncForKey ( $sync_key , true , false );
		
		$cmd_line = kDataCenterMgr::createCmdForRemoteDataCenter( $ready_file_sync , $local_path  );
		
		kFile::fullMkdir( $local_path );
		$this->handle = $this->spawnProcess ( $cmd_line ) ;
		
		$this->status = self::STATUS_SPAWNED;
		while ($this->handle )
		{
			$proc_status = proc_get_status($this->handle);
			if ($proc_status == false || ! $proc_status ["running"] ) // process disappeared
			{
				$this->updateFileSync($sync_key ,  $file_sync , $local_path , $cmd_line , $ready_file_sync );
				$this->status = self::STATUS_ENDED;
TRACE ( "--- ENDED status [" . print_r ( $proc_status , true ) . "] id [" . $this->file_sync->getId() ."]"  );				
				return;
			}
			
TRACE ( "--- status [" . print_r ( $proc_status , true ) . "] id [" . $this->file_sync->getId() ."]"  );			
			sleep ( 1) ; // sleep before nudging 
		}
	}
	
	
	private function updateFileSync ( $sync_key , $file_sync , $local_path , $cmd_line , $ready_file_sync )
	{
		TRACE ( __METHOD__ . ": ended [" . $sync_key->__toString() . "]" );		

return;
		// verify the size of the local file - be strict !
		if ( $ready_file_sync )
		{
			if ( filesize( $local_path ) != $ready_file_sync->getFileSize() )
			{
				$error = "File size mismatch for FileSync [" . $file_sync->getId() . "]. command: [$cmd_line]. local [" . 
						 filesize( $local_path ) ."] remote [" . $ready_file_sync->getFileSize() . "]";
							 
				$file_sync->setStatus(FileSync::FILE_SYNC_STATUS_ERROR);
				$file_sync->save();	
							 
				throw new Exception ( $error );
			}
		}
		else
		{
			$error = "Error retrieving file for FileSync [" . $file_sync->getId() . "]. command: [$cmd_line]";
			$file_sync->setStatus(FileSync::FILE_SYNC_STATUS_ERROR);
			$file_sync->save();	
					
			throw new Exception ( $error );
		}				
		
		// update the successful FileSync
		$file_sync->setFileSize( filesize( $local_path ) );
		$file_sync->setStatus(FileSync::FILE_SYNC_STATUS_READY);
		$file_sync->setReadyAt ( time() );
		$file_sync->save();			
	}

	public function cleanup()
	{
		if ($this->handle && is_resource($this->handle))
		{
			proc_terminate($this->handle);
			proc_close($this->handle);
			$this->handle = null;
		}
	}
	
	public function __destruct()
	{
		$this->cleanup();
	}
		
	public function spawnProcess($cmd_line)
	{
		$descriptorspec = array(
		0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
		1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
		2 => array("pipe", "w") // stderr is a pipe to write to
		);

		$other_options = array ( 'suppress_errors' => FALSE, 'bypass_shell' => TRUE );
		
		$handle = proc_open($cmd_line, $descriptorspec, $pipes , null , null , $other_options );

		if (is_resource($handle))
		{
			return $handle;
		}
		else
		return false;
	}	
	
}



?>