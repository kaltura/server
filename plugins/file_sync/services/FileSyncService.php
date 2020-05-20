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
	 * Update file sync by id
	 * 
	 * @action update
	 * @param int $id
	 * @param KalturaFileSync $fileSync
	 * @return KalturaFileSync
	 * 
	 * @throws FileSyncErrors::FILESYNC_ID_NOT_FOUND
	 */
	function updateAction($id, KalturaFileSync $fileSync)
	{
		$dbFileSync = FileSyncPeer::retrieveByPK($id);
		if (!$dbFileSync)
		{
			throw new KalturaAPIException(FileSyncErrors::FILESYNC_ID_NOT_FOUND, $id);
		}

		$fileSync->toUpdatableObject($dbFileSync);
		$dbFileSync->save();
		$dbFileSync->encrypt();
		
		$fileSync = new KalturaFileSync();
		$fileSync->fromObject($dbFileSync, $this->getResponseProfile());
		return $fileSync;
	}
}
