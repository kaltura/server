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
	public function testUserJoinAndListCategory(KalturaCategory $category, KalturaUser $user, $categoryUserPermissionLevel)
	{
		$this->startSession($this->client);
		
		/* @var $category KalturaCategory */
		$category->name = $category->name . time() . rand();
		$category = $this->client->category->add($category);
		
		/* @var $user KalturaUser */
		$user->id = $user->id . time() . rand();
		$user = $this->client->user->add($user);
			
		$this->startSessionWithDiffe(SessionType::USER, $user->id);
		
		$categoryUser = new KalturaCategoryUser();
		$categoryUser->categoryId = $category->id;
		$categoryUser->userId = $user->id;
		$categoryUser->permissionLevel = $categoryUserPermissionLevel;
		
		$categoryUserResponse = null;
		try {
			$categoryUserResponse = $this->client->categoryUser->add($categoryUser);
		}
		catch(Exception $ex)
		{
			KalturaLog::err('Error: line:' . __LINE__ .' ' . $ex->getMessage());
			if ($category->appearInList == KalturaAppearInListType::CATEGORY_MEMBERS_ONLY)				
				$this->assertTrue(true, 'Category is members only and therefor user is not able to get it and to be added to');
			elseif($category->userJoinPolicy == KalturaUserJoinPolicyType::NOT_ALLOWED)
				$this->assertTrue(true, 'User is not allowed to join this category');
			else
				$this->assertTrue(false, 'Fialed to add user to category');
		}
		
		$filterCategory = new KalturaCategoryFilter();
		$filterCategory->idEqual = $category->id;		
		$categoriesListResponse = $this->client->category->listAction($filterCategory);

		
		if(!count($categoriesListResponse->objects) && $category->appearInList == KalturaAppearInListType::PARTNER_ONLY)
			$this->assertTrue(false, 'Category should returned in list since it appearInListType is set to PARTNER_ONLY');
			
		if ($category->appearInList != KalturaAppearInListType::CATEGORY_MEMBERS_ONLY)
		{
			switch ($category->userJoinPolicy)
			{
				case KalturaUserJoinPolicyType::AUTO_JOIN:
					if ($categoryUserResponse && $categoryUserResponse->status == KalturaCategoryUserStatus::ACTIVE)
						$this->assertTrue(true, 'user was  added to the category since it\'s policy is AUTO_JOIN');
					else
						$this->assertTrue(false, 'user was not added to the category although it\'s policy is AUTO_JOIN');
						
					if ($categoryUserResponse && $categoryUserResponse->permissionLevel == $category->defaultPermissionLevel)
						$this->assertTrue(true, 'user permission Level was set as default permission level');
					else
						$this->assertTrue(false, 'user permission Level [' . $categoryUserResponse->permissionLevel . '] was not set as category permission level [' . $category->defaultPermissionLevel . ']');
						
					break;
				case KalturaUserJoinPolicyType::NOT_ALLOWED:
					if (!$categoryUserResponse)
						$this->assertTrue(true, 'user cannot be added to this category since it is not allowed');
					else
						$this->assertTrue(false, 'user was added to this category although it is not allowed');
					
					if(count($categoriesListResponse->objects) && $category->appearInList == KalturaAppearInListType::CATEGORY_MEMBERS_ONLY)
						$this->assertTrue(false, 'Category should not returned in list since it appearInListType is set to CATEGORY_MEMBERS_ONLY and user is not member in this category');
					
					break;
					
				case KalturaUserJoinPolicyType::REQUEST_TO_JOIN:
					if ($categoryUserResponse && $categoryUserResponse->status == KalturaCategoryUserStatus::PENDING)
						$this->assertTrue(true, 'user was added to the category since it\'s policy is REQUEST_TO_JOIN with status pending');
					else
						$this->assertTrue(false, 'user was not added to the category or was not set to pending status although it\'s policy is REQUEST_TO_JOIN');
						
					if ($categoryUserResponse && $categoryUserResponse->permissionLevel == $category->defaultPermissionLevel)
						$this->assertTrue(true, 'user permission Level was set as default permission level');
					else
						$this->assertTrue(false, 'user permission Level [' . $categoryUserResponse->permissionLevel . '] was not set as category permission level [' . $category->defaultPermissionLevel . ']');
					
					if(count($categoriesListResponse->objects) && $category->appearInList == KalturaAppearInListType::CATEGORY_MEMBERS_ONLY)
						$this->assertTrue(false, 'Category should not returned in list since it appearInListType is set to CATEGORY_MEMBERS_ONLY and user is not member in this category');
					
					break;
					
				default:
					break;	
			}
			
		}
		
		if ($categoryUserResponse && $categoryUserResponse->status == KalturaCategoryUserStatus::ACTIVE)
		{
			try
			{
				$this->client->categoryUser->deactivate($category->id, $user->id);
			}catch (Exception $ex)
			{
				$this->assertTrue(false, 'User must be able to remove himself from the category');
			}
		}
				
		//$this->client->category->delete($category->id);
		//$this->client->user->delete($user->id);
	}

	/**
	 * Tests testCategoryHierarchy - Tests all inherited fields in category tree.
	 * @param KalturaCategory $category1
	 * @param KalturaCategory $category2
	 * @param KalturaCategory $category3
	 * @param KalturaUser $user1
	 * @dataProvider provideData
	 */
	public function testCategoryHierarchy($category1, $category2, $category3, $user1)
	{
		$this->startSession($this->client);
		
		$users = array();
		$users[] = $category1->owner;
		$users[] = $category2->owner;
		$users[] = $category3->owner;
		
		foreach($users as $userId)
		{
			if($userId == '')
				continue;
				
			try{
				$user = new KalturaUser();
				$user->id = $userId;
				$this->client->user->add($user);
			}
			catch (Exception $ex)
			{
				if($ex->getCode() != 'DUPLICATE_USER_BY_ID')
				{
					$this->assertTrue(false, 'Fialed to add user: ' . $ex->getCode());
				}
			}
		}
		
		KalturaLog::info('Add categories');
		$category1->name = $category1->name . time() . rand();
		$category1 = $this->client->category->add($category1);
		
		$category2->parentId = $category1->id;
		$category2->name = $category2->name . time() . rand();
		$category2 = $this->client->category->add($category2);
		
		$category3->parentId = $category2->id;
		$category3->name = $category3->name . time() . rand();
		$category3 = $this->client->category->add($category3);
		
		/* @var $category1 KalturaCategory */
		/* @var $category2 KalturaCategory */
		/* @var $category3 KalturaCategory */
		
		KalturaLog::info('Add user');
		/* @var $user KalturaUser */
		$user1->id = $user1->id . time() . rand();
		$user1 = $this->client->user->add($user1);
			
		$this->startSessionWithDiffe(SessionType::USER, $user1->id);
		
		$categoryUser = new KalturaCategoryUser();
		$categoryUser->categoryId = $category1->id;
		$categoryUser->userId = $user1->id;
		
		$categoryUserResponse = null;
		try {
			$categoryUserResponse = $this->client->categoryUser->add($categoryUser);
		}
		catch(Exception $ex)
		{
			KalturaLog::err('Error: line:' . __LINE__ .' ' . $ex->getMessage());
			if ($category1->appearInList == KalturaAppearInListType::CATEGORY_MEMBERS_ONLY)				
				$this->assertTrue(true, 'Category1 is members only and therefor user is not able to get it and to be added to');
			elseif($category1->userJoinPolicy == KalturaUserJoinPolicyType::NOT_ALLOWED)
				$this->assertTrue(true, 'User is not allowed to join this category');
			else
				$this->assertTrue(false, 'Fialed to add user to category');
			
			return;
		}
				
		$category1 = $this->client->category->get($category1->id);
		$category2 = $this->client->category->get($category2->id);
		$category3 = $this->client->category->get($category3->id);
		
		$filter = new KalturaCategoryUserFilter();
		$filter->categoryIdEqual = $category1->id;		
		$category1Memebers = $this->client->categoryUser->listAction($filter);
		
		$filter->categoryIdEqual = $category2->id;		
		$category2Memebers = $this->client->categoryUser->listAction($filter);
		
		$filter->categoryIdEqual = $category3->id;		
		$category3Memebers = $this->client->categoryUser->listAction($filter);
		
		if($category2->inheritanceType == KalturaInheritanceType::INHERIT)
		{
			$this->assertEquals($category1->membersCount, $category2->membersCount);
			$this->assertEquals($category1->pendingMembersCount, $category2->pendingMembersCount);
			$this->assertEquals($category1->userJoinPolicy, $category2->userJoinPolicy);
			$this->assertEquals($category1->defaultPermissionLevel, $category2->defaultPermissionLevel);
			$this->assertEquals($category1->contributionPolicy, $category2->contributionPolicy);
			
			if(count($category1Memebers->objects) == count($category2Memebers->objects))
				$this->assertAPIObjects($category1Memebers->objects[0], $category2Memebers->objects[0]);
			else
				$this->assertTrue(false);
			
			if($category3->inheritanceType == KalturaInheritanceType::INHERIT)
			{
				$this->assertEquals($category1->membersCount, $category3->membersCount);
				$this->assertEquals($category1->pendingMembersCount, $category3->pendingMembersCount);
				$this->assertEquals($category1->userJoinPolicy, $category3->userJoinPolicy);
				$this->assertEquals($category1->defaultPermissionLevel, $category3->defaultPermissionLevel);
				$this->assertEquals($category1->contributionPolicy, $category3->contributionPolicy);

				if(count($category1Memebers->objects) == count($category3Memebers->objects))
					$this->assertAPIObjects($category1Memebers->objects[0], $category3Memebers->objects[0]);
				else
					$this->assertTrue(false, 'Category3 inherit from category1, but members count are not the same, category [' . $category1->id . '] members ['. count($category1Memebers->objects) .'] category [' . $category3->id . ']  members [' . count($category3Memebers->objects) . ']');
			}
		}
		elseif($category3->inheritanceType == KalturaInheritanceType::INHERIT)
		{
			$this->assertEquals($category2->membersCount, $category3->membersCount);
			$this->assertEquals($category2->pendingMembersCount, $category3->pendingMembersCount);
			$this->assertEquals($category2->userJoinPolicy, $category3->userJoinPolicy);
			$this->assertEquals($category2->defaultPermissionLevel, $category3->defaultPermissionLevel);
			$this->assertEquals($category2->contributionPolicy, $category3->contributionPolicy);
		}
		
		$this->assertTrue(true);		
	}
	
	/**
	 * Tests testEntryEntit - Test entry entitlement for category and user.
	 * @param KalturaCategory $category
	 * @param KalturaUser $user
	 * @param KalturaBaseEntry $entry
	 * @dataProvider provideData
	 */
	public function testEntryEntit($category, $user, $entry, $categoryUserPermissionLevel)
	{
		$this->startSession($this->client);
		
		/* @var $category KalturaCategory */
		$category->name = $category->name . time() . rand();
		$category = $this->client->category->add($category);
					
		$filterCategory = new KalturaCategoryFilter();
		$filterCategory->idEqual = $category->id;		
		$categoriesListResponse = $this->client->category->listAction($filterCategory);
		
		$entry = $this->client->baseEntry->add($entry);
		
		$categoryCategoryEntry = new KalturaCategoryEntry();
		$categoryCategoryEntry->categoryId = $category->id;
		$categoryCategoryEntry->entryId = $entry->id;
		
		try {
			$categoryEntryResponse = $this->client->categoryEntry->add($categoryCategoryEntry);
		}
		catch(Exception $ex)
		{
			KalturaLog::err('Error: line:' . __LINE__ .' ' . $ex->getMessage());
			
			if ($ex->getCode() != 'CATEGORY_NOT_FOUND' && $category->appearInList == KalturaAppearInListType::CATEGORY_MEMBERS_ONLY)
			{			
				$this->assertTrue(true, 'Category is members only and cannot get this entry');
			}
			elseif($category->contributionPolicy != KalturaContributionPolicyType::ALL)
			{
				$this->assertTrue(true, 'not allowed to add entry to category');
			}
			else
			{
				$this->assertTrue(false, 'Fialed to add entry to category');
				return;
			}
		}
		
		/* @var $user KalturaUser */
		$user->id = $user->id . time() . rand();
		$user = $this->client->user->add($user);
			
		$this->startSessionWithDiffe(SessionType::USER, $user->id);
		
		//user get the entry with no permission
		try {
			$entry = $this->client->baseEntry->get($entry->id);
		}
		catch (Exception $ex)
		{
			if($ex->getCode() != 'ENTRY_ID_NOT_FOUND')
			{
				if($category->privacy != KalturaPrivacyType::MEMBERS_ONLY)
					$this->assertTrue(false, 'Category privacy if not members only and user should be able to get the entry');	
			}
		}
		
		if($category->privacy == KalturaPrivacyType::MEMBERS_ONLY)
			$this->assertTrue(false, 'Category privacy if members only and user should not be able to get the entry');	
		
		$this->startSession($this->client);
		
		$categoryUser = new KalturaCategoryUser();
		$categoryUser->categoryId = $category->id;
		$categoryUser->userId = $user->id;
		$categoryUser->permissionLevel = $categoryUserPermissionLevel;
		
		$categoryUserResponse = null;
		try {
			$categoryUserResponse = $this->client->categoryUser->add($categoryUser);
		}
		catch(Exception $ex)
		{
			KalturaLog::err('Error: line:' . __LINE__ .' ' . $ex->getMessage());
			
			if ($category->appearInList == KalturaAppearInListType::CATEGORY_MEMBERS_ONLY)
			{				
				$this->assertTrue(true, 'Category is members only and therefor user is not able to get it and to be added to');
			}
			elseif($category->userJoinPolicy == KalturaUserJoinPolicyType::NOT_ALLOWED)
			{
				$this->assertTrue(true, 'User is not allowed to join this category');
			}
			else
			{
				$this->assertTrue(false, 'Fialed to add user to category');
				return;
			}
		}
		
		if($categoryUserResponse && $categoryUserResponse->status == KalturaCategoryUserStatus::ACTIVE)
		{
				//user get the entry with no permission
			try {
				$entry = $this->client->baseEntry->get($entry->id);
			}
			catch (Exception $ex)
			{
				if($ex->getCode() != 'ENTRY_ID_NOT_FOUND')
					$this->assertTrue(false, 'Category privacy if not members only and user should be able to get the entry');	
			}
		}
	}
	
	
	/**
	 * Tests testUserAddCategoryEntry - Test user add categroyEntry
	 * @param KalturaCategory $category
	 * @param KalturaUser $user
	 * @param KalturaBaseEntry $entry
	 * @dataProvider provideData
	 */
	public function testUserAddCategoryEntry($category, $user, $entry, $categoryUserPermissionLevel)
	{
		$this->startSession($this->client);
		
		/* @var $category KalturaCategory */
		$category->name = $category->name . time() . rand();
		$category = $this->client->category->add($category);
					
		$filterCategory = new KalturaCategoryFilter();
		$filterCategory->idEqual = $category->id;		
		$categoriesListResponse = $this->client->category->listAction($filterCategory);
		
		/* @var $user KalturaUser */
		$user->id = $user->id . time() . rand();
		$user = $this->client->user->add($user);
			
		$this->startSessionWithDiffe(SessionType::USER, $user->id);
		
		$entryResponse = $this->client->baseEntry->add($entry);
		
		$categoryCategoryEntry = new KalturaCategoryEntry();
		$categoryCategoryEntry->categoryId = $category->id;
		$categoryCategoryEntry->entryId = $entryResponse->id;
		
		try {
			$categoryEntryResponse = $this->client->categoryEntry->add($categoryCategoryEntry);
		}
		catch(Exception $ex)
		{
			KalturaLog::err('Error: line:' . __LINE__ .' ' . $ex->getMessage());
			
			if ($ex->getCode() != 'CATEGORY_NOT_FOUND' && $category->appearInList == KalturaAppearInListType::CATEGORY_MEMBERS_ONLY)
			{			
				$this->assertTrue(true, 'Category is members only and cannot get this entry');
			}
			elseif($category->contributionPolicy != KalturaContributionPolicyType::ALL)
			{
				$this->assertTrue(true, 'not allowed to add entry to category');
			}
			else
			{
				$this->assertTrue(false, 'Fialed to add entry to category');
				return;
			}
		}

		$this->startSession($this->client);
		
		$categoryUser = new KalturaCategoryUser();
		$categoryUser->categoryId = $category->id;
		$categoryUser->userId = $user->id;
		$categoryUser->permissionLevel = $categoryUserPermissionLevel;
		
		$categoryUserResponse = null;
		try {
			$categoryUserResponse = $this->client->categoryUser->add($categoryUser);
		}
		catch(Exception $ex)
		{
			KalturaLog::err('Error: line:' . __LINE__ .' ' . $ex->getMessage());
			
			if ($category->appearInList == KalturaAppearInListType::CATEGORY_MEMBERS_ONLY)
			{				
				$this->assertTrue(true, 'Category is members only and therefor user is not able to get it and to be added to');
			}
			elseif($category->userJoinPolicy == KalturaUserJoinPolicyType::NOT_ALLOWED)
			{
				$this->assertTrue(true, 'User is not allowed to join this category');
			}
			else
			{
				$this->assertTrue(false, 'Fialed to add user to category');
				return;
			}
		}
		
		if($categoryUserResponse->status != KalturaCategoryUserStatus::ACTIVE)
			return;
		
		$this->startSessionWithDiffe(SessionType::USER, $user->id);
		
		$entry = $this->client->baseEntry->add($entry);
		
		$categoryCategoryEntry = new KalturaCategoryEntry();
		$categoryCategoryEntry->categoryId = $category->id;
		$categoryCategoryEntry->entryId = $entry->id;
		
		try {
			$categoryEntryResponse = $this->client->categoryEntry->add($categoryCategoryEntry);
		}
		catch(Exception $ex)
		{
			KalturaLog::err('Error: line:' . __LINE__ .' ' . $ex->getMessage());
			
			if ($ex->getCode() != 'CATEGORY_NOT_FOUND' && $category->appearInList == KalturaAppearInListType::CATEGORY_MEMBERS_ONLY)
			{			
				$this->assertTrue(false, 'Category is members only and user is a member but cannot get the category');
			}
			elseif($category->contributionPolicy != KalturaContributionPolicyType::ALL && $categoryUserResponse->permissionLevel != KalturaCategoryUserPermissionLevel::MEMBER)
			{
				$this->assertTrue(false, 'not allowed to add entry to category although should be able to');
			}
			else
			{
				$this->assertTrue(false, 'Fialed to add entry to category');
				return;
			}
		}
		
	}
	
}

