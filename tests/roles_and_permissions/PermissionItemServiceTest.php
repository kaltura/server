<?php

require_once 'PHPUnit\Framework\TestCase.php';
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'bootstrap.php');
require_once(KALTURA_CLIENT_PATH);

/**
 * test case.
 */
class PermissionItemServiceTest extends PHPUnit_Framework_TestCase {
	
	const TEST_PARTNER_ID = 408;
	const TEST_ADMIN_SECRET = 'adminsecret408';
	const TEST_USER_SECRET = 'usersecret408';
	
	private $addedPermissionItemIds = array();
	
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
		$this->addedPermissionItemIds = array();
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
		PermissionItemPeer::setUseCriteriaFilter(false);
		foreach ($this->addedPermissionItemIds as $id) {
			try
			{
				
				$obj = PermissionItemPeer::retrieveByPK($id);
				if ($obj) {
					$obj->delete();
				}
			}
			catch (PropelException $e) {}
		}
		PermissionItemPeer::setUseCriteriaFilter(true);
		$this->addedPermissionItemIds = array();
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
	 * @return KalturaPermissionItem
	 */
	private function addPermissionItemWrap(KalturaPermissionItem $permissionItem)
	{
		$addedPermissionItem = $this->client->permissionItem->add($permissionItem);
		$this->addedPermissionItemIds[] = $addedPermissionItem->id;
		return $addedPermissionItem;
	}
	
	
	public function testAddAction()
	{
		//TODO: can't add with normal partner
		
		//TODO: should check the action with admin console partner!
		
		$this->markTestIncomplete ( "testAddAction test not implemented" );
	}
	
	public function testUpdateAction()
	{
		//TODO: can't update with normal partner
		
		//TODO: should check the action with admin console partner!
		
		$this->markTestIncomplete ( "testUpdateAction test not implemented" );
	}
	
	public function testDeleteAction()
	{
		//TODO: can't delete with normal partner
		
		//TODO: should check the action with admin console partner!
		
		$this->markTestIncomplete ( "testDeleteAction test not implemented" );
	}
	
	
	public function testGetAction()
	{
		//TODO: normal add+get works
		
		//TODO: can't get permission item of other partners
		
		//TODO: can get both current partner and partner 0 permission items
		
		$this->markTestIncomplete ( "testGetAction test not implemented" );
	}
	
	public function testListAction()
	{
		//TODO: list works with the right totalCount + count(objects)
		
		//TODO: list both current partner and partner 0 permission items
				
		//TODO: check that only partner 0 and current partner permission items are returned
		
		//TODO: check that all partner 0 permission items are returned
		
		$this->markTestIncomplete ( "testListAction test not implemented" );
	}
		

}
