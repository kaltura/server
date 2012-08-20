<?php

require_once 'PHPUnit/Framework/TestCase.php';
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'bootstrap.php');
require_once(KALTURA_CLIENT_PATH);

/**
 * UserService test case.
 */
class UserServiceTest extends PHPUnit_Framework_TestCase {
	
	const TEST_PARTNER_ID = null;
	const TEST_ADMIN_SECRET = null;
	const TEST_USER_SECRET = null;
	
	const TEST_PARTNER_ID_2 = null;
	const TEST_ADMIN_SECRET_2 = null;
	const TEST_USER_SECRET_2 = null;

	private $createdDbObjects = array();
	
	/**
	 * @var Partner
	 */
	private $dummyPartner = null;
	
	/**
	 * @var Partner
	 */
	private $dummyPartner2 = null;
	
	/**
	 * @var KalturaClient
	 */
	private $client = null;
	
	/**
	 * @var KalturaClient
	 */
	private $client2 = null;
	
	
	/**
	 * @var int
	 */
	private $nextUserId = null;
	
	
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		
		if ( !self::TEST_PARTNER_ID   || !self::TEST_PARTNER_ID_2   ||
		     !self::TEST_ADMIN_SECRET || !self::TEST_ADMIN_SECRET_2 ||
		     !self::TEST_USER_SECRET  || !self::TEST_USER_SECRET_2    )
		     {
		     	die('Test partners were not defined - quitting test!');
		     }
		
		parent::setUp ();
		$this->client = $this->getClient(self::TEST_PARTNER_ID);
		$this->client2 = $this->getClient(self::TEST_PARTNER_ID_2);
		$this->dummyPartner = PartnerPeer::retrieveByPK(self::TEST_PARTNER_ID);
		$this->assertEquals(self::TEST_PARTNER_ID, $this->dummyPartner->getId());
		$this->dummyPartner2 = PartnerPeer::retrieveByPK(self::TEST_PARTNER_ID_2);
		$this->assertEquals(self::TEST_PARTNER_ID_2, $this->dummyPartner2->getId());
		$this->dummyPartner->setAdminLoginUsersQuota(5);
		$this->dummyPartner->save();
		$this->dummyPartner2->setAdminLoginUsersQuota(5);
		$this->dummyPartner2->save();
		$this->nextUserId = 1;
		kuserPeer::clearInstancePool();
		UserLoginDataPeer::clearInstancePool();
		kuserPeer::setDefaultCriteriaFilter(false);
		UserLoginDataPeer::setUseCriteriaFilter(false);
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		kuserPeer::clearInstancePool();
		UserLoginDataPeer::clearInstancePool();
		kuserPeer::setDefaultCriteriaFilter(false);
		UserLoginDataPeer::setUseCriteriaFilter(false);
		$this->clientConfig = null;
		$this->client = null;
		$this->nextUserId = null;
		foreach ($this->createdDbObjects as $obj) {
			try
			{
				if (get_class($obj) == 'kuser')
				{
					$updated = kuserPeer::retrieveByPK($obj->getId());
					if ($updated) {
						$loginData = UserLoginDataPeer::retrieveByPK($updated->getLoginDataId());
						if ($loginData) {
							$loginData->delete();
						}
					}
				}
				$obj->delete();
			}
			catch (PropelException $e) {}
		}
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
	
	
	/**
	 * Starts a new session
	 * @param KalturaSessionType $type
	 * @param string $userId
	 */
	private function startSession2($type, $userId)
	{
		$secret = ($type == KalturaSessionType::ADMIN) ? self::TEST_ADMIN_SECRET_2 : self::TEST_USER_SECRET_2;
		$ks = $this->client2->session->start($secret, $userId, $type, self::TEST_PARTNER_ID_2);
		$this->assertNotNull($ks);
		if (!$ks) {
			return false;
		}
		$this->client2->setKs($ks);
		return true;		
	}
	
	/**
	 * 
	 * @param unknown_type $login
	 * @param unknown_type $admin
	 * @return KalturaUser
	 */
	private function createUser($login, $admin, $screenName = null)
	{
		$newUserId = uniqid();
		$newUser = new KalturaUser();
		$newUser->email = 'test'.$this->nextUserId.'@test.com';
		$newUser->screenName = $screenName;
		$newUser->firstName = 'first '.$this->nextUserId.' '.$screenName;
		$newUser->lastName = $screenName;
		$newUser->id = $newUserId;
		
		if ($login) {
			$newUser->password = UserLoginDataPeer::generateNewPassword();
		}
		if ($admin) {
			$newUser->isAdmin = true;
		}
		
		$this->nextUserId++;
		
		return $newUser;
	}
	
	private function addUser($user)
	{
		$createdUser = $this->client->user->add($user); 
		$dbUser = kuserPeer::getKuserByPartnerAndUid(self::TEST_PARTNER_ID, $user->id);
		$this->assertNotNull($dbUser);
		$this->createdDbObjects[] = $dbUser;  // will be cleaned up later
		return $createdUser;
	}
	
