<?php

require_once 'PHPUnit\Framework\TestCase.php';
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'bootstrap.php');
require_once(KALTURA_CLIENT_PATH);

/**
 * test case.
 */
class UserRoleServiceTest extends PHPUnit_Framework_TestCase {
	
	const TEST_PARTNER_ID = null;
	const TEST_ADMIN_SECRET = null;
	const TEST_USER_SECRET = null;
	
	private $addedRoleIds = array();
	
	/**
	 * @var KalturaClient
	 */
	private $client = null;
	
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		if ( !self::TEST_PARTNER_ID   || !self::TEST_ADMIN_SECRET || !self::TEST_USER_SECRET    )
		{
	     	die('Test partners were not defined - quitting test!');
		}
		
		parent::setUp ();
		$this->client = $this->getClient(self::TEST_PARTNER_ID);
		$this->dummyPartner = PartnerPeer::retrieveByPK(self::TEST_PARTNER_ID);
		$this->assertEquals(self::TEST_PARTNER_ID, $this->dummyPartner->getId());
		$this->addedRoleIds = array();
		UserRolePeer::clearInstancePool();
		PermissionPeer::clearInstancePool();
		PermissionItemPeer::clearInstancePool();
		kuserPeer::clearInstancePool();
		PartnerPeer::clearInstancePool();
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		UserRolePeer::clearInstancePool();
		PermissionPeer::clearInstancePool();
		PermissionItemPeer::clearInstancePool();
		kuserPeer::clearInstancePool();
		PartnerPeer::clearInstancePool();
		
