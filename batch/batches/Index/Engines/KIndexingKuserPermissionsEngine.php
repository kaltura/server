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
		
		$this->impersonate();
		$usersList = $this->client->user->listAction($filter, $this->pager);
		if(!count($usersList->objects))
			return 0;
			
		$this->client->startMultiRequest();
		foreach($usersList->objects as $user)
		{
			$this->client->user->index($user->id, $shouldUpdate);
		}
		$results = $this->client->doMultiRequest();
		$this->unimpersonate();
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