	private function addUser2($user)
	{
		$createdUser = $this->client2->user->add($user); 
		$dbUser = kuserPeer::getKuserByPartnerAndUid(self::TEST_PARTNER_ID_2, $user->id);
		$this->assertNotNull($dbUser);
		$this->createdDbObjects[] = $dbUser;  // will be cleaned up later
		return $createdUser;
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
	 * Constructs the test case.
	 */
	public function __construct()
	{
		// TODO Auto-generated constructor
	}
	
	/**
	 * Tests UserService->addAction()
	 */
	public function testAddAction()
	{
		$this->startSession(KalturaSessionType::ADMIN, null);
		
		$this->dummyPartner->setAdminLoginUsersQuota(5);
		$this->dummyPartner->save();
		
		// -- add a normal end user
		$newUser = $this->createUser(false, false, __FUNCTION__);
		
		// check the returned user
		$createdUser = $this->addUser($newUser);
		$this->assertNotNull($createdUser);
		$this->assertEquals($newUser->id, $createdUser->id);
		$this->assertEquals($newUser->email, $createdUser->email);
		$this->assertEquals($newUser->firstName, $createdUser->firstName);
		$this->assertEquals($newUser->lastName, $createdUser->lastName);
		$this->assertEquals('', $createdUser->roleIds);
				
		// check the user returned from the api
		$getUser = $this->client->user->get($newUser->id);
		$this->assertNotNull($getUser);
		$this->assertEquals($createdUser, $getUser);
		
		$dbUser = kuserPeer::getKuserByPartnerAndUid(self::TEST_PARTNER_ID, $newUser->id);
		$this->assertNotNull($dbUser);

		// check that no login data was created
		$this->assertNull($dbUser->getLoginDataId());
		$this->assertFalse($getUser->loginEnabled);		
		
		$newUser = null;
		$getUser = null;
		$createdUser = null;
		$newUserId = null;
		
		// -- add a login end user
		$newUser = $this->createUser(true, false, __FUNCTION__);
		$createdUser = $this->addUser($newUser);
		
		$this->assertEquals($newUser->id, $createdUser->id);
		$this->assertEquals($newUser->email, $createdUser->email);
		$this->assertEquals($newUser->firstName, $createdUser->firstName);
		$this->assertEquals($newUser->lastName, $createdUser->lastName);
		$this->assertEquals('', $createdUser->roleIds);
		
		$dbUser = kuserPeer::getKuserByPartnerAndUid(self::TEST_PARTNER_ID, $newUser->id);
		$this->assertNotNull($dbUser);
		
		// check the user returned from the api
		$getUser = $this->client->user->get($newUser->id);
		$this->assertNotNull($getUser);
		$this->assertEquals($createdUser, $getUser);
		
		// check that login data was created
		$this->assertNotNull($dbUser->getLoginDataId());
		$loginData = UserLoginDataPeer::retrieveByPK($dbUser->getLoginDataId());
		$this->assertTrue($getUser->loginEnabled);
		$this->assertEquals($dbUser->getLoginDataId(), $loginData->getId());
		$dbUser2 = kuserPeer::getByLoginDataAndPartner($dbUser->getLoginDataId(), self::TEST_PARTNER_ID);
		$this->assertNotNull($dbUser2);
		$this->assertEquals($dbUser, $dbUser2);
		
		$this->assertEquals($newUser->firstName, $loginData->getFirstName());
		$this->assertEquals($newUser->lastName, $loginData->getLastName());
		$this->assertEquals($newUser->email, $loginData->getLoginEmail());
		$this->assertEquals(self::TEST_PARTNER_ID, $loginData->getConfigPartnerId());
		
		// try to login with the new data and check that ks is not an admin ks
		$ks = $this->client->user->loginByLoginId($getUser->email, $newUser->password, self::TEST_PARTNER_ID);
		$this->assertNotNull($ks);
		$ks = kSessionUtils::crackKs($ks);
		$this->assertNotNull($ks);
		$this->assertFalse($ks->isAdmin());
		$ks2 = $this->client->user->login(self::TEST_PARTNER_ID, $newUser->id, $newUser->password);
		$this->assertNotNull($ks2);
		$ks2 = kSessionUtils::crackKs($ks2);
		$this->assertNotNull($ks2);
		$this->assertFalse($ks2->isAdmin());
	}
	
	/**
	 * Tests user->add with adminLoginUsersQuota partner parameter
	 */
	public function testAdminLoginUsersQuota()
	{
		// check test prerequisites
		$c = new Criteria();
		$c->addAnd(kuserPeer::PARTNER_ID, $this->dummyPartner->getId());
		$c->addAnd(kuserPeer::LOGIN_DATA_ID, NULL, Criteria::NOT_EQUAL);
		$loginUsersNum = kuserPeer::doCount($c);
		if ($loginUsersNum != 1) {
			$this->fail('Partner ['.$this->dummyPartner->getId().'] does not have only 1 user - test cannot proceed');
			return;
		}
		
		$this->startSession(KalturaSessionType::ADMIN, null);
		$newUser = $this->createUser(true, false, __FUNCTION__);
		$newUser2 = $this->createUser(true, true, __FUNCTION__);
		$newUser3 = $this->createUser(true, false, __FUNCTION__);
		$newUser4 = $this->createUser(true, true, __FUNCTION__);
		$newUser5 = $this->createUser(true, true, __FUNCTION__);
		
		// check that a negative number means no new users possible
		$this->dummyPartner->setAdminLoginUsersQuota(rand(-30, -2));
		$this->dummyPartner->save();
		$exceptionThrown = false;
		try { $this->addUser($newUser2); }
		catch (Exception $e) { $exceptionThrown = $e; }
		$this->checkException($exceptionThrown, 'ADMIN_LOGIN_USERS_QUOTA_EXCEEDED');
		
		// check that null means no new users possible
		$this->dummyPartner->setAdminLoginUsersQuota(null);
		$this->dummyPartner->save();
		$exceptionThrown = false;
		try { $this->addUser($newUser2); }
		catch (Exception $e) { $exceptionThrown = $e; }
		$this->checkException($exceptionThrown, 'ADMIN_LOGIN_USERS_QUOTA_EXCEEDED');
		
		// try to add more users than the partner's quota
		$this->dummyPartner->setAdminLoginUsersQuota(3);
		$this->dummyPartner->save();
		$this->assertType('KalturaUser', $this->addUser($newUser));
		$this->assertType('KalturaUser', $this->addUser($newUser2));
		$this->assertType('KalturaUser', $this->addUser($newUser5));
		
		$exceptionThrown = false;
		try { $this->addUser($newUser4); }
		catch (Exception $e) { $exceptionThrown = $e; }
		$this->checkException($exceptionThrown, 'ADMIN_LOGIN_USERS_QUOTA_EXCEEDED');
		
		$this->assertType('KalturaUser', $this->addUser($newUser3));
		
		$this->dummyPartner->setAdminLoginUsersQuota(4);
		$this->dummyPartner->save();
		$this->assertType('KalturaUser', $this->addUser($newUser4));
	}
	
	/**
	 * Tests cases in which user->add should fail
	 */
	public function testAddActionFailures()
	{
		$this->dummyPartner->setAdminLoginUsersQuota(5);
		$this->dummyPartner->save();
		
		// test failure to add user with user ks
		$this->startSession(KalturaSessionType::USER, null);
		$newUser = $this->createUser(false, false, __FUNCTION__);
		$exceptionThrown = false;
		try { $createdUser = $this->addUser($newUser); }
		catch (Exception $e) { $exceptionThrown = $e; }
		$this->checkException($exceptionThrown, 'SERVICE_FORBIDDEN');
		
		// test failure to add same user ID twice
		$this->startSession(KalturaSessionType::ADMIN, null);
		$newUser = $this->createUser(false, false, __FUNCTION__);
		$this->assertType('KalturaUser', $this->addUser($newUser));
		$newUser->firstName = 'test add same puserId twice';
		$exceptionThrown = false;
		try { $this->addUser($newUser); }
		catch (Exception $e) { $exceptionThrown = $e; }
		$this->checkException($exceptionThrown, 'DUPLICATE_USER_BY_ID');
		
		// test failure adding same login id twice
		$this->startSession(KalturaSessionType::ADMIN, null);
		$newUser = $this->createUser(true, false, __FUNCTION__);
		$this->assertType('KalturaUser', $this->addUser($newUser));
		$newUser->firstName = 'test failure adding same login id twice';
		$newUser->id = uniqid();
		$exceptionThrown = false;
		try { $this->addUser($newUser); }
		catch (Exception $e) { $exceptionThrown = $e; }
		$this->checkException($exceptionThrown, 'DUPLICATE_USER_BY_LOGIN_ID');
		
		// same test with admin user
		$newUser->id = uniqid();
		$newUser->isAdmin = true;
		$exceptionThrown = false;
		try { $this->addUser($newUser); }
		catch (Exception $e) { $exceptionThrown = $e; }
		$this->checkException($exceptionThrown, 'DUPLICATE_USER_BY_LOGIN_ID');	
		
		// test failure adding an admin user with password but no email
		$this->startSession(KalturaSessionType::ADMIN, null);
		$newUser = $this->createUser(true, true, __FUNCTION__);
		$newUser->email = null;
		$exceptionThrown = false;
		try { $this->addUser($newUser); }
		catch (Exception $e) { $exceptionThrown = $e; }
		$this->checkException($exceptionThrown, 'INVALID_FIELD_VALUE');
		
		// test failure adding an admin user with password but invalid email
		$this->startSession(KalturaSessionType::ADMIN, null);
		$newUser = $this->createUser(true, true, __FUNCTION__);
		$newUser->email = uniqid();
		$exceptionThrown = false;
		try { $this->addUser($newUser); }
		catch (Exception $e) { $exceptionThrown = $e; }
		$this->checkException($exceptionThrown, 'INVALID_FIELD_VALUE');
		
		// test failure adding a user with an invalid password
		$this->startSession(KalturaSessionType::ADMIN, null);
		$newUser = $this->createUser(true, true, __FUNCTION__);
		$newUser->password = 'aaaa';
		$exceptionThrown = false;
		try { $this->addUser($newUser); }
		catch (Exception $e) { $exceptionThrown = $e; }
		$this->checkException($exceptionThrown, 'PASSWORD_STRUCTURE_INVALID');
	}
	
	public function testUserMultiplePartners()
	{
		$this->startSession(KalturaSessionType::ADMIN, null);
		$this->startSession2(KalturaSessionType::ADMIN, null);
		
		// add 2 end users with no login data
		$newUser = $this->createUser(false, false, __FUNCTION__);
		$createdUser1 = $this->addUser($newUser);
		$this->assertType('KalturaUser', $createdUser1);
		$createdUser2 = $this->addUser2($newUser);
		$this->assertType('KalturaUser', $createdUser2);
		$this->assertEquals(self::TEST_PARTNER_ID, $createdUser1->partnerId);
		$this->assertEquals(self::TEST_PARTNER_ID_2, $createdUser2->partnerId);
		
		// add 2 end users with same login data
		$newUser = $this->createUser(true, false, __FUNCTION__);
		$createdUser1 = $this->addUser($newUser);
		$this->assertType('KalturaUser', $createdUser1);
		$createdUser2 = $this->addUser2($newUser);
		$this->assertType('KalturaUser', $createdUser2);
		$this->assertEquals(self::TEST_PARTNER_ID, $createdUser1->partnerId);
		$this->assertEquals(self::TEST_PARTNER_ID_2, $createdUser2->partnerId);

		// check login to both partners
		$client = $this->getClient(null);
		$ks1 = $client->user->loginByLoginId($newUser->email, $newUser->password, self::TEST_PARTNER_ID);
		$this->assertNotNull($ks1);
		$ks1 = kSessionUtils::crackKs($ks1);
		$this->assertNotNull($ks1);
		$this->assertEquals(self::TEST_PARTNER_ID, $ks1->partner_id);
		$ks2 = $client->user->loginByLoginId($newUser->email, $newUser->password, self::TEST_PARTNER_ID_2);
		$this->assertNotNull($ks2);
		$ks2 = kSessionUtils::crackKs($ks2);
		$this->assertNotNull($ks2);
		$this->assertEquals(self::TEST_PARTNER_ID_2, $ks2->partner_id);
		
		// check login without partnerId -> always goes to first added partner
		$ks3 = $client->user->loginByLoginId($newUser->email, $newUser->password);
		$this->assertNotNull($ks3);
		$ks3 = kSessionUtils::crackKs($ks3);
		$this->assertNotNull($ks3);
		$this->assertEquals(self::TEST_PARTNER_ID, $ks3->partner_id);
		
		$ks3 = $client->user->loginByLoginId($newUser->email, $newUser->password);
		$this->assertNotNull($ks3);
		$ks3 = kSessionUtils::crackKs($ks3);
		$this->assertNotNull($ks3);
		$this->assertEquals(self::TEST_PARTNER_ID, $ks3->partner_id);
		
		// add 2 admin users with same login data
		$newUser = $this->createUser(true, true, __FUNCTION__);
		$createdUser1 = $this->addUser($newUser);
		$this->assertType('KalturaUser', $createdUser1);
		$createdUser2 = $this->addUser2($newUser);
		$this->assertType('KalturaUser', $createdUser2);
		$this->assertEquals(self::TEST_PARTNER_ID, $createdUser1->partnerId);
		$this->assertEquals(self::TEST_PARTNER_ID_2, $createdUser2->partnerId);

		// check login to both partners
		$client = $this->getClient(null);
		$ks1 = $client->user->loginByLoginId($newUser->email, $newUser->password, self::TEST_PARTNER_ID);
		$this->assertNotNull($ks1);
		$ks1 = kSessionUtils::crackKs($ks1);
		$this->assertNotNull($ks1);
		$this->assertEquals(self::TEST_PARTNER_ID, $ks1->partner_id);
		$ks2 = $client->user->loginByLoginId($newUser->email, $newUser->password, self::TEST_PARTNER_ID_2);
		$this->assertNotNull($ks2);
		$ks2 = kSessionUtils::crackKs($ks2);
		$this->assertNotNull($ks2);
		$this->assertEquals(self::TEST_PARTNER_ID_2, $ks2->partner_id);
		
		// check login without partnerIt -> always goes to the last logged in partner
		$ks4 = $client->user->loginByLoginId($newUser->email, $newUser->password);
		$this->assertNotNull($ks4);
		$ks4 = kSessionUtils::crackKs($ks4);
		$this->assertNotNull($ks4);
		$this->assertEquals(self::TEST_PARTNER_ID_2, $ks4->partner_id);
		
		$ks5 = $client->user->loginByLoginId($newUser->email, $newUser->password, self::TEST_PARTNER_ID);
		$this->assertNotNull($ks5);
		$ks5 = kSessionUtils::crackKs($ks5);
		$this->assertNotNull($ks5);
		$this->assertEquals(self::TEST_PARTNER_ID, $ks5->partner_id);
		
		$ks6 = $client->user->loginByLoginId($newUser->email, $newUser->password);
		$this->assertNotNull($ks6);
		$ks6 = kSessionUtils::crackKs($ks6);
		$this->assertNotNull($ks6);
		$this->assertEquals(self::TEST_PARTNER_ID, $ks6->partner_id);
	}
	
	
	public function testAddUserOnDemand()
	{
		// add entry with new user id and see if user is created or not
		$userId = uniqid().'testAddUserOnDemand';
		$c = new Criteria();
		$c->addAnd(kuserPeer::PUSER_ID, $userId);
		$this->assertNull(kuserPeer::doSelectOne($c));
		
		$this->startSession(KalturaSessionType::USER, $userId);
		$uploadToken = $this->client->baseEntry->upload(realpath(__FILE__));
		$this->assertNotNull($uploadToken);
		$entry = new KalturaBaseEntry();
		$entry->name = uniqid();
		$entry->type = KalturaEntryType::MEDIA_CLIP;
		$entry->mediaType = KalturaMediaType::VIDEO;
		$createdEntry = $this->client->baseEntry->addFromUploadedFile($entry, $uploadToken);
		$this->assertNotNull($createdEntry);
		$this->assertEquals($userId, $createdEntry->userId);
		
		$dbUser = kuserPeer::doSelectOne($c);
		$this->assertNotNull($dbUser);
		$this->createdDbObjects[] = $dbUser;
		
		// check that user is not admin and can't login
		$this->assertFalse($dbUser->getIsAdmin());
		$this->assertNull($dbUser->getLoginDataId());
		
		// add entry with admin session and verify that user created is not admin and can't login
		$userId = uniqid().'testAddUserOnDemand';
		$c = new Criteria();
		$c->addAnd(kuserPeer::PUSER_ID, $userId);
		$this->assertNull(kuserPeer::doSelectOne($c));
		
		$this->startSession(KalturaSessionType::ADMIN, $userId);
		$uploadToken = $this->client->baseEntry->upload(realpath(__FILE__));
		$this->assertNotNull($uploadToken);
		$entry = new KalturaBaseEntry();
		$entry->name = 'test';
		$entry->type = KalturaEntryType::MEDIA_CLIP;
		$entry->mediaType = KalturaMediaType::VIDEO;
		$createdEntry = $this->client->baseEntry->addFromUploadedFile($entry, $uploadToken);
		$this->assertNotNull($createdEntry);
		$this->assertEquals($userId, $createdEntry->userId);
		
		$dbUser = kuserPeer::doSelectOne($c);
		$this->assertNotNull($dbUser);
		$this->createdDbObjects[] = $dbUser;
		
		// check that user is not admin and can't login
		$this->assertFalse($dbUser->getIsAdmin());
		$this->assertNull($dbUser->getLoginDataId());
	}
	
	
	/**
	 * Tests UserService->updateAction()
	 */
	public function testUpdateAction() {
		
		//try to update password - should fail
		$this->startSession(KalturaSessionType::ADMIN, null);
		$newUser = $this->createUser(true, false, __FUNCTION__);
		$this->assertType('KalturaUser', $this->addUser($newUser));
		$newUser2 = new KalturaUser();
		$newUser2->password = uniqid();
		$exceptionThrown = false;
		try { $this->client->user->update($newUser->id, $newUser2); }
		catch (Exception $e) { $exceptionThrown = $e; }
		$this->checkException($exceptionThrown, 'PROPERTY_VALIDATION_NOT_UPDATABLE');
		
		// try to update isAdmin with user ks
		$this->startSession(KalturaSessionType::ADMIN, null);
		$newUser = $this->createUser(false, false, __FUNCTION__);
		$this->assertType('KalturaUser', $this->addUser($newUser));
		$this->startSession(KalturaSessionType::USER, null);
		$newUser2 = new KalturaUser();
		$newUser2->isAdmin = true;
		$exceptionThrown = false;
		try { $this->client->user->update($newUser->id, $newUser2); }
		catch (Exception $e) { $exceptionThrown = $e; }
		$this->checkException($exceptionThrown, 'SERVICE_FORBIDDEN');
		
		//try to update email/first_name/last_name for user in all partners
		$this->startSession(KalturaSessionType::ADMIN, null);
		$this->startSession2(KalturaSessionType::ADMIN, null);
				
		// add 2 end users with same login data to 2 partners
		$newUser = $this->createUser(true, false, __FUNCTION__);
		$createdUser1 = $this->addUser($newUser);
		$this->assertType('KalturaUser', $createdUser1);
		$createdUser2 = $this->addUser2($newUser);
		$this->assertType('KalturaUser', $createdUser2);
		$this->assertEquals(self::TEST_PARTNER_ID, $createdUser1->partnerId);
		$this->assertEquals(self::TEST_PARTNER_ID_2, $createdUser2->partnerId);
		
		$updatedUser = new KalturaUser();
		$updatedUser->firstName = 'first';
		$updatedUser->lastName = 'last';
		$updatedUser->email = 'abcd@aaaaa.com';
	}
	
	/**
	 * Tests UserService->getAction()
	 */
	public function testGetAction() {
		// TODO Auto-generated UserServiceTest->testGetAction()
		$this->markTestIncomplete ( "getAction test not implemented" );
		
		$this->UserService->getAction(/* parameters */);
		
		//TODO: no password / salt / sha1 / logindataId / kuserId
		
		//TODO: validate all other parameters are correct after adding a new user
			
	}
	
	/**
	 * Tests UserService->getByLoginIdAction()
	 */
	public function testGetByLoginIdAction() {
		// TODO Auto-generated UserServiceTest->testGetByLoginIdAction()
		$this->markTestIncomplete ( "getByLoginIdAction test not implemented" );
		
		$this->UserService->getByLoginIdAction(/* parameters */);
		
		//TODO: check that get and getByLoginId return equal results
	
	}
	
	/**
	 * Tests UserService->deleteAction()
	 */
	public function testDeleteAction()
	{
		// create a new user
		$this->startSession(KalturaSessionType::ADMIN, null);
		$newUser = $this->createUser(true, true, __FUNCTION__);
		$addedUser = $this->addUser($newUser);
		$this->assertType('KalturaUser', $addedUser);
		
		// try to delete with user ks - should fail
		$this->startSession(KalturaSessionType::USER, null);
		$exceptionThrown = false;
		try { $this->client->user->delete($newUser->id); }
		catch (Exception $e) { $exceptionThrown = $e; }
		$this->checkException($exceptionThrown, 'SERVICE_FORBIDDEN');
		
		// check that can get user
		$this->startSession(KalturaSessionType::ADMIN, null);
		$getUser = $this->client->user->get($newUser->id);
		$this->assertType('KalturaUser', $getUser);
		$this->assertEquals(KalturaUserStatus::ACTIVE, $getUser->status);		
		
		// check delete user with admin ks
		$deletedUser = $this->client->user->delete($newUser->id);
		$this->assertEquals(KalturaUserStatus::DELETED, $deletedUser->status);
		
		// delete user -> check no get
		$exceptionThrown = false;
		try { $this->client->user->get($newUser->id); }
		catch (Exception $e) { $exceptionThrown = $e; }
		$this->checkException($exceptionThrown, 'INVALID_USER_ID');
		
		// delete user -> check no list with and without filter
		$userList = $this->client->user->listAction();
		foreach($userList->objects as $user) {
			if ($user->id == $newUser->id) {
				$this->fail('user->list returned a deleted user!');
			}
		}		
		$userFilter = new KalturaUserFilter();
		$userFilter->idEqual = $newUser->id;
		$userList = $this->client->user->listAction($userFilter);
		$this->assertEquals(0, count($userList->objects));
		$this->assertEquals(0, $userList->totalCount);
		
		// check that can add a new user with same id
		$addedUser = $this->addUser($newUser);
		$this->assertType('KalturaUser', $addedUser);
		$this->assertEquals($newUser->id, $addedUser->id);
		
		// delete user from one partner only
		$this->startSession(KalturaSessionType::ADMIN, null);
		$this->startSession2(KalturaSessionType::ADMIN, null);
		$newUser = $this->createUser(false, true, __FUNCTION__);
		$createdUser1 = $this->addUser($newUser);
		$this->assertType('KalturaUser', $createdUser1);
		$newUser->isAdmin = false; // make the user not admin on the 2nd partner
		$createdUser2 = $this->addUser2($newUser);
		$this->assertType('KalturaUser', $createdUser2);
		$this->assertEquals(self::TEST_PARTNER_ID, $createdUser1->partnerId);
		$this->assertEquals(self::TEST_PARTNER_ID_2, $createdUser2->partnerId);
	
		$deletedUser = $this->client->user->delete($newUser->id);
		$this->assertEquals(KalturaUserStatus::DELETED, $deletedUser->status);
		
		$exceptionThrown = false;
		try { $this->client->user->get($newUser->id); }
		catch (Exception $e) { $exceptionThrown = $e; }
		$this->checkException($exceptionThrown, 'INVALID_USER_ID');
		
		$getUser2 = $this->client2->user->get($newUser->id);
		$this->assertEquals($createdUser2, $getUser2);
		$this->assertEquals(self::TEST_PARTNER_ID_2, $getUser2->partnerId);
		$this->assertEquals($newUser->id, $getUser2->id);
		
			
		// create user with login id on 2 partners
		$newUser = $this->createUser(true, false, __FUNCTION__);
		$createdUser1 = $this->addUser($newUser);
		$this->assertType('KalturaUser', $createdUser1);
		$newUser->isAdmin = true; // make the user admin on the 2nd partner
		$createdUser2 = $this->addUser2($newUser);
		$this->assertType('KalturaUser', $createdUser2);
		$this->assertEquals(self::TEST_PARTNER_ID, $createdUser1->partnerId);
		$this->assertEquals(self::TEST_PARTNER_ID_2, $createdUser2->partnerId);
		
		// check that login data exists
		$dbUser = kuserPeer::getKuserByPartnerAndUid(self::TEST_PARTNER_ID, $newUser->id);
		$loginDataId = $dbUser->getLoginDataId();
		$loginData = UserLoginDataPeer::retrieveByPK($loginDataId);
		$this->assertNotNull($loginData);
		
		// delete from 1st partner and verify that login data still exists
		$this->client->user->delete($newUser->id);
		$loginData = UserLoginDataPeer::retrieveByPK($loginDataId);
		$this->assertNotNull($loginData);
		
		// verify that user now logs in to the 2nd partner
		$newClient = $this->getClient(null);
		$ks = $newClient->user->loginByLoginId($newUser->email, $newUser->password);
		$this->assertNotNull($ks);
		$ks = kSessionUtils::crackKs($ks);
		$this->assertNotNull($ks);
		$this->assertEquals(self::TEST_PARTNER_ID_2, $ks->partner_id);
		
		// delete user with login id when it's the last one
		$this->client2->user->delete($newUser->id);
		UserLoginDataPeer::clearInstancePool();
		$loginData = UserLoginDataPeer::retrieveByPK($loginDataId);
		$this->assertNull($loginData);
	}
	
	/**
	 * Tests UserService->listAction()
	 */
	public function testListAction() {
		
		// check list by isAdmin and loginEnabled
		$this->dummyPartner->setAdminLoginUsersQuota(30);
		$this->dummyPartner->save();
		$this->startSession(KalturaSessionType::ADMIN, null);
		$adminLoginNum = 2;
		$normalNoLoginNum = 3;
		$normalLoginNum = 4;
		
		$adminLogin = array();
		for ($i=0; $i<$adminLoginNum; $i++) {
			$newUser = $this->createUser(true, true, 'adminLogin');
			$this->addUser($newUser);
			$adminLogin[] = $newUser;
		}
		
		$normalNoLogin = array();
		for ($i=0; $i<$normalNoLoginNum; $i++) {
			$newUser = $this->createUser(false, false, 'normalNoLogin');
			$this->addUser($newUser);
			$normalNoLogin[] = $newUser;
		}
		
		$normalLogin = array();
		for ($i=0; $i<$normalLoginNum; $i++) {
			$newUser = $this->createUser(true, false, 'normalLogin');
			$this->addUser($newUser);
			$normalLogin[] = $newUser;
		}

		
		$adminNoLoginFilter = new KalturaUserFilter();
		$adminNoLoginFilter->isAdminEqual = true;
		$adminNoLoginFilter->loginEnabledEqual = false;
		$adminLoginFilter = new KalturaUserFilter();
		$adminLoginFilter->isAdminEqual = true;
		$adminLoginFilter->loginEnabledEqual = true;
		$normalNoLoginFilter = new KalturaUserFilter();
		$normalNoLoginFilter->isAdminEqual = false;
		$normalNoLoginFilter->loginEnabledEqual = false;
		$normalLoginFilter = new KalturaUserFilter();
		$normalLoginFilter->isAdminEqual = false;
		$normalLoginFilter->loginEnabledEqual = true;
		
		$adminNoLoginList = $this->client->user->listAction($adminNoLoginFilter);
		$adminLoginList = $this->client->user->listAction($adminLoginFilter);
		$normalNoLoginList = $this->client->user->listAction($normalNoLoginFilter);
		$normalLoginList = $this->client->user->listAction($normalLoginFilter);
		
		// verify number of users
		$this->assertEquals(0, count($adminNoLoginList->objects));
		$this->assertEquals(0, $adminNoLoginList->totalCount);
		$this->assertEquals($adminLoginNum+1, count($adminLoginList->objects)); // 1 admin login user already existed
		$this->assertEquals($adminLoginNum+1, $adminLoginList->totalCount);
		$this->assertEquals($normalNoLoginNum, count($normalNoLoginList->objects));
		$this->assertEquals($normalNoLoginNum, $normalNoLoginList->totalCount);
		$this->assertEquals($normalLoginNum, count($normalLoginList->objects));
		$this->assertEquals($normalLoginNum, $normalLoginList->totalCount);
		
		for ($i=0; $i<$adminLoginNum; $i++) {
			//$this->assertEquals($adminLogin[$i]->id, $adminLoginList->objects[$i+1]->id);
		}
		for ($i=0; $i<$normalNoLoginNum; $i++) {
			$this->assertEquals($normalNoLogin[$i]->id, $normalNoLoginList->objects[$i]->id);
		}
		for ($i=0; $i<$normalLoginNum; $i++) {
			$this->assertEquals($normalLogin[$i]->id, $normalLoginList->objects[$i]->id);
		}
		
		$listResponse = $this->client->user->listAction();
		$allUsers = $listResponse->objects;
		foreach ($allUsers as $user) {
			// check that all returned users are of type KalturaUser
			$this->assertType('KalturaUser', $user);
			// check that only current partner's users were returned
			$this->assertEquals(self::TEST_PARTNER_ID, $user->partnerId);
			// check that deleted users are not returned
			$this->assertNotEquals(KalturaUserStatus::DELETED, $user->status);
		}
		
		// check that total count is right
		$this->assertEquals(count($allUsers), $listResponse->totalCount);
	}
	
	/**
	 * Tests UserService->notifyBan()
	 */
	public function testNotifyBan() {
		// TODO Auto-generated UserServiceTest->testNotifyBan()
		$this->markTestIncomplete ( "notifyBan test not implemented" );
		
		$this->UserService->notifyBan(/* parameters */);
	
	}
	
	/**
	 * Tests UserService->loginAction()
	 */
	public function testLoginAction() {
		// TODO Auto-generated UserServiceTest->testLoginAction()
		$this->markTestIncomplete ( "loginAction test not implemented" );
		
		$this->UserService->loginAction(/* parameters */);
		
		//TODO: test login with right password
		
		//TODO: test failure to login to a different partner
		
		//TODO: test failure to login with wrong email or password
		
		//TODO: test right type of ks is returned
		
		//TODO: test cannot login as blocked user
	}
	
	/**
	 * Tests UserService->loginByLoginIdAction()
	 */
	public function testLoginByLoginIdAction() {
		// TODO Auto-generated UserServiceTest->testLoginByLoginIdAction()
		$this->markTestIncomplete ( "loginByLoginIdAction test not implemented" );
		
		$this->UserService->loginByLoginIdAction(/* parameters */);
		
		//TODO: test login with right password
		
		//TODO: test failure to login to a different partner
		
		//TODO: test failure to login with wrong email or password
		
		//TODO: test right type of ks is returned
		
		//TODO: test cannot login as blocked user
	
	}
	
	/**
	 * Tests UserService->updateLoginDataAction()
	 */
	public function testUpdateLoginDataAction() {
	
		// test that cannot update to existing data
		$this->startSession(KalturaSessionType::ADMIN, null);
		$this->startSession2(KalturaSessionType::ADMIN, null);
		
		$newUser1 = $this->createUser(true, false, __FUNCTION__);
		$newUser2 = $this->createUser(true, true, __FUNCTION__);
		$addedUser1 = $this->addUser($newUser1);
		$addedUser2 = $this->addUser($newUser2);
		$exceptionThrown = false;
		try { $this->client->user->updateLoginData($newUser2->email, $newUser2->password, $newUser1->email, UserLoginDataPeer::generateNewPassword()); }
		catch (Exception $e) { $exceptionThrown = $e; }
		$this->checkException($exceptionThrown, 'LOGIN_ID_ALREADY_USED');
		
		// update and try to login with the new data
		$newUser3 = $this->createUser(true, false);
		$updatedUser = $this->client->user->updateLoginData($newUser2->email, $newUser2->password, $newUser3->email, $newUser3->password);
		$newClient = $this->getClient(null);
		$ks = $newClient->user->loginByLoginId($newUser3->email, $newUser3->password);
		$this->assertNotNull($ks);
		$ks = kSessionUtils::crackKs($ks);
		$this->assertNotNull($ks);
		$this->assertEquals($addedUser2->partnerId, $ks->partner_id);
		
		// test that updaing login data, updates the email on all kusers using it.
		$newUser4 = $this->createUser(true, true);
		$this->addUser($newUser4);
		$this->addUser2($newUser4);
		$newEmail = 'new_email@blahblah.com';
		$this->client->user->updateLogindata($newUser4->email, $newUser4->password, $newEmail);
		$getUser1 = $this->client->user->get($newUser4->id);
		$getUser2 = $this->client2->user->get($newUser4->id);
		$this->assertEquals($newEmail, $getUser1->email);
		$this->assertEquals($newEmail, $getUser2->email);
	}
	
	public function testUpdateLoginDataFailuresAction()
	{
		$this->markTestIncomplete ( "testUpdateLoginDataFailuresAction test not implemented" );
		
		//TODO: test failures
	}
	
	/**
	 * Tests UserService->resetPasswordAction()
	 */
	public function testResetPasswordAction() {
		// TODO Auto-generated UserServiceTest->testResetPasswordAction()
		$this->markTestIncomplete ( "resetPasswordAction test not implemented" );
		
		$this->UserService->resetPasswordAction(/* parameters */);
	
	}
	
	/**
	 * Tests UserService->setInitialPasswordAction()
	 */
	public function testSetInitialPasswordAction() {
		// TODO Auto-generated UserServiceTest->testSetInitialPasswordAction()
		$this->markTestIncomplete ( "setInitialPasswordAction test not implemented" );
		
		$this->UserService->setInitialPasswordAction(/* parameters */);
		
		//TODO: test that old hash key is no longer valid
		
		
	
	}
	
	/**
	 * Tests UserService->enableLoginAction()
	 */
	public function testEnableLoginAction() {
	
		// check that login can be enabled
		$this->startSession(KalturaSessionType::ADMIN, null);
		$newUser1 = $this->createUser(false, false, __FUNCTION__);
		$addedUser1 = $this->addUser($newUser1);
		$loginUser = $this->createUser(true, false, __FUNCTION__);
		$enabledUser = $this->client->user->enableLogin($newUser1->id, $loginUser->email, $loginUser->password);
		$this->assertType('KalturaUser', $enabledUser);
		$this->assertEquals($newUser1->id, $enabledUser->id);
		
		$newClient = $this->getClient(null);
		$ks = $newClient->user->loginByLoginId($loginUser->email, $loginUser->password);
		$this->assertNotNull($ks);
		$ks = kSessionUtils::crackKs($ks);
		$this->assertNotNull($ks);
		$this->assertEquals($addedUser1->partnerId, $ks->partner_id);
		
		// check failure to enable already enabled user
		$exceptionThrown = false;
		try { $this->client->user->enableLogin($newUser1->id, $loginUser->email, $loginUser->password); }
		catch (Exception $e) { $exceptionThrown = $e; }
		$this->checkException($exceptionThrown, 'USER_LOGIN_ALREADY_ENABLED');
		
		
		//TODO: test enabled login with password = null - and email should be sent to generate initial password
		
	}
	
	/**
	 * Tests UserService->disableLoginAction()
	 */
	public function testDisableLoginAction()
	{
		// check that login can be disabled
		$this->startSession(KalturaSessionType::ADMIN, null);
		$newUser1 = $this->createUser(true, false, __FUNCTION__);
		$addedUser1 = $this->addUser($newUser1);
		$loginUser = $this->createUser(true, false, __FUNCTION__);
		
		$newClient = $this->getClient(null);
		$ks = $newClient->user->loginByLoginId($newUser1->email, $newUser1->password);
		$this->assertNotNull($ks);
		$ks = kSessionUtils::crackKs($ks);
		$this->assertNotNull($ks);
		$this->assertEquals($addedUser1->partnerId, $ks->partner_id);
		
		$disabledUser = $this->client->user->disableLogin($newUser1->id);
		$this->assertType('KalturaUser', $disabledUser);
		$this->assertEquals($newUser1->id, $disabledUser->id);
		
		$exceptionThrown = false;
		try { $newClient->user->loginByLoginId($newUser1->email, $newUser1->password); }
		catch (Exception $e) { $exceptionThrown = $e; }
		$this->checkException($exceptionThrown, 'USER_NOT_FOUND');
		
		// check failure to disable already disabled user
		$exceptionThrown = false;
		try { $this->client->user->disableLogin($newUser1->id); }
		catch (Exception $e) { $exceptionThrown = $e; }
		$this->checkException($exceptionThrown, 'USER_LOGIN_ALREADY_DISABLED');
	}
	
	
	public function testPartnerRegister()
	{
		//TODO: register a new partner and verify that a new admin kuser is created
		
		//TODO: verify that new partner email was sent
		
		//TODO: verify that new user email was sent
		
		//TODO: try to register another partner with the same email and verify success and association to existing user
		
		//TODO: verify that existing user mail is sent for the new partner
		
		$this->markTestIncomplete ( "testPartnerRegister test not implemented" );
	}

}

