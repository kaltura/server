<?php

/**
 * Entry Vendor Task Service
 *
 * @service entryVendorTask
 * @package plugins.reach
 * @subpackage api.services
 * @throws KalturaErrors::SERVICE_FORBIDDEN
 */
class EntryVendorTaskService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		
		if (!ReachPlugin::isAllowedPartner($this->getPartnerId()))
			throw new KalturaAPIException(KalturaErrors::FEATURE_FORBIDDEN, ReachPlugin::PLUGIN_NAME);
		
		if (!in_array($actionName, array('getJobs', 'updateJob', 'list', 'extendAccessKey')))
		{
			$this->applyPartnerFilterForClass('entryVendorTask');
			$this->applyPartnerFilterForClass('reachProfile');
		}
	}
	
	/**
	 * Allows you to add a entry vendor task
	 *
	 * @action add
	 * @param KalturaEntryVendorTask $entryVendorTask
	 * @return KalturaEntryVendorTask
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaReachErrors::REACH_PROFILE_NOT_FOUND
	 * @throws KalturaReachErrors::CATALOG_ITEM_NOT_FOUND
	 * @throws KalturaReachErrors::ENTRY_VENDOR_TASK_DUPLICATION
	 * @throws KalturaReachErrors::EXCEEDED_MAX_CREDIT_ALLOWED
	 */
	public function addAction(KalturaEntryVendorTask $entryVendorTask)
	{
		$entryVendorTask->validateForInsert();
		
		$dbEntry = entryPeer::retrieveByPK($entryVendorTask->entryId);
		if (!$dbEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryVendorTask->entryId);
		
		$dbReachProfile = ReachProfilePeer::retrieveActiveByPk($entryVendorTask->reachProfileId);
		if (!$dbReachProfile)
			throw new KalturaAPIException(KalturaReachErrors::REACH_PROFILE_NOT_FOUND, $entryVendorTask->reachProfileId);
		
		$dbVendorCatalogItem = VendorCatalogItemPeer::retrieveByPK($entryVendorTask->catalogItemId);
		if (!$dbVendorCatalogItem)
			throw new KalturaAPIException(KalturaReachErrors::CATALOG_ITEM_NOT_FOUND, $entryVendorTask->catalogItemId);
		
		$partnerCatalogItem = PartnerCatalogItemPeer::retrieveByCatalogItemId($entryVendorTask->catalogItemId, kCurrentContext::getCurrentPartnerId());
		if (!$partnerCatalogItem)
			throw new KalturaAPIException(KalturaReachErrors::CATALOG_ITEM_NOT_ENABLED_FOR_ACCOUNT, $entryVendorTask->catalogItemId);
		
		$taskVersion = $dbVendorCatalogItem->getTaskVersion($dbEntry->getId(), $entryVendorTask->taskJobData ? $entryVendorTask->taskJobData->toObject() : null);
		
		//check if credit has expired
		if (kReachUtils::hasCreditExpired($dbReachProfile) && $dbVendorCatalogItem->getPricing() && $dbVendorCatalogItem->getPricing()->getPricePerUnit())
			throw new KalturaAPIException(KalturaReachErrors::CREDIT_EXPIRED, $entryVendorTask->entryId, $entryVendorTask->catalogItemId);
		
		if (!kReachUtils::isEnoughCreditLeft($dbEntry, $dbVendorCatalogItem, $dbReachProfile))
			throw new KalturaAPIException(KalturaReachErrors::EXCEEDED_MAX_CREDIT_ALLOWED, $entryVendorTask->entryId, $entryVendorTask->catalogItemId);

		$lockKey = "entryVendorTask_add_" . $entryVendorTask->entryId . '_' . $entryVendorTask->catalogItemId . '_' . kCurrentContext::getCurrentPartnerId() . '_' . $taskVersion;
		$dbEntryVendorTask = kLock::runLocked($lockKey, array($this, 'addEntryVendorTaskImpl'), array($entryVendorTask, $taskVersion, $dbEntry, $dbReachProfile, $dbVendorCatalogItem));

		// return the saved object
		$entryVendorTask->fromObject($dbEntryVendorTask, $this->getResponseProfile());
		return $entryVendorTask;
	}

	public function addEntryVendorTaskImpl($entryVendorTask, $taskVersion, $dbEntry, $dbReachProfile, $dbVendorCatalogItem)
	{
		if (kReachUtils::isDuplicateTask($entryVendorTask->entryId, $entryVendorTask->catalogItemId, kCurrentContext::getCurrentPartnerId(), $taskVersion, $dbVendorCatalogItem->getAllowResubmission()))
		{
			throw new KalturaAPIException(KalturaReachErrors::ENTRY_VENDOR_TASK_DUPLICATION, $entryVendorTask->entryId, $entryVendorTask->catalogItemId, $taskVersion);
		}

		$dbEntryVendorTask = kReachManager::addEntryVendorTask($dbEntry, $dbReachProfile, $dbVendorCatalogItem, !kCurrentContext::$is_admin_session, $taskVersion);
		if(!$dbEntryVendorTask)
		{
			throw new KalturaAPIException(KalturaReachErrors::TASK_NOT_CREATED, $entryVendorTask->entryId, $entryVendorTask->catalogItemId);
		}
		$entryVendorTask->toInsertableObject($dbEntryVendorTask);
		$dbEntryVendorTask->save();
		return $dbEntryVendorTask;
	}
	
	/**
	 * Retrieve specific entry vendor task by id
	 *
	 * @action get
	 * @param int $id
	 * @return KalturaEntryVendorTask
	 * @throws KalturaReachErrors::REACH_PROFILE_NOT_FOUND
	 */
	function getAction($id)
	{
		$dbEntryVendorTask = EntryVendorTaskPeer::retrieveByPK($id);
		if (!$dbEntryVendorTask)
			throw new KalturaAPIException(KalturaReachErrors::ENTRY_VENDOR_TASK_NOT_FOUND, $id);
		
		$entryVendorTask = new KalturaEntryVendorTask();
		$entryVendorTask->fromObject($dbEntryVendorTask, $this->getResponseProfile());
		return $entryVendorTask;
	}
	
	/**
	 * List KalturaEntryVendorTask objects
	 *
	 * @action list
	 * @param KalturaEntryVendorTaskFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaEntryVendorTaskListResponse
	 */
	public function listAction(KalturaEntryVendorTaskFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaEntryVendorTaskFilter();
		
		if (!$pager)
			$pager = new KalturaFilterPager();

		$this->applyFiltersAccordingToPartner($filter);

		return $filter->getListResponse($pager, $this->getResponseProfile());
	}

	protected function applyFiltersAccordingToPartner($filter)
	{
		if (kCurrentContext::$ks_partner_id == partner::ADMIN_CONSOLE_PARTNER_ID)
		{
				$this->applyPartnerFilterForClass('entryVendorTask');
		}
		else
		{
			if (!PermissionPeer::isValidForPartner(PermissionName::REACH_VENDOR_PARTNER_PERMISSION, kCurrentContext::getCurrentPartnerId()))
			{
				$this->applyPartnerFilterForClass('entryVendorTask');
			}
			else
			{
				$filter->vendorPartnerIdEqual = kCurrentContext::getCurrentPartnerId();
			}
		}
	}
	
	/**
	 * Update entry vendor task. Only the properties that were set will be updated.
	 *
	 * @action update
	 * @param int $id vendor task id to update
	 * @param KalturaEntryVendorTask $entryVendorTask evntry vendor task to update
	 *
	 * @return KalturaEntryVendorTask
	 *
	 * @throws KalturaReachErrors::ENTRY_VENDOR_TASK_NOT_FOUND
	 */
	public function updateAction($id, KalturaEntryVendorTask $entryVendorTask)
	{
		$dbEntryVendorTask = EntryVendorTaskPeer::retrieveByPK($id);
		if (!$dbEntryVendorTask)
			throw new KalturaAPIException(KalturaReachErrors::ENTRY_VENDOR_TASK_NOT_FOUND, $id);
		
		$dbEntry = entryPeer::retrieveByPK($dbEntryVendorTask->getEntryId());
		if (!$dbEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $dbEntryVendorTask->getEntryId());
		
		$dbEntryVendorTask = $entryVendorTask->toUpdatableObject($dbEntryVendorTask);
		$dbEntryVendorTask->save();
		
		// return the saved object
		$entryVendorTask = new KalturaEntryVendorTask();
		$entryVendorTask->fromObject($dbEntryVendorTask, $this->getResponseProfile());
		return $entryVendorTask;
	}
	
	/**
	 * Approve entry vendor task for execution.
	 *
	 * @action approve
	 * @param int $id vendor task id to approve
	 * @param KalturaEntryVendorTask $entryVendorTask evntry vendor task to approve
	 *
	 * @return KalturaEntryVendorTask
	 *
	 * @throws KalturaReachErrors::ENTRY_VENDOR_TASK_NOT_FOUND
	 * @throws KalturaReachErrors::CANNOT_APPROVE_NOT_MODERATED_TASK
	 * @throws KalturaReachErrors::EXCEEDED_MAX_CREDIT_ALLOWED
	 */
	public function approveAction($id)
	{
		$dbEntryVendorTask = EntryVendorTaskPeer::retrieveByPK($id);
		if (!$dbEntryVendorTask)
			throw new KalturaAPIException(KalturaReachErrors::ENTRY_VENDOR_TASK_NOT_FOUND, $id);
		
		$dbEntry = entryPeer::retrieveByPK($dbEntryVendorTask->getEntryId());
		if (!$dbEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $dbEntryVendorTask->getEntryId());
		
		if ($dbEntryVendorTask->getStatus() != EntryVendorTaskStatus::PENDING_MODERATION)
			throw new KalturaAPIException(KalturaReachErrors::CANNOT_APPROVE_NOT_MODERATED_TASK);
		
		if (!kReachUtils::checkCreditForApproval($dbEntryVendorTask))
			throw new KalturaAPIException(KalturaReachErrors::EXCEEDED_MAX_CREDIT_ALLOWED, $dbEntryVendorTask->getEntry(), $dbEntryVendorTask->getCatalogItem());
		
		$dbEntryVendorTask->setModeratingUser($this->getKuser()->getPuserId());
		$dbEntryVendorTask->setStatus(KalturaEntryVendorTaskStatus::PENDING);
		$dbEntryVendorTask->save();
		
		// return the saved object
		$entryVendorTask = new KalturaEntryVendorTask();
		$entryVendorTask->fromObject($dbEntryVendorTask, $this->getResponseProfile());
		return $entryVendorTask;
	}
	
	/**
	 * Reject entry vendor task for execution.
	 *
	 * @action reject
	 * @param int $id vendor task id to reject
	 * @param string $rejectReason
	 * @param KalturaEntryVendorTask $entryVendorTask evntry vendor task to reject
	 *
	 * @return KalturaEntryVendorTask
	 *
	 * @throws KalturaReachErrors::ENTRY_VENDOR_TASK_NOT_FOUND
	 * @throws KalturaReachErrors::CANNOT_REJECT_NOT_MODERATED_TASK
	 */
	public function rejectAction($id,  $rejectReason = null)
	{
		$dbEntryVendorTask = EntryVendorTaskPeer::retrieveByPK($id);
		if (!$dbEntryVendorTask)
			throw new KalturaAPIException(KalturaReachErrors::ENTRY_VENDOR_TASK_NOT_FOUND, $id);
		
		$dbEntry = entryPeer::retrieveByPK($dbEntryVendorTask->getEntryId());
		if (!$dbEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $dbEntryVendorTask->getEntryId());
		
		if ($dbEntryVendorTask->getStatus() != EntryVendorTaskStatus::PENDING_MODERATION)
			throw new KalturaAPIException(KalturaReachErrors::CANNOT_REJECT_NOT_MODERATED_TASK);
		
		$dbEntryVendorTask->setModeratingUser($this->getKuser()->getPuserId());
		$dbEntryVendorTask->setStatus(KalturaEntryVendorTaskStatus::REJECTED);
		$dbEntryVendorTask->setErrDescription($rejectReason);
		$dbEntryVendorTask->save();
		
		// return the saved object
		$entryVendorTask = new KalturaEntryVendorTask();
		$entryVendorTask->fromObject($dbEntryVendorTask, $this->getResponseProfile());
		return $entryVendorTask;
	}
	
	/**
	 * get KalturaEntryVendorTask objects for specific vendor partner
	 *
	 * @action getJobs
	 * @param KalturaEntryVendorTaskFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaEntryVendorTaskListResponse
	 */
	public function getJobsAction(KalturaEntryVendorTaskFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!PermissionPeer::isValidForPartner(PermissionName::REACH_VENDOR_PARTNER_PERMISSION, kCurrentContext::$ks_partner_id))
			throw new KalturaAPIException(KalturaReachErrors::ENTRY_VENDOR_TASK_SERVICE_GET_JOB_NOT_ALLOWED, kCurrentContext::getCurrentPartnerId());
		
		if (!$filter)
			$filter = new KalturaEntryVendorTaskFilter();
		
		$filter->vendorPartnerIdEqual = kCurrentContext::getCurrentPartnerId();
		$filter->statusEqual = EntryVendorTaskStatus::PENDING;
		if (!$pager)
			$pager = new KalturaFilterPager();
		
		return $filter->getListResponse($pager, $this->getResponseProfile());
	}
	
	/**
	 * Update entry vendor task. Only the properties that were set will be updated.
	 *
	 * @action updateJob
	 * @param int $id vendor task id to update
	 * @param KalturaEntryVendorTask $entryVendorTask evntry vendor task to update
	 * @return KalturaEntryVendorTask
	 * @throws KalturaReachErrors::ENTRY_VENDOR_TASK_NOT_FOUND
	 */
	public function updateJobAction($id, KalturaEntryVendorTask $entryVendorTask)
	{
		if (!PermissionPeer::isValidForPartner(PermissionName::REACH_VENDOR_PARTNER_PERMISSION, kCurrentContext::$ks_partner_id))
			throw new KalturaAPIException(KalturaReachErrors::ENTRY_VENDOR_TASK_SERVICE_GET_JOB_NOT_ALLOWED, kCurrentContext::getCurrentPartnerId());
		
		$dbEntryVendorTask = EntryVendorTaskPeer::retrieveByPKAndVendorPartnerId($id, kCurrentContext::$ks_partner_id);
		if (!$dbEntryVendorTask)
			throw new KalturaAPIException(KalturaReachErrors::ENTRY_VENDOR_TASK_NOT_FOUND, $id);

		$partnerId = $dbEntryVendorTask->getPartnerId();
		$this->setPartnerFilters($partnerId);
		kCurrentContext::$partner_id = $partnerId;
		$dbEntryVendorTask = $entryVendorTask->toUpdatableObject($dbEntryVendorTask);
		$dbEntryVendorTask->save();
		
		// return the saved object
		$entryVendorTask = new KalturaEntryVendorTask();
		$entryVendorTask->fromObject($dbEntryVendorTask, $this->getResponseProfile());
		return $entryVendorTask;
	}
	
	/**
	 * Cancel entry task. will only occur for task in PENDING or PENDING_MODERATION status
	 *
	 * @action abort
	 * @param int $id vendor task id
	 * @param string $abortReason
	 * @return KalturaEntryVendorTask
	 * @throws KalturaReachErrors::ENTRY_VENDOR_TASK_NOT_FOUND
	 */
	public function abortAction($id, $abortReason = null)
	{
		$dbEntryVendorTask = EntryVendorTaskPeer::retrieveByPK($id);
		if (!$dbEntryVendorTask)
			throw new KalturaAPIException(KalturaReachErrors::ENTRY_VENDOR_TASK_NOT_FOUND, $id);
		
		$dbEntry = entryPeer::retrieveByPK($dbEntryVendorTask->getEntryId());
		if (!$dbEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $dbEntryVendorTask->getEntryId());
		
		/* @var EntryVendorTask $dbEntryVendorTask */
		if ($dbEntryVendorTask->getStatus() != EntryVendorTaskStatus::PENDING_MODERATION)
			throw new KalturaAPIException(KalturaReachErrors::CANNOT_ABORT_NOT_MODERATED_TASK, $id);
		
		if (!kCurrentContext::$is_admin_session && kCurrentContext::$ks_uid != $dbEntryVendorTask->getUserId())
			throw new KalturaAPIException(KalturaReachErrors::ENTRY_VENDOR_TASK_ACTION_NOT_ALLOWED, $id, kCurrentContext::$ks_uid);
		
		$dbEntryVendorTask->setStatus(KalturaEntryVendorTaskStatus::ABORTED);
		$dbEntryVendorTask->setErrDescription($abortReason);
		$dbEntryVendorTask->save();
		
		// return the saved object
		$entryVendorTask = new KalturaEntryVendorTask();
		$entryVendorTask->fromObject($dbEntryVendorTask, $this->getResponseProfile());
		return $entryVendorTask;
	}
	
	/**
	 * add batch job that sends an email with a link to download an updated CSV that contains list of users
	 *
	 * @action exportToCsv
	 * @param KalturaEntryVendorTaskFilter $filter A filter used to exclude specific tasks
	 * @return string
	 */
	function exportToCsvAction(KalturaEntryVendorTaskFilter $filter)
	{
		if (!$filter)
			$filter = new KalturaEntryVendorTaskFilter();
		$dbFilter = new EntryVendorTaskFilter();
		$filter->toObject($dbFilter);
		
		$kuser = $this->getKuser();
		if (!$kuser || !$kuser->getEmail())
			throw new KalturaAPIException(APIErrors::USER_EMAIL_NOT_FOUND, $kuser);
		
		$jobData = new kEntryVendorTaskCsvJobData();
		$jobData->setFilter($dbFilter);
		$jobData->setUserMail($kuser->getEmail());
		$jobData->setUserName($kuser->getPuserId());
		
		kJobsManager::addExportCsvJob($jobData, $this->getPartnerId(), ReachPlugin::getExportTypeCoreValue(EntryVendorTaskExportObjectType::ENTRY_VENDOR_TASK));
		
		return $kuser->getEmail();
	}
	
	
	/**
	 *
	 * Will serve a requested csv
	 * @action serveCsv
	 *
	 * @deprecated use exportCsv.serveCsv
	 * @param string $id - the requested file id
	 * @return string
	 */
	public function serveCsvAction($id)
	{
		$file_path = ExportCsvService::generateCsvPath($id, $this->getKs());
		
		return $this->dumpFile($file_path, 'text/csv');
	}

	/**
	 * Extend access key in case the existing one has expired.
	 *
	 * @action extendAccessKey
	 * @param int $id vendor task id
	 * @return KalturaEntryVendorTask
	 *
	 * @throws KalturaReachErrors::ENTRY_VENDOR_TASK_NOT_FOUND
	 * @throws KalturaReachErrors::CANNOT_EXTEND_ACCESS_KEY
	 */
	public function extendAccessKeyAction($id)
	{
		$dbEntryVendorTask = EntryVendorTaskPeer::retrieveByPK($id);
		if (!$dbEntryVendorTask)
		{
			throw new KalturaAPIException(KalturaReachErrors::ENTRY_VENDOR_TASK_NOT_FOUND, $id);
		}
		
		if($dbEntryVendorTask->getStatus() != EntryVendorTaskStatus::PROCESSING)
		{
			throw new KalturaAPIException(KalturaReachErrors::CANNOT_EXTEND_ACCESS_KEY);
		}
		
		$shouldModerateOutput = $dbEntryVendorTask->getIsOutputModerated();
		$accessKeyExpiry = $dbEntryVendorTask->getAccessKeyExpiry();
		
		try
		{
			$dbEntryVendorTask->setAccessKey(kReachUtils::generateReachVendorKs($dbEntryVendorTask->getEntryId(), $shouldModerateOutput, $accessKeyExpiry, true));
			$dbEntryVendorTask->save();
		}
		catch (Exception $e)
		{
			throw new KalturaAPIException(KalturaReachErrors::FAILED_EXTEND_ACCESS_KEY);
		}
		
		// return the saved object
		$entryVendorTask = new KalturaEntryVendorTask();
		$entryVendorTask->fromObject($dbEntryVendorTask, $this->getResponseProfile());
		return $entryVendorTask;
	}

	/**
	 * @action serve
	 * @param int $vendorPartnerId
	 * @param int $partnerId
	 * @param int $status
	 * @param string $dueDate
	 * @return file
	 */
	public function serveAction($vendorPartnerId = null, $partnerId = null, $status = null, $dueDate = null)
	{
		$filter = new KalturaEntryVendorTaskFilter();
		if($vendorPartnerId)
		{
			$filter->vendorPartnerIdEqual = $vendorPartnerId;
		}
		if ($partnerId)
		{
			kCurrentContext::$partner_id = $partnerId;
		}
		if ($status)
		{
			$filter->statusEqual = $status;
		}
		else
		{
			$filter->statusIn = EntryVendorTaskStatus::PENDING .','. EntryVendorTaskStatus::PROCESSING.','.EntryVendorTaskStatus::ERROR;
		}

		kReachUtils::setSelectedRelativeTime($dueDate, $filter);
		$filter->updatedAtGreaterThanOrEqual = time() - (VendorServiceTurnAroundTime::TEN_DAYS * 4);
		$filter->orderBy = '-createdAt';

		$pager = new KalturaFilterPager();
		$pager->pageSize = KalturaPager::MAX_PAGE_SIZE;
		$pager->pageIndex = 1;

		$content = implode(',', kReachUtils::getEntryVendorTaskCsvHeaders()) . PHP_EOL;
		$res =  $filter->getListResponse($pager, $this->getResponseProfile());
		$totalCount = min($res->totalCount, SphinxCriteria::MAX_MATCHES - 1);
		while ($totalCount > 0 && $pager->pageIndex <= 20)
		{
			foreach ($res->objects as $entryVendorTask)
			{
				$entryVendorTaskValues = kReachUtils::getObejctValues($entryVendorTask);
				$csvRowData = kReachUtils::createCsvRowData($entryVendorTaskValues, 'entryVendorTask');
				$content .= $csvRowData . PHP_EOL;
			}

			$pager->pageIndex++;
			$totalCount = $totalCount - $pager->pageSize;
			$pager->pageSize = min(KalturaPager::MAX_PAGE_SIZE, $totalCount);
			if ($pager->pageSize > 0)
			{
				$res = $filter->getListResponse($pager, $this->getResponseProfile());
			}
		}
		$fileName = "export.csv";
		header('Content-Disposition: attachment; filename="'.$fileName.'"');
		return new kRendererString($content, 'text/csv');
	}

	/**
	 * @action getServeUrl
	 * @param string $filterType
	 * @param int $filterInput
	 * @param int $status
	 * @param string $dueDate
	 * @return string $url
	 */
	public function getServeUrlAction($filterType = null, $filterInput = null, $status = null, $dueDate = null)
	{
		$finalPath = '/api_v3/service/reach_entryvendortask/action/serve/';
		if ($filterType && $filterInput && is_numeric($filterInput))
		{
			if ($filterType === 'vendorPartnerIdEqual')
			{
				$finalPath .= "vendorPartnerId/$filterInput/";
			}
			else if($filterType === 'partnerIdEqual')
			{
				$finalPath .= "partnerId/$filterInput/";
			}
		}
		if ($status)
		{
			$finalPath .= "status/$status/";
		}
		if ($dueDate)
		{
			$finalPath .= "dueDate/$dueDate/";
		}
		$finalPath .= 'ks/' . kCurrentContext::$ks;
		$url = 'http://' . kConf::get('www_host') . $finalPath;
		return $url;
	}
}
