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
		$category->name = $category->name . time();
		$category = $this->client->category->add($category);
		
		/* @var $user KalturaUser */
		$user->id = $user->id . time();
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

		
		if(count($categoriesListResponse->objects))
		{
			if($category->appearInList == KalturaAppearInListType::PARTNER_ONLY)
				$this->assertTrue(true, 'Category returned since it should appear in list');
			else
				$this->assertTrue(false, 'Category is not parent only and should not be appear in list');
		}
		
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
						$this->assertTrue(false, 'user permission Level was not set as default permission level');
						
					break;
				case KalturaUserJoinPolicyType::NOT_ALLOWED:
					if (!$categoryUserResponse)
						$this->assertTrue(true, 'user cannot be added to this category since it is not allowed');
					else
					{
						$this->assertTrue(false, 'user was added to this category although it is not allowed');
					}				
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
		KalturaLog::info('Add categories');
		$category1->name = $category1->name . time();
		$category1 = $this->client->category->add($category1);
		
		$category2->parentId = $category1->id;
		$category2->name = $category2->name . time();
		$category2 = $this->client->category->add($category2);
		
		$category3->parentId = $category2->id;
		$category3->name = $category3->name . time();
		$category3 = $this->client->category->add($category3);
		
		/* @var $category1 KalturaCategory */
		/* @var $category2 KalturaCategory */
		/* @var $category3 KalturaCategory */
		
		KalturaLog::info('Add user');
		/* @var $user KalturaUser */
		$user1->id = $user1->id . time();
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
					$this->assertAPIObjects($category1Memebers->objects[0], $category2Memebers->objects[0]);
				else
					$this->assertTrue(false);
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
	public function testEntryEntit($category, $user, $entry)
	{
		$entry = $this->client->baseEntry->add($entry);
		
		$category->name = $category->name . time();
		$category = $this->client->category->add($category);
		
		$user->id = $user->id . time();
		$user = $this->client->user->add($user);
		
		$categoryEntry = new KalturaCategoryEntry();
		$categoryEntry->entryId = $entry->id;
		$categoryEntry->categoryId = $category->id;
		
		$categoryUser = new KalturaCategoryUser();
		$categoryUser->categoryId = $category->id;
		$categoryUser->userId = $user->id;
		
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
				
			return;
		}
		
		try {
			$userSessionEntry = $this->client->baseEntry->get($entry->id);
			
			if ($category->privacy == KalturaPrivacyType::AUTHENTICATED_USERS ||
				$category->privacy == KalturaPrivacyType::ALL)
				$this->assertTrue(true);
			else
				$this->assertTrue(false, 'Entry belong to members only category, and user is not a memebr, but was able to get the entry');
			
		}
		catch (Exception $ex)
		{
			$this->assertEquals($category->privacy, KalturaPrivacyType::MEMBERS_ONLY);
		}
		
		
			
		$this->client->categoryEntry->add($categoryEntry);
		
		
		
		$this->startSessionWithDiffe(SessionType::USER, $user->id);
				
	}
	
}