		$this->client = null;
		UserRolePeer::setUseCriteriaFilter(false);
		foreach ($this->addedRoleIds as $id) {
			try
			{
				
				$obj = UserRolePeer::retrieveByPK($id);
				if ($obj) {
					$obj->delete();
				}
			}
			catch (PropelException $e) {}
		}
		UserRolePeer::setUseCriteriaFilter(true);
		$this->addedRoleIds = array();
		parent::tearDown ();
	}
	
	/**
	 * @param int $partnerId
	 * @return KalturaClient
	 */
	private function getClient($partnerId)
	{
		if ($partnerId) {
			$config = new KalturaConfiguration($partnerId);
		}
		else {
			$config = new KalturaConfiguration();
		}
		
		$config->serviceUrl = kConf::get('apphome_url');
		$client = new KalturaClient($config);
		return $client;
	}
	
	
	/**
	 * Starts a new session
	 * @param KalturaSessionType $type
	 * @param string $userId
	 */
	private function startSession($type, $userId)
	{
		$secret = ($type == KalturaSessionType::ADMIN) ? self::TEST_ADMIN_SECRET : self::TEST_USER_SECRET;
		$ks = $this->client->session->start($secret, $userId, $type, self::TEST_PARTNER_ID);
		$this->assertNotNull($ks);
		if (!$ks) {
			return false;
		}
		$this->client->setKs($ks);
		return true;		
	}
	
	
	private function checkException($exceptionThrown, $code = null, $message = null)
	{
		if (!$exceptionThrown) {
			$this->fail('No exception was thrown');
		}
		if ($code && $exceptionThrown->getCode() != $code) {
			$this->fail('Exception thrown with code ['.$exceptionThrown->getCode().'] instead of ['.$code.']');
		}
		if ($message && $exceptionThrown->getMessage() != $message) {
			$this->fail('Exception thrown with message ['.$exceptionThrown->getMessage().'] instead of ['.$message.']');
		}
	}
	
	
	/**
	 * @return Partner
	 */
	private function getDbPartner()
	{
		return PartnerPeer::retrieveByPK(self::TEST_PARTNER_ID);
	}
	
	private function addRoleWrap(KalturaUserRole $role)
	{
		$addedRole = $this->client->userRole->add($role);
		$this->addedRoleIds[] = $addedRole->id;
		return $addedRole;
	}
	
	
	public function testFailuresUserSession()
	{
		// failure to make all requests with a user KS instead of an admin KS
		
		$this->startSession(KalturaSessionType::USER, null); // start a user session
		
		// add action
		$exceptionThrown = false;
		try { $this->addRoleWrap(new KalturaUserRole()); }
		catch (Exception $e) { $exceptionThrown = $e; }
		$this->checkException($exceptionThrown, 'INVALID_KS');
		
		// clone action
		$exceptionThrown = false;
		try { $this->client->userRole->cloneAction(rand(0, 10)); }
		catch (Exception $e) { $exceptionThrown = $e; }
		$this->checkException($exceptionThrown, 'INVALID_KS');
		
		// delete action
		$exceptionThrown = false;
		try { $this->client->userRole->delete(rand(0, 10)); }
		catch (Exception $e) { $exceptionThrown = $e; }
		$this->checkException($exceptionThrown, 'INVALID_KS');
		
		// get action
		$exceptionThrown = false;
		try { $this->client->userRole->get(rand(0, 10)); }
		catch (Exception $e) { $exceptionThrown = $e; }
		$this->checkException($exceptionThrown, 'INVALID_KS');
		
		// list action
		$exceptionThrown = false;
		try { $this->client->userRole->listAction(); }
		catch (Exception $e) { $exceptionThrown = $e; }
		$this->checkException($exceptionThrown, 'INVALID_KS');
		
		// update action
		$exceptionThrown = false;
		try { $this->client->userRole->update(rand(0, 10), new KalturaUserRole()); }
		catch (Exception $e) { $exceptionThrown = $e; }
		$this->checkException($exceptionThrown, 'INVALID_KS');
	}
	
	
	public function testAddAction()
	{
		$this->startSession(KalturaSessionType::ADMIN, $this->getDbPartner()->getAdminUserId());
				
		// add role
		$newRole = new KalturaUserRole();
		$newRole->name = 'New test role'.uniqid();
		$newRole->description = 'Test description'.uniqid();
		$newRole->status = KalturaUserRoleStatus::ACTIVE;
		$newRole->permissionNames = KalturaPermissionName::ACCESS_CONTROL_BASE.','.KalturaPermissionName::INTEGRATION_BASE;
		$addedRole = $this->addRoleWrap($newRole);
		
		// verify the returned role object's parameters
		$this->assertType('KalturaUserRole', $addedRole);
		$this->assertEquals($newRole->name, $addedRole->name);
		$this->assertEquals($newRole->description, $addedRole->description);
		$this->assertEquals($newRole->status, $addedRole->status);
		$this->assertEquals($newRole->permissionNames, $addedRole->permissionNames);
		$this->assertEquals(self::TEST_PARTNER_ID, $addedRole->partnerId);
		$this->assertNotNull($addedRole->id);
		$this->assertNotNull($addedRole->createdAt);
		$this->assertNotNull($addedRole->updatedAt);
		
		// try to get the role and verify returned parameters
		$getRole = $this->client->userRole->get($addedRole->id);
		$this->assertType('KalturaUserRole', $getRole);
		$this->assertEquals($newRole->name, $getRole->name);
		$this->assertEquals($newRole->description, $getRole->description);
		$this->assertEquals($newRole->status, $getRole->status);
		$this->assertEquals($newRole->permissionNames, $getRole->permissionNames);
		$this->assertEquals(self::TEST_PARTNER_ID, $getRole->partnerId);
		$this->assertEquals($addedRole->id, $getRole->id);
		$this->assertEquals($addedRole->createdAt, $getRole->createdAt);
		$this->assertEquals($addedRole->updatedAt, $getRole->updatedAt);
		
		// get the role in a list
		$roleList = $this->client->userRole->listAction();
		$roleList = $roleList->objects;
		$found = false;
		foreach ($roleList as $role) {
			if ($role->id === $getRole->id && $role->name === $getRole->name) {
				$found = true;
				break;
			}
		}
		if (!$found) {
			$this->fail('New added role with id ['.$getRole->id.'] was not returned from userRole->list action');
		}
		
		// add a role with no status and verify that it is set to ACTIVE
		$newRole = new KalturaUserRole();
		$newRole->name = 'Test role with no status'.uniqid();
		$addedRole = $this->addRoleWrap($newRole);
		$this->assertEquals(KalturaUserRoleStatus::ACTIVE, $addedRole->status);
	}
	
	
	public function testAddActionFailures()
	{
		$this->startSession(KalturaSessionType::ADMIN, $this->getDbPartner()->getAdminUserId());
		
		$failedRoleNames = array();
		
		// failure to add a role with no name
		$newRole = new KalturaUserRole();
		$exceptionThrown = false;
		try { $addedRole = $this->addRoleWrap($newRole); }
		catch (Exception $e) { $exceptionThrown = $e; }
		$this->checkException($exceptionThrown, 'PROPERTY_VALIDATION_CANNOT_BE_NULL');
		$failedRoleNames[] = $newRole->name;
		
		// failure to add a role with an invalid permission
		$newRole = new KalturaUserRole();
		$newRole->name = 'Stam 1'.uniqid();
		$newRole->permissionNames = uniqid();
		$exceptionThrown = false;
		try { $addedRole = $this->addRoleWrap($newRole); }
		catch (Exception $e) { $exceptionThrown = $e; }
		$this->checkException($exceptionThrown, 'PERMISSION_NOT_FOUND');
		$failedRoleNames[] = $newRole->name;
		
		// failure to add a role with a permission which the partner does not have
		$newRole = new KalturaUserRole();
		$newRole->name = 'Stam 2'.uniqid();
		$newRole->permissionNames = PermissionName::BATCH_BASE;
		$exceptionThrown = false;
		try { $addedRole = $this->addRoleWrap($newRole); }
		catch (Exception $e) { $exceptionThrown = $e; }
		$this->checkException($exceptionThrown, 'PERMISSION_NOT_FOUND');
		$failedRoleNames[] = $newRole->name;
		
		// failure to choose role's ID
		$newRole = new KalturaUserRole();
		$newRole->name = 'Stam 3'.uniqid();
		$newRole->id = rand(1,100);
		$exceptionThrown = false;
		try { $addedRole = $this->addRoleWrap($newRole); }
		catch (Exception $e) { $exceptionThrown = $e; }
		$this->checkException($exceptionThrown, 'PROPERTY_VALIDATION_NOT_UPDATABLE');
		$failedRoleNames[] = $newRole->name;
		
		// failure to choose role's partner ID
		$newRole = new KalturaUserRole();
		$newRole->name = 'Stam 4'.uniqid();
		$newRole->partnerId = rand(100, 300);
		$exceptionThrown = false;
		try { $addedRole = $this->addRoleWrap($newRole); }
		catch (Exception $e) { $exceptionThrown = $e; }
		$this->checkException($exceptionThrown, 'PROPERTY_VALIDATION_NOT_UPDATABLE');
		$failedRoleNames[] = $newRole->name;
		
		// verify that none of the failed roles is returned in the roles list
		$roleList = $this->client->userRole->listAction();
		$roleList = $roleList->objects;
		foreach ($roleList as $role) {
			if (in_array($role->name, $failedRoleNames)) {
				$this->fail('Failed role with name ['.$role->name.'] was mistakenly added and returned in list with ID ['.$role->id.']');
			}
		}
	}
	
	
	public function testCloneAction()
	{
		$this->startSession(KalturaSessionType::ADMIN, $this->getDbPartner()->getAdminUserId());
		
		// failure to clone an invalid role
		$roleId = rand(-100, -1);
		$exceptionThrown = false;
		try { $this->client->userRole->cloneAction($roleId); }
		catch (Exception $e) { $exceptionThrown = $e; }
		$this->checkException($exceptionThrown, 'INVALID_OBJECT_ID');
		
		// failure to clone another partner's role
		$c = new Criteria();
		$c->addAnd(UserRolePeer::PARTNER_ID, array(Partner::ADMIN_CONSOLE_PARTNER_ID, Partner::BATCH_PARTNER_ID), Criteria::IN);
		$systemPartnersRoles = UserRolePeer::doSelect($c);
		foreach ($systemPartnersRoles as $systemPartnerRole)
		{
			$exceptionThrown = false;
			try { $this->client->userRole->cloneAction($systemPartnerRole->getId()); }
			catch (Exception $e) { $exceptionThrown = $e; }
			$this->checkException($exceptionThrown, 'INVALID_OBJECT_ID');
		}
		
		// failure to clone a deleted role
		$newRole = new KalturaUserRole();
		$newRole->name = 'Deleted role'.uniqid();
		$addedRole = $this->addRoleWrap($newRole);
		$this->client->userRole->delete($addedRole->id);
		$exceptionThrown = false;
		try { $this->client->userRole->cloneAction($addedRole->id); }
		catch (Exception $e) { $exceptionThrown = $e; }
		$this->checkException($exceptionThrown, 'INVALID_OBJECT_ID');
		
		// clone a valid role and validate all fields except for ID
		$newRole = new KalturaUserRole();
		$newRole->name = 'New test role to clone'.uniqid();
		$newRole->description = 'Test description'.uniqid();
		$newRole->permissionNames = KalturaPermissionName::ADMIN_ROLE_DELETE.','.KalturaPermissionName::CONTENT_MANAGE_RECONVERT;
		$addedRole = $this->addRoleWrap($newRole); // add a new role
		$clonedRole = $this->client->userRole->cloneAction($addedRole->id); // clone role
		$this->addedRoleIds[] = $clonedRole->id;
		$this->assertType('KalturaUserRole', $clonedRole);
		$this->assertGreaterThan($addedRole->id, $clonedRole->id);
		$this->assertEquals($newRole->name, $addedRole->name);
		$this->assertEquals($newRole->description, $addedRole->description);
		$this->assertEquals(KalturaUserRoleStatus::ACTIVE, $clonedRole->status);
		$this->assertEquals($newRole->permissionNames, $clonedRole->permissionNames);
		$this->assertEquals(self::TEST_PARTNER_ID, $clonedRole->partnerId);
		$this->assertNotNull($clonedRole->createdAt);
		$this->assertNotNull($clonedRole->updatedAt);
	}
	
	
	public function testDeleteAction()
	{
		$this->startSession(KalturaSessionType::ADMIN, $this->getDbPartner()->getAdminUserId());
		
		// failure to delete partner 0 role
		$c = new Criteria();
		$c->addAnd(UserRolePeer::PARTNER_ID, PartnerPeer::GLOBAL_PARTNER, Criteria::EQUAL);
		$partner0Roles = UserRolePeer::doSelect($c);
		foreach ($partner0Roles as $role)
		{
			$exceptionThrown = false;
			try { $this->client->userRole->delete($role->getId()); }
			catch (Exception $e) { $exceptionThrown = $e; }
			$this->checkException($exceptionThrown, 'INVALID_OBJECT_ID');
		}
		
		// failure to delete another partner's role
		$c = new Criteria();
		$c->addAnd(UserRolePeer::PARTNER_ID, self::TEST_PARTNER_ID, Criteria::NOT_EQUAL);
		$otherPartnerRoles = UserRolePeer::doSelect($c);
		for ($i=1; $i<4; $i++)
		{
			$randId = rand(0, count($otherPartnerRoles)-1);
			$exceptionThrown = false;
			try { $this->client->userRole->delete($otherPartnerRoles[$randId]->getId()); }
			catch (Exception $e) { $exceptionThrown = $e; }
			$this->checkException($exceptionThrown, 'INVALID_OBJECT_ID');
		}
		
		// success deleting current partner's role
		$newRole = new KalturaUserRole();
		$newRole->name = 'Deleted role'.uniqid();
		$addedRole = $this->addRoleWrap($newRole);
		$getRole = $this->client->userRole->get($addedRole->id);
		$this->assertEquals($newRole->name, $getRole->name);
		$deletedRole = $this->client->userRole->delete($addedRole->id);
		$this->assertEquals($newRole->name, $deletedRole->name);
		$this->assertEquals(KalturaUserRoleStatus::DELETED, $deletedRole->status);

		
		// verify that deleted role is not returned in get or list
		$exceptionThrown = false;
		try { $this->client->userRole->get($addedRole->id); }
		catch (Exception $e) { $exceptionThrown = $e; }
		$this->checkException($exceptionThrown, 'INVALID_OBJECT_ID');
		
		$roleList = $this->client->userRole->listAction();
		$roleList = $roleList->objects;
		foreach ($roleList as $role) {
			if ($role->name == $newRole->name) {
				$this->fail('Deleted role with name ['.$role->name.'] was mistakenly returned in list with ID ['.$role->id.']');
			}
		}
	}
	
	public function testGetAction()
	{
		$this->startSession(KalturaSessionType::ADMIN, $this->getDbPartner()->getAdminUserId());
		
		// get a partner 0 role and compare to DB record
		$c = new Criteria();
		$c->addAnd(UserRolePeer::PARTNER_ID, PartnerPeer::GLOBAL_PARTNER, Criteria::EQUAL);
		$partner0Roles = UserRolePeer::doSelect($c);
		for ($i=1; $i<4; $i++)
		{
			$randId = rand(0, count($partner0Roles)-1);
			$getRole = $this->client->userRole->get($partner0Roles[$randId]->getId());
			$this->assertType('KalturaUserRole', $getRole);
			$this->assertEquals(PartnerPeer::GLOBAL_PARTNER, $getRole->partnerId);
			$this->assertEquals($partner0Roles[$randId]->getId(), $getRole->id);
			$this->assertEquals($partner0Roles[$randId]->getName(), $getRole->name);
			$this->assertEquals($partner0Roles[$randId]->getDescription(), $getRole->description);
			$this->assertEquals($partner0Roles[$randId]->getPartnerId(), $getRole->partnerId);
			$this->assertEquals($partner0Roles[$randId]->getPermissionNames(), $getRole->permissionNames);
			$this->assertNotNull($getRole->createdAt);
			$this->assertNotNull($getRole->updatedAt);					
		}
				
		// get current partner's role and compare to DB record
		$c = new Criteria();
		$c->addAnd(UserRolePeer::PARTNER_ID, self::TEST_PARTNER_ID, Criteria::EQUAL);
		$partnerRoles = UserRolePeer::doSelect($c);
		for ($i=1; $i<4; $i++)
		{
			$randId = rand(0, count($partner0Roles)-1);
			$getRole = $this->client->userRole->get($partnerRoles[$randId]->getId());
			$this->assertType('KalturaUserRole', $getRole);
			$this->assertEquals(self::TEST_PARTNER_ID, $getRole->partnerId);
			$this->assertEquals($partnerRoles[$randId]->getId(), $getRole->id);
			$this->assertEquals($partnerRoles[$randId]->getName(), $getRole->name);
			$this->assertEquals($partnerRoles[$randId]->getDescription(), $getRole->description);
			$this->assertEquals($partnerRoles[$randId]->getPartnerId(), $getRole->partnerId);
			$this->assertEquals($partnerRoles[$randId]->getPermissionNames(), $getRole->permissionNames);
			$this->assertNotNull($getRole->createdAt);
		}
		
		// failure to get another partner's role (not partner 0)
		$c = new Criteria();
		$c->addAnd(UserRolePeer::PARTNER_ID, array(self::TEST_PARTNER_ID, PartnerPeer::GLOBAL_PARTNER), Criteria::NOT_IN);
		$otherPartnerRoles = UserRolePeer::doSelect($c);
		for ($i=1; $i<4; $i++)
		{
			$randId = rand(0, count($partner0Roles)-1);
			$exceptionThrown = false;
			try { $this->client->userRole->get($otherPartnerRoles[$randId]->getId()); }
			catch (Exception $e) { $exceptionThrown = $e; }
			$this->checkException($exceptionThrown, 'INVALID_OBJECT_ID');
		}
		
		// add role with permission names = * and verify that all relevant permissions are returned
		$newRole = new KalturaUserRole();
		$newRole->name = 'Test role with ';
		$newRole->permissionNames = UserRole::ALL_PARTNER_PERMISSIONS_WILDCARD;
		$addedRole = $this->addRoleWrap($newRole);
		$getRole = $this->client->userRole->get($addedRole->id);
		$this->assertEquals($addedRole->permissionNames, $getRole->permissionNames);
		
		$c = new Criteria();
		$c->addAnd(PermissionPeer::PARTNER_ID, array(self::TEST_PARTNER_ID, PartnerPeer::GLOBAL_PARTNER), Criteria::EQUAL);
		$c->addAnd(PermissionPeer::TYPE, array(PermissionType::API_ACCESS, PermissionType::EXTERNAL), Criteria::IN);
		$allPartnerPermissions = PermissionPeer::doSelect($c);
		$returnedPermissions = explode(',', trim($getRole->permissionNames,','));
		$this->assertEquals(count($allPartnerPermissions), count($returnedPermissions));
		foreach ($allPartnerPermissions as $permission)
		{
			$this->assertTrue(in_array($permission->getName(), $returnedPermissions));
		}
	}
	
	public function testListAction()
	{
		$this->startSession(KalturaSessionType::ADMIN, $this->getDbPartner()->getAdminUserId());
		
		// get list
		$listResult = $this->client->userRole->listAction();
		$roleList = $listResult->objects;
		$returnedRoleNames = array();
		foreach ($roleList as $role)
		{
			$returnedRoleNames[] = $role->name;
		}
			
		// check that total count is right
		$this->assertGreaterThan(0, count($roleList));
		$this->assertEquals(count($roleList), $listResult->totalCount);
		
		// check that only partner 0 and current partner roles are returned
		foreach ($roleList as $role)
		{
			if ($role->partnerId != self::TEST_PARTNER_ID && $role->partnerId != PartnerPeer::GLOBAL_PARTNER) {
				$this->fail('List returned role id ['.$role->id.'] of partner ['.$role->partnerId.'] instead of partner ['.self::TEST_PARTNER_ID.']');
			}
		}
		
		// check that all partner 0 roles are returned
		$c = new Criteria();
		$c->addAnd(UserRolePeer::PARTNER_ID, PartnerPeer::GLOBAL_PARTNER, Criteria::EQUAL);
		$partner0Roles = UserRolePeer::doSelect($c);
		foreach ($partner0Roles as $role)
		{
			$this->assertTrue(in_array($role->getName(), $returnedRoleNames));
		}		
		
		//TODO: test filters ?
	}
	
	public function testUpdateAction()
	{
		$this->startSession(KalturaSessionType::ADMIN, $this->getDbPartner()->getAdminUserId());
		
		// failure to update partner 0 roles
		$c = new Criteria();
		$c->addAnd(UserRolePeer::PARTNER_ID, PartnerPeer::GLOBAL_PARTNER, Criteria::EQUAL);
		$partner0Roles = UserRolePeer::doSelect($c);
		for ($i=1; $i<4; $i++)
		{
			$randId = rand(0, count($partner0Roles)-1);
			$exceptionThrown = false;
			$updateRole = new KalturaUserRole();
			$updateRole->name = uniqid();
			try { $this->client->userRole->update($partner0Roles[$randId]->getId(), $updateRole); }
			catch (Exception $e) { $exceptionThrown = $e; }
			$this->checkException($exceptionThrown, 'INVALID_OBJECT_ID');
		}
		
		// add a new role to test with
		$newRole = new KalturaUserRole();
		$newRole->name = uniqid();
		$addedRole = $this->addRoleWrap($newRole);
		
		// failure to add a permisison which the partner does not have
		$updateRole = new KalturaUserRole();
		$updateRole->permissionNames = PermissionName::BATCH_BASE;
		$exceptionThrown = false;
		try { $addedRole = $this->client->userRole->update($addedRole->id, $updateRole); }
		catch (Exception $e) { $exceptionThrown = $e; }
		$this->checkException($exceptionThrown, 'PERMISSION_NOT_FOUND');
		
		// success adding and removing valid permissions
		$updateRole = new KalturaUserRole();
		$updateRole->permissionNames = PermissionName::ACCOUNT_BASE.','.PermissionName::CONTENT_MANAGE_EMBED_CODE;
		$resultRole = $this->client->userRole->update($addedRole->id, $updateRole);
		$this->assertEquals($updateRole->permissionNames, $resultRole->permissionNames);
		
		// replace permissions test - verify that old permissions are no more returned
		$updateRole = new KalturaUserRole();
		$updateRole->permissionNames = PermissionName::CONTENT_INGEST_BULK_UPLOAD.','.PermissionName::CUSTOM_DATA_PROFILE_DELETE;
		$resultRole = $this->client->userRole->update($addedRole->id, $updateRole);
		$this->assertEquals($updateRole->permissionNames, $resultRole->permissionNames);
		
		// success updating name, description and status
		$updateRole = new KalturaUserRole();
		$updateRole->name = uniqid();
		$updateRole->description = uniqid();
		$updateRole->status = KalturaUserRoleStatus::BLOCKED;
		$resultRole = $this->client->userRole->update($addedRole->id, $updateRole);
		$this->assertEquals($updateRole->name, $resultRole->name);
		$this->assertEquals($updateRole->description, $resultRole->description);
		$this->assertEquals($updateRole->status, $resultRole->status);		

		// failure to update partner id
		$updateRole = new KalturaUserRole();
		$updateRole->partnerId = rand(100, 300);
		$exceptionThrown = false;
		try { $addedRole = $this->client->userRole->update($addedRole->id, $updateRole); }
		catch (Exception $e) { $exceptionThrown = $e; }
		$this->checkException($exceptionThrown, 'PROPERTY_VALIDATION_NOT_UPDATABLE');
		
		// failure to update role id
		$updateRole = new KalturaUserRole();
		$updateRole->id = rand(1, 1000);
		$exceptionThrown = false;
		try { $addedRole = $this->client->userRole->update($addedRole->id, $updateRole); }
		catch (Exception $e) { $exceptionThrown = $e; }
		$this->checkException($exceptionThrown, 'PROPERTY_VALIDATION_NOT_UPDATABLE');
		
		// failure to update createdAt
		$updateRole = new KalturaUserRole();
		$updateRole->createdAt = time();
		$exceptionThrown = false;
		try { $addedRole = $this->client->userRole->update($addedRole->id, $updateRole); }
		catch (Exception $e) { $exceptionThrown = $e; }
		$this->checkException($exceptionThrown, 'PROPERTY_VALIDATION_NOT_UPDATABLE');
		
		// failure to update updatedAt
		$updateRole = new KalturaUserRole();
		$updateRole->updatedAt = time();
		$exceptionThrown = false;
		try { $addedRole = $this->client->userRole->update($addedRole->id, $updateRole); }
		catch (Exception $e) { $exceptionThrown = $e; }
		$this->checkException($exceptionThrown, 'PROPERTY_VALIDATION_NOT_UPDATABLE');
		
		//TODO: verify that only given parameters were updated and not other parameters
	}	

}

