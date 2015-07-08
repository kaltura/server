<?php
/**
 * @package Scheduler
 * @subpackage Index
 */
class KIndexingKuserPermissionsEngine extends KIndexingEngine
{
	/* (non-PHPdoc)
	 * @see KIndexingEngine::index()
	 */
	protected function index(KalturaFilter $filter, $shouldUpdate) 
	{
		$this->indexPermissionsForUsers ($filter, $shouldUpdate);
	}

	protected function indexPermissionsForUsers (KalturaFilter $filter, $shouldUpdate)
	{
		$filter->orderBy = KalturaBaseEntryOrderBy::CREATED_AT_ASC;
		
		$usersList = KBatchBase::$kClient->user->listAction($filter, $this->pager);
		if(!count($usersList->objects))
			return 0;
			
		KBatchBase::$kClient->startMultiRequest();
		foreach($usersList->objects as $user)
		{
			KBatchBase::$kClient->user->index($user->id, $shouldUpdate);
		}
		$results = KBatchBase::$kClient->doMultiRequest();
		foreach($results as $index => $result)
			if(!is_int($result))
				unset($results[$index]);
				
		if(!count($results))
			return 0;
			
		$lastIndexId = end($results);
		$this->setLastIndexId($lastIndexId);
		
		return count($results);
	}
}