<?php
/**
 * Copy an entire partner to and new one
 *
 * @package Scheduler
 * @subpackage CopyPartner
 */
class KAsyncCopyPartner extends KJobHandlerWorker
{
	const UI_CONF_TYPE_WIDGET = 1;
	const UI_CONF_TYPE_KDP3 = 8;
	
	protected $fromPartnerId;
	protected $toPartnerId;
	protected $entryIdsMap;
	protected $staticPlaylistToEntriesMap;
	
	const DUPLICATE_CATEGORY = 'DUPLICATE_CATEGORY';
	
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::COPY_PARTNER;
	}
	
	/* (non-PHPdoc)
	 * @see KBatchBase::getJobType()
	 */
	public function getJobType()
	{
		return self::getType();
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 * @return KalturaBatchJob
	 */
	protected function exec(KalturaBatchJob $job)
	{
		return $this->doCopyPartner($job, $job->data);
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 * @return KalturaBatchJob
	 */
	protected function doCopyPartner(KalturaBatchJob $job, KalturaCopyPartnerJobData $jobData)
	{
		$this->log("doCopyPartner job id [$job->id], From PID: $jobData->fromPartnerId, To PID: $jobData->toPartnerId");

		$this->fromPartnerId = $jobData->fromPartnerId;
		$this->toPartnerId = $jobData->toPartnerId;
		
		$this->entryIdsMap = array();
		$this->staticPlaylistToEntriesMap = array();
		
		$this->copyCategories();
		$this->copyUiConfs();
		// copy permssions before trying to copy additional objects such as distribution profiles which are not enabled yet for the partner
		$this->copyAllEntries();
		$this->copyStaticPlaylistsContents();
		
		return $this->closeJob($job, null, null, "doCopyPartner finished", KalturaBatchJobStatus::FINISHED);
	}
	
	/**
	 * copyAllEntries()
	 */
	protected function copyAllEntries()
	{
		$entryFilter = new KalturaBaseEntryFilter();
		$entryFilter->order = KalturaBaseEntryOrderBy::CREATED_AT_ASC;
		
		$pageFilter = new KalturaFilterPager();
		$pageFilter->pageSize = 50;
		$pageFilter->pageIndex = 1;
		
		/* @var $this->getClient() KalturaClient */
		do
		{
			// Get the source partner's entries list
			self::impersonate($this->fromPartnerId);
			$entriesList = $this->getClient()->baseEntry->listAction($entryFilter, $pageFilter);
			self::unimpersonate();

			$receivedObjectsCount = $entriesList->objects ? count($entriesList->objects) : 0;
			$pageFilter->pageIndex++;
			
			if ($receivedObjectsCount <= 0)
			{
				break;
			}
			
			// Write the source partner's entries to the destination partner
			foreach ($entriesList->objects as $entry)
			{
				self::impersonate($this->toPartnerId);
				$newEntry = $this->getClient()->baseEntry->cloneAction($entry->id);
				self::unimpersonate();
				$this->setOriginalToNewEntryId($entry->id, $newEntry->id);
				if ($entry->type == KalturaEntryType::PLAYLIST)
				{
					self::impersonate($this->fromPartnerId);
					$originalPlaylist = $this->getClient()->playlist->get($entry->id);
					self::unimpersonate();
					if ($originalPlaylist->playlistType == KalturaPlaylistType::STATIC_LIST)
					{
						$this->setStaticPlaylistToEntries($newEntry->id, $originalPlaylist->playlistContent);
					}
				}
			}
		} while ($receivedObjectsCount);
	}
	
	protected function copyCategories()
	{
		$this->log("Copying categories from partner [" . $this->fromPartnerId . "] to partner [" . $this->toPartnerId . "]");
		
		$categoryFilter = new KalturaCategoryFilter();
		$categoryFilter->status = KalturaCategoryStatus::ACTIVE;
		
		$pageFilter = new KalturaFilterPager();
		$pageFilter->pageSize = 50;
		$pageFilter->pageIndex = 1;
		/* @var $this->getClient() KalturaClient */
		do
		{
			// Get the source partner's entries list
			self::impersonate($this->fromPartnerId);
			$categoryList = $this->getClient()->category->listAction($categoryFilter, $pageFilter);
			$receivedObjectsCount = $categoryList->objects ? count($categoryList->objects) : 0;
			$pageFilter->pageIndex++;
			
			if ($receivedObjectsCount > 0)
			{
				$parentCategoryIdMapping = array();
				// Write the source partner's entries to the destination partner
				foreach ($categoryList->objects as $category)
				{
					self::impersonate($this->toPartnerId);
					$parentCatId = ($category->parentId != 0) ? $parentCategoryIdMapping[$category->parentId] : null;
					try
					{
						$result = $this->getClient()->category->cloneAction($category->id, $this->fromPartnerId, $parentCatId);
					}
					catch (KalturaException $exception)
					{
						if ($exception->getCode() === KAsyncCopyPartner::DUPLICATE_CATEGORY)
						{
							$categoryFullName = $category->fullName;
							KalturaLog::info("Category '$categoryFullName' already exists and was not cloned");

							//get the already exist category
							$categoryFilter->fullNameEqual = $categoryFullName;
							$categoryListResponse = $this->getClient()->category->listAction($categoryFilter);
							$result = null;
							if($categoryListResponse && $categoryListResponse->objects)
							{
								$result = $categoryListResponse->objects[0];
								KalturaLog::info("Searching for category by full name '$categoryFullName' found id - ". $result->id);
							}
						}
						else
						{
							throw $exception;
						}
					}
					if ($result)
					{
						$parentCategoryIdMapping[$category->id] = $result->id;
						$this->log('created category [' . $result->id . ']');
					}
				}
			}
		}
		while ($receivedObjectsCount);
		
		self::unimpersonate();
		$this->log("Copied categories from partner [" . $this->fromPartnerId . "] to partner [" . $this->toPartnerId . "]");
	}
	
	protected function copyUiConfs()
	{
		$this->copyUiConfsByType(self::UI_CONF_TYPE_WIDGET);
		$this->copyUiConfsByType(self::UI_CONF_TYPE_KDP3);
	}
	
	protected function copyUiConfsByType($uiConfType)
	{
		$this->log("Copying uiconfs from partner [" . $this->fromPartnerId . "] to partner [" . $this->toPartnerId."] with type [" . $uiConfType."]");
		
		$uiConfFilter = new KalturaUiConfFilter();
		$uiConfFilter->objTypeEqual = $uiConfType;
		
		$pageFilter = new KalturaFilterPager();
		$pageFilter->pageSize = 50;
		$pageFilter->pageIndex = 1;
		/* @var $this->getClient() KalturaClient */
		do
		{
			// Get the source partner's entries list
			self::impersonate($this->fromPartnerId);
			$client = $this->getClient()->uiConf;
			$uiConfList = $client->listAction($uiConfFilter, $pageFilter);
			
			$receivedObjectsCount = $uiConfList->objects ? count($uiConfList->objects) : 0;
			$pageFilter->pageIndex++;
			
			if ($receivedObjectsCount > 0)
			{
				// Write the source partner's entries to the destination partner
				self::impersonate($this->toPartnerId);
				foreach ($uiConfList->objects as $uiConf)
				{
					$result = $this->getClient()->uiConf->add($this->cloneUiConf($uiConf));
					if ($result)
					{
						$this->log('created uiConf [' . $result->id . ']');
					}
				}
			}
		}
		while ($receivedObjectsCount);
		
		self::unimpersonate();
	}
	
	protected function cloneUiConf($uiConf)
	{
		/* @var $uiConf KalturaUiConf */
		/* @var $newUiConf KalturaUiConf */
		$newUiConf = clone($uiConf);
		$newUiConf->id = null;
		$newUiConf->partnerId = null;
		$newUiConf->createdAt = null;
		$newUiConf->updatedAt = null;
		return $newUiConf;
	}
	
	protected function copyStaticPlaylistsContents()
	{
		$this->log("Copying static playlists contents from partner [" . $this->fromPartnerId . "] to partner [" . $this->toPartnerId . "]");
		$staticPlaylistList = $this->getStaticPlaylistToEntriesMap();
		foreach ($staticPlaylistList as $staticPlaylistId => $entryIdsList)
		{
			$fromEntryIds = explode(",", $entryIdsList);
			$toEntryIds = array();
			foreach ($fromEntryIds as $fromEntryId)
			{
				$toEntryIds[] = $this->getNewEntryId($fromEntryId);
			}
			
			$staticPlaylist = new KalturaPlaylist();
			$staticPlaylist->playlistContent = (implode(',', $toEntryIds));
			self::impersonate($this->toPartnerId);
			$this->getClient()->playlist->update($staticPlaylistId, $staticPlaylist);
			self::unimpersonate();
		}
	}
	
	public function getNewEntryId($originalEntryId)
	{
		return $this->entryIdsMap[$originalEntryId];
	}
	
	public function setOriginalToNewEntryId($originalEntryId, $newEntryId)
	{
		$this->entryIdsMap[$originalEntryId] = $newEntryId;
	}
	
	public function getStaticPlaylistToEntriesMap()
	{
		return $this->staticPlaylistToEntriesMap;
	}
	
	public function setStaticPlaylistToEntries($playlistId, $entryIdsList)
	{
		$this->staticPlaylistToEntriesMap[$playlistId] = $entryIdsList;
	}
}
