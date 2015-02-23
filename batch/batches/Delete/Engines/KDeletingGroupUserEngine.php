<?php
/**
 * @package Scheduler
 * @subpackage Delete
 */
class KDeletingGroupUserEngine extends KDeletingEngine
{
	/* (non-PHPdoc)
	 * @see KDeletingEngine::delete()
	 */
	protected function delete(KalturaFilter $filter)
	{
		return $this->deleteGroupUser($filter);
	}
	
	/**
	 * @param KalturaGroupUserFilter $filter The filter should return the list of groupUsers users that need to be deleted
	 * @return int the number of deleted groupUsers
	 */
	protected function deleteGroupUser(KalturaGroupUserFilter $filter)
	{
		$filter->orderBy = KalturaGroupUserOrderBy::CREATED_AT_ASC;
		
		$groupUsersList = KBatchBase::$kClient->groupUser->listAction($filter, $this->pager);
		if(!count($groupUsersList->objects))
			return 0;
			
		KBatchBase::$kClient->startMultiRequest();
		foreach($groupUsersList->objects as $groupUser)
		{
			/* @var $groupUser KalturaGroupUser */
			KBatchBase::$kClient->groupUser->delete($groupUser->userId, $groupUser->groupId);
		}
		$results = KBatchBase::$kClient->doMultiRequest();
		foreach($results as $index => $result)
			if(is_array($result) && isset($result['code']))
				unset($results[$index]);
				
		return count($results);
	}
}
