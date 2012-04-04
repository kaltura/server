<?php

require_once(dirname(__FILE__) . '/../../bootstrap.php');
require_once(dirname(__FILE__) . '/EntitlementTestBase.php');

/**
 * report service test case.
 */
class EntitlementTest extends EntitlementTestBase
{
	/**
	 * Set up the test initial data
	 */
	protected function setUp()
	{
		parent::setUp();
	}

	/**
	 * Tests testUsersJoinToCategory - all cases when user can ask to join to a category
	 * @param KalturaCategory $category
	 * @param KalturaUser $user
	 * @param $categoryUserPermissionLevel
	 * @dataProvider provideData
	 */
	public function testUsersJoinToCategory(KalturaCategory $category, KalturaUser $user, $categoryUserPermissionLevel)
	{
		$this->startSession($this->client);
		
		KalturaLog::info('Add category');
		/* @var $category KalturaCategory */
		$category->name = $category->name . time();
		$category = $this->client->category->add($category);
		
		KalturaLog::info('Add user');
		/* @var $user KalturaUser */
		$user->id = $user->id . time();
		$user = $this->client->user->add($user);
			
		$this->startSessionWithDiffe(SessionType::USER, $user->id);
		
		$categoryUser = new KalturaCategoryUser();
		$categoryUser->categoryId = $category->id;
		$categoryUser->userId = $user->id;
		$categoryUser->permissionLevel = $categoryUserPermissionLevel;
		
		try {
			$categoryUser = $this->client->categoryUser->add($categoryUser);
		}
		catch(Exception $ex)
		{
			KalturaLog::err('Error: line:' . __LINE__ .' ' . $ex->getMessage());
			
			if ($category->appearInList == KalturaAppearInListType::CATEGORY_MEMBERS_ONLY || 
				$category->userJoinPolicy == KalturaUserJoinPolicyType::NOT_ALLOWED)
				$this->assertTrue(true);
			else
				$this->assertTrue(false);
				
			return;
		}
		
		switch ($category->userJoinPolicy)
		{
			case KalturaUserJoinPolicyType::AUTO_JOIN:
				if ($categoryUser && $categoryUser->status == KalturaCategoryUserStatus::ACTIVE)
					$this->assertTrue(true);
				else
					$this->assertTrue(false);
					
				if ($categoryUser && $categoryUser->permissionLevel == $category->defaultPermissionLevel)
					$this->assertTrue(true);
				else
					$this->assertTrue(false);
					
				$filterCategory = new KalturaCategoryFilter();
				$filterCategory->idEqual = $category->id;
				$categoriesListResponse = $this->client->category->listAction($filterCategory);
				$categoryUser = $this->client->categoryUser->get($category->id, $user->id);
				
				if(count($categoriesListResponse->objects))
					$this->assertTrue(true);
				else
					$this->assertTrue(false);
					
				break;
			case KalturaUserJoinPolicyType::NOT_ALLOWED:
				if (!$categoryUser)
					$this->assertTrue(true);
				else
					$this->assertTrue(false);				
				break;
			default:
				break;	
			
		}
		
		$this->client->category->delete($category->id);
		$this->client->user->delete($user->id);
		
	}	
	
}

