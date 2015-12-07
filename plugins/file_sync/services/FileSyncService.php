<?php
/**
 * System user service
 *
 * @service fileSync
 * @package plugins.fileSync
 * @subpackage api.services
 */
class FileSyncService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);

		// since plugin might be using KS impersonation, we need to validate the requesting
		// partnerId from the KS and not with the $_POST one
		if(!FileSyncPlugin::isAllowedPartner($this->getPartnerId()))
			throw new KalturaAPIException(KalturaErrors::FEATURE_FORBIDDEN, FileSyncPlugin::PLUGIN_NAME);
	}
	
	/**
	 * List file syce objects by filter and pager
	 *
	 * @action list
	 * @param KalturaFileSyncFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaFileSyncListResponse
	 */
	function listAction(KalturaFileSyncFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaFileSyncFilter();

		if (!$pager)
			$pager = new KalturaFilterPager();
			
		$fileSyncFilter = new FileSyncFilter();
		
		$filter->toObject($fileSyncFilter);

		$c = new Criteria();
		$fileSyncFilter->attachToCriteria($c);
		
		$totalCount = FileSyncPeer::doCount($c);
		
		$pager->attachToCriteria($c);
		$dbList = FileSyncPeer::doSelect($c);
		
		$list = KalturaFileSyncArray::fromDbArray($dbList, $this->getResponseProfile());
		$response = new KalturaFileSyncListResponse();
		$response->objects = $list;
		$response->totalCount = $totalCount;
		return $response;
	}
	
	/**
	 * @action sync
	 * @param int $fileSyncId
	 * @param file $fileData
	 * @return KalturaFileSync
	 */
	function syncAction($fileSyncId, $fileData)
	{
		$dbFileSync = FileSyncPeer::retrieveByPK($fileSyncId);
		if(!$dbFileSync)
			throw new APIException(APIErrors::INVALID_FILE_SYNC_ID, $fileSyncId);
			
		$key = kFileSyncUtils::getKeyForFileSync($dbFileSync);
		kFileSyncUtils::moveFromFile($fileData['tmp_name'], $key, false);
		
		list($file_root, $real_path) = kPathManager::getFilePathArr($key);
		$full_path = $file_root . $real_path;
		
		if(file_exists($full_path))
		{
			$dbFileSync->setFileRoot($file_root);
			$dbFileSync->setFilePath($real_path);
			$dbFileSync->setFileSizeFromPath($full_path);
			$dbFileSync->setStatus(FileSync::FILE_SYNC_STATUS_READY);
		}
		else
		{
			$dbFileSync->setFileSize(-1);
			$dbFileSync->setStatus(FileSync::FILE_SYNC_STATUS_ERROR);
		}
		$dbFileSync->save();
		
		$fileSync = new KalturaFileSync();
		$fileSync->fromObject($dbFileSync, $this->getResponseProfile());
		return $fileSync;
	}
	
}
