<?php

require_once 'PHPUnit\Framework\TestCase.php';
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'bootstrap.php');
require_once(KALTURA_CLIENT_PATH);

/**
 * test case.
 */
class PermissionServiceTest extends PHPUnit_Framework_TestCase {
	
	const TEST_PARTNER_ID = 408;
	const TEST_ADMIN_SECRET = 'adminsecret408';
	const TEST_USER_SECRET = 'usersecret408';
	
	private $addedPermissionIds = array();
	
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
		$this->addedPermissionIds = array();
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
		PermissionPeer::setUseCriteriaFilter(false);
		foreach ($this->addedPermissionIds as $id) {
			try
			{
				
				$obj = PermissionPeer::retrieveByPK($id);
				if ($obj) {
					$obj->delete();
				}
			}
			catch (PropelException $e) {}
		}
		PermissionPeer::setUseCriteriaFilter(true);
		$this->addedPermissionIds = array();
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
	
	/**
	 * @return KalturaPermission
	 */
	private function addPermissionWrap(KalturaPermission $permission)
	{
		$addedPermission = $this->client->permission->add($permission);
		$this->addedPermissionIds[] = $addedPermission->id;
		return $addedPermission;
	}
	
	public function testUserSessionFailures()
	{
		//TODO: can't call any action other than GetCurrentPermissions with a normal user ks
		
		$this->markTestIncomplete ( "testUserSessionFailures test not implemented" );
	}
	
	
	public function testAddAction()
	{
		//TODO: can't add with no name
		
		//TODO: add with no status -> status ACTIVE returned
		
		//TODO: returned type is always KalturaPermissionType::NORMAL
		
		//TODO: can't add permission for another partner
		
		//TODO: can't add permission with a name that already exists, even for partner 0
		
		//TODO: can't add permission item that does not exist
		
		//TODO: failure setting permission items that the partner doesn't have
		
		//TODO: normal add works with all parameters as defined	

		//TODO: verify that the permission items are set right
		
		//TODO: add permission to user and verify that he can now access the new permission items!
		
		$this->markTestIncomplete ( "testAddAction test not implemented" );
	}
	
	public function testGetAction()
	{
		//TODO: normal add+get works
		
		//TODO: can't get permission of other partners
		
		//TODO: can get both current partner and partner 0 permissions
		
		$this->markTestIncomplete ( "testGetAction test not implemented" );
	}
	
	public function testListAction()
	{
		//TODO: list works with the right totalCount + count(objects)
		
		//TODO: list both current partner and partner 0 permissions
		
		//TODO: add/update then check that list response is updated
		
		//TODO: check that only partner 0 and current partner roles are returned
		
		//TODO: check that all partner 0 roles are returned
		
		$this->markTestIncomplete ( "testListAction test not implemented" );
	}
	
	public function testUpdateAction()
	{
		//TODO: normal update works
		
		//TODO: failure to update partner 0 permissions
		
		//TODO: failure to update permission type
		
		//TODO: failure to update permission partner id
		
		//TODO: failure setting permission items that the partner doesn't have
		
		//TODO: failure updating to name that already exists
		
		//TODO: what to do when changing permission name ??
		
		//TODO: failure to update id
		
		//TODO: verify that only given parameters were updated and not other parameters
	
		//TODO: failure to update another partner's permissions
		
		$this->markTestIncomplete ( "testUpdateAction test not implemented" );
	}
	
	public function testDeleteAction()
	{
		//TODO: failure to delete partner 0 permissions
		
		//TODO: failure to delete another partner's permissions
		
		//TODO: normal delete works
		
		//TODO: verify that deleted permissions doesn't return in get or list
		
		$this->markTestIncomplete ( "testDeleteAction test not implemented" );
	}
	
	public function testGetCurrentPermissionsAction()
	{
		//TODO: verify that the whole list is returned
		//TODO: should it include not-NORMAL permisison types ?
		
		$this->markTestIncomplete ( "testGetCurrentPermissionsAction test not implemented" );
	}
		

}
