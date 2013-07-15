<?php
/**
 * @package Scheduler
 * @subpackage Copy
 */
class KCopyingCategoryUserEngine extends KCopyingEngine
{
	/* (non-PHPdoc)
	 * @see KCopyingEngine::copy()
	 */
	protected function copy(KalturaFilter $filter, KalturaObjectBase $templateObject)
	{
		return $this->copyCategoryUsers($filter, $templateObject);
	}
	
	/**
	 * @param KalturaCategoryUserFilter $filter The filter should return the list of category users that need to be copied
	 * @param KalturaCategoryUser $templateObject Template object to overwrite attributes on the copied object
	 * @return int the number of copied category users
	 */
	protected function copyCategoryUsers(KalturaCategoryUserFilter $filter, KalturaCategoryUser $templateObject)
	{
		$filter->orderBy = KalturaCategoryUserOrderBy::CREATED_AT_ASC;
		
		$categoryUsersList = KBatchBase::$kClient->categoryUser->listAction($filter, $this->pager);
		if(!count($categoryUsersList->objects))
			return 0;
			
		KBatchBase::$kClient->startMultiRequest();
		foreach($categoryUsersList->objects as $categoryUser)
		{
			$newCategoryUser = $this->getNewObject($categoryUser, $templateObject);
			KBatchBase::$kClient->categoryUser->add($newCategoryUser);
		}
		
		$results = KBatchBase::$kClient->doMultiRequest();
		foreach($results as $index => $result)
			if(!is_int($result))
				unset($results[$index]);
				
		if(!count($results))
			return 0;
			
		$lastCopyId = end($results);
		$this->setLastCopyId($lastCopyId);
		
		return count($results);
	}
	
	/**
	 * @see KCopyingEngine::getNewObject()
	 * 
	 * @param KalturaCategoryUser $sourceObject
	 * @param KalturaCategoryUser $templateObject
	 * @return KalturaCategoryUser
	 */
	protected function getNewObject(KalturaObjectBase $sourceObject, KalturaObjectBase $templateObject)
	{
		$class = get_class($sourceObject);
		$newObject = new $class();
		
		/* @var $newObject KalturaCategoryUser */
		/* @var $sourceObject KalturaCategoryUser */
		/* @var $templateObject KalturaCategoryUser */
		
		$newObject->categoryId = $sourceObject->categoryId;
		$newObject->userId = $sourceObject->userId;
		$newObject->permissionLevel = $sourceObject->permissionLevel;
		$newObject->updateMethod = $sourceObject->updateMethod;
			
		if(!is_null($templateObject->categoryId))
			$newObject->categoryId = $templateObject->categoryId;
		if(!is_null($templateObject->userId))
			$newObject->userId = $templateObject->userId;
		if(!is_null($templateObject->permissionLevel))
			$newObject->permissionLevel = $templateObject->permissionLevel;
		if(!is_null($templateObject->updateMethod))
			$newObject->updateMethod = $templateObject->updateMethod;
	
		return $newObject;
	}
}
