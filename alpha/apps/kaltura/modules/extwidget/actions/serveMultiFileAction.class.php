<?php
/**
 * @package Core
 * @subpackage externalWidgets
 */
class serveMultiFileAction extends sfAction
{
	/**
	 * Serves multiple files for synchronization between datacenters 
	 */
	public function execute()
	{
		$fileSyncIds = $this->getRequestParameter( "ids" );
		$hash = $this->getRequestParameter( "hash" );

		// validate hash
		$currentDc = kDataCenterMgr::getCurrentDc();
		$currentDcId = $currentDc["id"];
		$expectedHash = md5($currentDc["secret" ] . $fileSyncIds);
		if ($hash !== $expectedHash)  
		{
			$error = "Invalid hash - ids [$fileSyncIds] got [$hash] expected [$expectedHash]";
			KalturaLog::err($error); 
			KExternalErrors::dieError(KExternalErrors::INVALID_TOKEN);
		}
		
		// load file syncs
		$fileSyncs = FileSyncPeer::retrieveByPks(explode(',', $fileSyncIds));
		if ($fileSyncs)
		{
			KalturaMonitorClient::initApiMonitor(false, 'extwidget.serveMultiFile', $fileSyncs[0]->getPartnerId());
		}
		
		// resolve file syncs
		$resolvedFileSyncs = array();
		foreach ($fileSyncs as $fileSync)
		{
			if ( $fileSync->getDc() != $currentDcId )
			{
				$error = "FileSync id [".$fileSync->getId()."] does not belong to this DC";
				KalturaLog::err($error);
				KExternalErrors::dieError(KExternalErrors::BAD_QUERY);
			}
			
			// resolve if file_sync is link
			$fileSyncResolved = kFileSyncUtils::resolve($fileSync);
			
			// check if file sync path leads to a file or a directory
			$resolvedPath = $fileSyncResolved->getFullPath();
			if (is_dir($resolvedPath))
			{
				$error = "FileSync id [".$fileSync->getId()."] is a directory";
				KalturaLog::err($error);
				KExternalErrors::dieError(KExternalErrors::BAD_QUERY);
			}
						
			if (!file_exists($resolvedPath))
			{
				$error = "Path [$resolvedPath] for fileSync id [".$fileSync->getId()."] does not exist";
				KalturaLog::err($error);
				continue;
			}
			
			$resolvedFileSyncs[$fileSync->getId()] = $fileSyncResolved;
		}
		
		$boundary = md5(uniqid('', true));
		header('Content-Type: multipart/form-data; boundary='.$boundary);

		foreach ($resolvedFileSyncs as $id => $resolvedFileSync)
		{
			echo "--$boundary\n";
			echo "Content-Type: application/octet-stream\n";
			echo "Content-Disposition: form-data; name=\"$id\"\n\n";

			echo kFileSyncUtils::getLocalContentsByFileSync($resolvedFileSync);//already checked that file
			echo "\n";
		}
		echo "--$boundary--\n";
		
		KExternalErrors::dieGracefully();
	}
}
