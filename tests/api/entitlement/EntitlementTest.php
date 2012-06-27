<?php

require_once(dirname(__FILE__) . '/../../bootstrap.php');
require_once(dirname(__FILE__) . '/EntitlementTestBase.php');

/**
 * report service test case.
 */
class EntitlementTest extends EntitlementTestBase
{
	const PRIVILEGE_ENABLE_ENTITLEMENT = 'enableentitlement';
	const PRIVILEGE_DISABLE_ENTITLEMENT = 'disableentitlement';
	const PRIVILEGE_PRIVACY_CONTEXT = "privacycontext";
	
	/**
	 * Set up the test initial data
	 */
	protected function setUp()
	{
		parent::setUp();
	}
	/**
	 * Tests testInheritanceOfUsers - all cases when user can ask to join to a category
	 * @param KalturaCategory $category1
	 * @param KalturaCategory $category2
	 * @param KalturaUser $user1
	 * @dataProvider provideData
	 */
	public function testInheritanceOfUsers(KalturaCategory $category1, KalturaCategory $category2, KalturaUser $user1)
	{
		$this->startSession($this->client, null, null, self::PRIVILEGE_DISABLE_ENTITLEMENT);
		
		try{
			$this->client->user->add($user1);
		}
		catch (Exception $ex)
		{
			if($ex->getCode() != 'DUPLICATE_USER_BY_ID')
			{
				$this->assertTrue(false, 'Fialed to add user: ' . $ex->getCode());
				return;
			}
		}
		
		KalturaLog::info('Add categories');		
		$category1->name = $category1->name . rand();
		$category1->privacyContext = 'mediaspace';
		$category1->inheritanceType = kalturaInheritanceType::MANUAL;
		
		try
		{
			$category1 = $this->client->category->add($category1);
		}
		catch (Exception $ex)
		{
			$this->assertTrue(false, 'Fialed to add category: ' . $ex->getCode());
		}
		
		$categoryUser = new KalturaCategoryUser();
		$categoryUser->userId = $user1->id;
		$categoryUser->categoryId = $category1->id;
		
		try
		{
			$categoryUser = $this->client->categoryUser->add($categoryUser);
		}
		catch (Exception $ex)
		{
			$this->assertTrue(false, 'Fialed to add category: ' . $ex->getCode());
			return;
		}
		
		$category2->name = $category2->name . rand();
		$category2->inheritanceType = kalturaInheritanceType::INHERIT;
		$category2->parentId = $category1->id;
		
		try
		{
			$category2 = $this->client->category->add($category2);
		}
		catch (Exception $ex)
		{
			$this->assertTrue(false, 'Fialed to add category: ' . $ex->getCode());
		}
		
		$this->startSessionWithDiffe(SessionType::USER, $user1->id, self::PRIVILEGE_PRIVACY_CONTEXT . ':' . $category1->privacyContext);
		
		try
		{
			$category1 = $this->client->category->get($category1->id);
		}
		catch (Exception $ex)
		{
			$this->assertTrue(false, 'Fialed to get category while user is a member of parent category and this category inherits members: ' . $ex->getCode());
		}
		
		if($category1->membersCount != 1)
			$this->assertTrue(false, 'Failed: members is not 1. category id: ' . print_r($category1->id, true) . ' $category1->membersCount: ' . print_r($category1->membersCount,true));
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
		$this->startSession($this->client, null, null, self::PRIVILEGE_DISABLE_ENTITLEMENT);
		
		/* @var $category KalturaCategory */
		$category->name = $category->name . rand();
		$category = $this->client->category->add($category);
		
		/* @var $user KalturaUser */
		$user->id = $user->id . rand();
		$user = $this->client->user->add($user);
			
		$this->startSessionWithDiffe(SessionType::USER, $user->id, self::PRIVILEGE_PRIVACY_CONTEXT . ':' . $category->privacyContext);
		
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
		
		KalturaLog::info('List Categories');		
		$categoriesListResponse = $this->client->category->listAction($filterCategory);

		if(!count($categoriesListResponse->objects) && $category->appearInList == KalturaAppearInListType::PARTNER_ONLY)
		{
			KalturaLog::err('Category should returned in list since it appearInListType is set to PARTNER_ONLY');
			$this->assertTrue(false, 'Category should returned in list since it appearInListType is set to PARTNER_ONLY');
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
						$this->assertTrue(false, 'user permission Level [' . $categoryUserResponse->permissionLevel . '] was not set as category permission level [' . $category->defaultPermissionLevel . ']');
						
					break;
				case KalturaUserJoinPolicyType::NOT_ALLOWED:
					if (!$categoryUserResponse)
						$this->assertTrue(true, 'user cannot be added to this category since it is not allowed');
					else
					{
						KalturaLog::debug('user was added to this category although it is not allowed: ' . print_r($categoryUserResponse, true));
						$this->assertTrue(false, 'user was added to this category although it is not allowed: ' . print_r($categoryUserResponse, true));
					}
					
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
		$this->startSession($this->client, null, null, self::PRIVILEGE_DISABLE_ENTITLEMENT);
		
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
		$category1->name = $category1->name . rand();
		try
		{
			$category1 = $this->client->category->add($category1);
		}
		catch (Exception $ex)
		{
			$this->assertTrue(false, 'Fialed to add category: ' . $ex->getCode());
		}
		
		$category2->parentId = $category1->id;
		$category2->name = $category2->name . rand();
		try
		{
			$category2 = $this->client->category->add($category2);
		}
		catch (Exception $ex)
		{
			$this->assertTrue(false, 'Fialed to add category: ' . $ex->getCode());
		}
		
		$category3->parentId = $category2->id;
		$category3->name = $category3->name . rand();
		try
		{
			$category3 = $this->client->category->add($category3);
		}
		catch (Exception $ex)
		{
			$this->assertTrue(false, 'Fialed to add category: ' . $ex->getCode());
		}
		
		/* @var $category1 KalturaCategory */
		/* @var $category2 KalturaCategory */
		/* @var $category3 KalturaCategory */
		
		KalturaLog::info('Add user');
		/* @var $user KalturaUser */
		$user1->id = $user1->id . rand();
		$user1 = $this->client->user->add($user1);
			
		$this->startSessionWithDiffe(SessionType::USER, $user1->id, 'privacycontext:' . $category1->privacyContext);
		
		$categoryUser = new KalturaCategoryUser();
		$categoryUser->categoryId = $category1->id;
		$categoryUser->userId = $user1->id;
		
		$categoryUserResponse = null;
		try {
			$categoryUserResponse = $this->client->categoryUser->add($categoryUser);
		}
		catch(Exception $ex)
		{
			if ($category1->appearInList == KalturaAppearInListType::CATEGORY_MEMBERS_ONLY)				
				$this->assertTrue(true, 'Category1 is members only and therefor user is not able to get it and to be added to');
			elseif($category1->userJoinPolicy == KalturaUserJoinPolicyType::NOT_ALLOWED)
				$this->assertTrue(true, 'User is not allowed to join this category');
			elseif($ex->getCode() == 'CATEGORY_NOT_FOUND' && $category1->appearInList == KalturaAppearInListType::CATEGORY_MEMBERS_ONLY)
				$this->assertTrue(true, 'User is not allowed to join this category since it unlisted: ' . $ex->getMessage());
			else
				$this->assertTrue(false, 'Fialed to add user to category: ' . $ex->getCode());
				
			return;
		}

		try 
		{
			$category1 = $this->client->category->get($category1->id);
		}
		catch(Exception $ex)
		{
			if ($ex->getCode() == 'CATEGORY_NOT_FOUND' && $category1->appearInList == KalturaAppearInListType::CATEGORY_MEMBERS_ONLY)
				$this->assertTrue(true, 'Category is members only and cannot get this entry: ' . $ex->getMessage());
			else
				$this->assertTrue(false, 'Could not get category: ' . $ex->getCode());			
		}
		
		try 
		{
			$category2 = $this->client->category->get($category2->id);
		}
		catch(Exception $ex)
		{
			if ($ex->getCode() == 'CATEGORY_NOT_FOUND' && $category2->appearInList == KalturaAppearInListType::CATEGORY_MEMBERS_ONLY)
				$this->assertTrue(true, 'Category is members only and cannot get this entry: ' . $ex->getMessage());
			else
				$this->assertTrue(false, 'Could not get category: ' . $ex->getCode());			
		}
		
		try
		{
			$category3 = $this->client->category->get($category3->id);
		}
		catch(Exception $ex)
		{
			if ($ex->getCode() == 'CATEGORY_NOT_FOUND' && $category3->appearInList == KalturaAppearInListType::CATEGORY_MEMBERS_ONLY)
				$this->assertTrue(true, 'Category is members only and cannot get this entry: ' . $ex->getMessage());
			else
				$this->assertTrue(false, 'Could not get category: ' . $ex->getCode());			
		}
		
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
		$this->startSession($this->client, null, null, self::PRIVILEGE_DISABLE_ENTITLEMENT);
		
		/* @var $category KalturaCategory */
		$category->name = $category->name . rand();
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
			
			if ($ex->getCode() == 'CATEGORY_NOT_FOUND' && $category->appearInList == KalturaAppearInListType::CATEGORY_MEMBERS_ONLY)
			{			
				$this->assertTrue(true, 'Category is members only and cannot get this entry: ' . $ex->getMessage());
			}
			elseif($category->contributionPolicy != KalturaContributionPolicyType::ALL)
			{
				$this->assertTrue(true, 'not allowed to add entry to category');
			}
			else
			{
				$this->assertTrue(false, 'Fialed to add entry to category: ' . $ex->getCode());
				return;
			}
		}
		
		/* @var $user KalturaUser */
		$user->id = $user->id . rand();
		$user = $this->client->user->add($user, null, null );
			
		$this->startSessionWithDiffe(SessionType::USER, $user->id, self::PRIVILEGE_PRIVACY_CONTEXT . ':' . $category->privacyContext);
		
		//user get the entry with no permission
		try {
			$entry = $this->client->baseEntry->get($entry->id);
		}
		catch (Exception $ex)
		{
			if($ex->getCode() != 'ENTRY_ID_NOT_FOUND')
			{
				if($category->privacy != KalturaPrivacyType::MEMBERS_ONLY)
					$this->assertTrue(false, 'Category privacy if not members only and user should be able to get the entry: ' . $ex->getCode());	
			}
		}
		
		if($category->privacy == KalturaPrivacyType::MEMBERS_ONLY)
			$this->assertTrue(false, 'Category privacy if members only and user should not be able to get the entry: ' . $ex->getMessage());	
		
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
				$this->assertTrue(true, 'Category is members only and therefor user is not able to get it and to be added to: ' . $ex->getMessage());
			}
			elseif($category->userJoinPolicy == KalturaUserJoinPolicyType::NOT_ALLOWED)
			{
				$this->assertTrue(true, 'User is not allowed to join this category: ' . $ex->getMessage());
			}
			else
			{
				$this->assertTrue(false, 'Fialed to add user to category: ' . $ex->getCode());
				return;
			}
		}
		
		$this->startSessionWithDiffe(SessionType::USER, $user->id);
		
		if($categoryUserResponse && $categoryUserResponse->status == KalturaCategoryUserStatus::ACTIVE)
		{
				//user get the entry with no permission
			try {
				$entry = $this->client->baseEntry->get($entry->id);
			}
			catch (Exception $ex)
			{
				if($ex->getCode() != 'ENTRY_ID_NOT_FOUND')
					$this->assertTrue(false, 'Category privacy if not members only and user should be able to get the entry: ' . $ex->getCode());
				else
					$this->assertTrue(false, $ex->getCode());
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
		$this->startSession($this->client, null, null, self::PRIVILEGE_DISABLE_ENTITLEMENT);
		
		/* @var $category KalturaCategory */
		$category->name = $category->name . rand();
		$category = $this->client->category->add($category);
					
		$filterCategory = new KalturaCategoryFilter();
		$filterCategory->idEqual = $category->id;		
		$categoriesListResponse = $this->client->category->listAction($filterCategory);
		
		/* @var $user KalturaUser */
		$user->id = $user->id . rand();
		$user = $this->client->user->add($user);
			
		$this->startSessionWithDiffe(SessionType::USER, $user->id, self::PRIVILEGE_PRIVACY_CONTEXT . ':' . $category->privacyContext);
		
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
				$this->assertTrue(true, 'Category is members only and cannot get this entry: ' . $ex->getMessage());
			}
			elseif($category->contributionPolicy != KalturaContributionPolicyType::ALL)
			{
				$this->assertTrue(true, 'not allowed to add entry to category: ' . $ex->getMessage());
			}
			else
			{
				$this->assertTrue(false, 'Fialed to add entry to category: ' . $ex->getMessage());
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
			}
			return;
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
			}
			return;
		}
		
	}
	
	/**
	 * Tests testBackwardCopmatEntryCategoryAdd
	 * @param KalturaCategory $category
	 * @param KalturaBaseEntry $entry
	 * @dataProvider provideData
	 */
	public function testEntryCategoryAdd($category, $entry)
	{
		$this->startSession($this->client, null, null, self::PRIVILEGE_DISABLE_ENTITLEMENT);
		/* @var $category KalturaCategory */
		$category->name = $category->name . rand();
		$category = $this->client->category->add($category);
		
		$this->startSession($this->client);
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
			
			if ($ex->getCode() == 'CATEGORY_NOT_FOUND' && ($category->privacyContext != '' || $category->privacyContext != null))
			{			
				$this->assertTrue(true, 'Category is set with a diffrenet privacy contexts: ' . $ex->getMessage());
			}
			elseif($category->contributionPolicy != KalturaContributionPolicyType::ALL)
			{
				$this->assertTrue(true, 'not allowed to add entry to category: ' . $ex->getMessage());
			}
			else
			{
				$this->assertTrue(false, 'Fialed to add entry to category: ' . $ex->getMessage());
			}
			return;
		}
		
		try{
			$entry = $this->client->baseEntry->get($entry->id);
		}
		catch (Exception $ex)
		{
			$this->assertTrue(false, 'Fialed to get entry to category: ' . $ex->getMessage());
			return;
		}
		
		if($entry->categories != $category->fullName && $category->privacyContext == '')
			$this->assertTrue(false, 'CategoryEntry new service didnt update entry->categories: ' . $entry->categories . ' category->fullName: ' . $category->fullName);
			
		if($entry->categories == $category->fullName && $category->privacyContext != '')
			$this->assertTrue(false, 'CategoryEntry new service update entry->categories for category with privacy context: ' . $entry->categories . ' category->fullName: ' . $category->fullName);
	}
	
	/**
	 * Tests testBackwardCopmatEntryCategoriesAdd
	 * @param string $categoryName
	 * @param KalturaBaseEntry $entry
	 * @dataProvider provideData
	 */
	public function testBackwardCopmatEntryCategoriesAdd($categoryName, $entry)
	{
		$this->startSession($this->client, null, null, self::PRIVILEGE_DISABLE_ENTITLEMENT);
					
		$categoryName = $categoryName . rand();
		$entry->categories = $categoryName;
		$entry = $this->client->baseEntry->add($entry);

		if($entry->categories != $categoryName)
			$this->assertTrue(false, 'Category was not set on entry');
			
		$categoryId = $entry->categoriesIds;
		
		try {
			$category = $this->client->category->get($categoryId);
		}
		catch(Exception $ex)
		{
			$this->assertTrue(false, 'Category was not found' . $ex->getMessage());
		}
		
		if($category->fullName != $categoryName)
			$this->assertTrue(false, 'Category full name is not as created');
		
		$filter = new KalturaCategoryEntryFilter();
		$filter->categoryIdEqual = $category->id;
		$filter->entryIdEqual = $entry->id;
		 
		try{
			$categoryEntryResponse = $this->client->categoryEntry->listAction($filter);
		}
		catch (Exception $ex)
		{
			$this->assertTrue(false, 'Fialed to get category entry' . $ex->getMessage());
			return;
		}
		
		if($categoryEntryResponse->objects && count($categoryEntryResponse->objects) != 1)
			$this->assertTrue(false, 'CategoryEntry was not created');
	}
	
	/**
	 * Tests testPrivacyContext
	 * @param KalturaCategory $category1
	 * @param KalturaCategory $category2
	 * @param KalturaBaseEntry $entry
	 * @dataProvider provideData
	 */
	public function testPrivacyContext($category1, $category2, $entry)
	{
		$this->startSession($this->client);
		
		/* @var $category KalturaCategory */
		$category1->name = $category1->name . rand();
		$category2->name = $category2->name . rand();
		try {
			$category1Response = $this->client->category->add($category1);
		}
		catch(Exception $ex)
		{
			KalturaLog::err('Error: line:' . __LINE__ .' ' . $ex->getMessage());
			
			if ($ex->getCode() != 'CANNOT_UPDATE_CATEGORY_PRIVACY_CONTEXT' && $category1->privacyContext != '')
			 	$this->assertTrue(false, 'should not be able to create category with privacyContext: ' . $ex->getCode());	

			if($ex->getCode() == 'CANNOT_UPDATE_CATEGORY_PRIVACY_CONTEXT' && $category1->privacyContext != '')
				$this->assertTrue(true, 'should not be able to create category with privacyContext: ' . $ex->getCode());
				
			$category1Response = null;
		}
		
		$this->startSessionWithDiffe(SessionType::ADMIN, null, 'disableentitlement');
		
		
		if(!$category1Response)
		{
			try {
				$category1Response = $this->client->category->add($category1);
			}
			catch(Exception $ex)
			{
				KalturaLog::err('Error: line:' . __LINE__ .' ' . $ex->getMessage());
				$this->assertTrue(true, 'Session with no entitlement cannot add category: ' . $ex->getMessage());
			}
		}
		
		try {
			$category2Response = $this->client->category->add($category2);
		}
		catch(Exception $ex)
		{
			KalturaLog::err('Error: line:' . __LINE__ .' ' . $ex->getMessage());
			$this->assertTrue(true, 'Session with no entitlement cannot add category: ' . $ex->getMessage());
			return;
		}
		
		$this->startSessionWithDiffe(SessionType::USER, 'anyuser', 'privacycontext:' . $category1->privacyContext);
		
		$category2ResponseWithDiffContext = null;
		try {
			$category2ResponseWithDiffContext = $this->client->category->get($category2Response->id);
		}
		catch(Exception $ex)
		{
			KalturaLog::err('Error: line:' . __LINE__ .' ' . $ex->getMessage());
			$this->assertTrue(true, 'Should not be able to get category with different context from the session ks: ' . $ex->getMessage());
		}
		
		if($category2ResponseWithDiffContext)
			$this->assertTrue(true, 'Should not be able to get category with different context from the session ks');
			
		$category1ResponseWithDiffContext = null;
		try {
			$category1ResponseWithDiffContext = $this->client->category->get($category1Response->id);
		}
		catch(Exception $ex)
		{
			KalturaLog::err('Error: line:' . __LINE__ .' ' . $ex->getMessage());
			$this->assertTrue(false, 'Should be able to get category with different context from the session ks: ' . $ex->getMessage());
		}
	}
}

