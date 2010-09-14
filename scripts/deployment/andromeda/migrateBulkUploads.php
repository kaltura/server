<?php
class migrateBulkUploads extends AndromedaMigration
{
	/**
	 * @param int $BUJobId
	 * @return bool
	 */	
	public static function migrateSingleBUJob($buJob)
	{
		$content = myContentStorage::getFSContentRootPath();
		$sub_types = array (
			BatchJob::FILE_SYNC_BATCHJOB_SUB_TYPE_BULKUPLOADCSV ,
			BatchJob::FILE_SYNC_BATCHJOB_SUB_TYPE_BULKUPLOADLOG ,
		);
		
		$version = null;
		$return_flag = 0;
		
		foreach ( $sub_types as $sub_type )
		{
			try
			{
				$sync_key = $buJob->getSyncKey ( $sub_type , $version );
				if ( kFileSyncUtils::file_exists( $sync_key ))
				{
					self::logPartner("     Single BulkUpload migration [{$buJob->getId()}]: already have fileSync for sub type: [$sub_type]- OK.");
					if($return_flag === 0 && $sub_type == BatchJob::FILE_SYNC_BATCHJOB_SUB_TYPE_BULKUPLOADCSV)
						$return_flag++;
				}
				else
				{
					list ( $root_path , $file_path ) = $buJob->generateFilePathArr( $sub_type , $version ) ;
					
					$full_path = $root_path . $file_path;

					if ( file_exists ( $full_path ) )
					{
						kFileSyncUtils::createSyncFileForKey( $sync_key );
						self::logPartner("     Single BulkUpload migration [{$buJob->getId()}]: created fileSync for sub type: [$sub_type]- OK.");
						if($sub_type == BatchJob::FILE_SYNC_BATCHJOB_SUB_TYPE_BULKUPLOADCSV)
							$return_flag++;
					}
					else
					{
						if ( $sub_type == BatchJob::FILE_SYNC_BATCHJOB_SUB_TYPE_BULKUPLOADLOG )
						{
							// this type only exists for old bulk uploads
							// if an old bulk-upload doesn't have a log we can't do much
							// about it and since it is a done-job we do not bother to 
							// do anything
							self::logPartner("     Single BulkUpload migration [{$buJob->getId()}]: couldn't find log file - considered OK.", Zend_Log::WARN);
						}
						else
						{
							self::logPartner("     Single BulkUpload migration [{$buJob->getId()}]: couldn't find source file - nothing much we can do...", Zend_Log::ERR);
							$return_flag = 0;
						}
					}
				}
			}
			catch ( Exception $ex )
			{
				self::logPartner("     Single BulkUpload migration [{$buJob->getId()}]: failed to create fileSync ".$ex->getMessage()." INVESTIGATE !", Zend_Log::CRIT);
				$return_flag = 0;
			}
		}
		return (bool)$return_flag;
	}
	
	private static $failedIds;
	
	public static function getFailedIds()
	{
		return self::$failedIds;
	}
	
	/**
	 * migrate a list of bulk-upload jobs. return value is integer: 0 - all failed, 1 - all OK, 2 - some failed
	 * @param array $arrBUjobs
	 * @return int
	 */
	public static function migrateBUJobs($arrBUjobs)
	{
		if(!count($arrBUjobs) || !is_array($arrBUjobs))
			return FALSE;
		self::$failedIds = array();
		foreach($arrBUjobs as $BUJob)
		{
			$result = self::migrateSingleBUJob($BUJob);
			if(!$result) self::$failedIds[] = $BUJob->getId();
		}
		if(count(self::$failedIds) == 0)
			return 1;
		elseif(count(self::$failedIds) == count($arrBUjobs))
			return 0;
		else
			return 2;
	}
}