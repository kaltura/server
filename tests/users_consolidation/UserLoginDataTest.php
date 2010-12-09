<?php

require_once 'PHPUnit\Framework\TestCase.php';
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'bootstrap.php');

/**
 * UserLoginData test case.
 */
class UserLoginDataTest extends PHPUnit_Framework_TestCase
{
	
	/**
	 * @var UserLoginData
	 */
	private $UserLoginData;
	
	/**
	 * @var Partner
	 */
	private $dummyPartner;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp()
	{
		parent::setUp ();
		$this->UserLoginData = new UserLoginData(/* parameters */);
		$this->dummyPartner = new Partner();
		$this->dummyPartner->setName('TEST ONLY PARTNER!');
		$this->dummyPartner->save();
		$this->UserLoginData->setConfigPartnerId($this->dummyPartner->getId());
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{
		$this->UserLoginData->delete();
		$this->UserLoginData = null;
		$this->dummyPartner->delete();
		$this->dummyPartner = null;
		parent::tearDown ();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct()
	{
		// TODO Auto-generated constructor
	}
		
	/**
	 * Tests UserLoginData->setLastLoginPartnerId() & UserLoginData->getLastLoginPartnerId()
	 */
	public function testGetSetLastLoginPartnerId()
	{
		$this->assertNull($this->UserLoginData->getLastLoginPartnerId());
		$partnerId = rand(10, 30000);		
		$this->UserLoginData->setLastLoginPartnerId($partnerId);
		$this->assertEquals($partnerId, $this->UserLoginData->getLastLoginPartnerId());
		$this->UserLoginData->save();
		$this->assertEquals($partnerId, $this->UserLoginData->getLastLoginPartnerId());
		
		$c = new Criteria();
		$fromDb = UserLoginDataPeer::retrieveByPK($this->UserLoginData->getId());
		$this->assertEquals($partnerId, $fromDb->getLastLoginPartnerId());
	}
	
	
	/**
	 * Tests UserLoginData->setPassword()
	 */
	public function testSetPassword()
	{
		$this->assertNull($this->UserLoginData->getSalt());
		$this->assertNull($this->UserLoginData->getSha1Password());
		$this->assertNull($this->UserLoginData->getPasswordUpdatedAt());
		
		$password = uniqid();		
		$this->UserLoginData->setPassword($password);
		$salt = $this->UserLoginData->getSalt();
		$sha1 = $this->UserLoginData->getSha1Password();
		$passwordUpdatedAt = $this->UserLoginData->getPasswordUpdatedAt();
		
		$this->assertNotNull($salt);
		$this->assertNotNull($sha1);
		$this->assertNotNull($passwordUpdatedAt);
		$this->assertTrue(sha1( $this->UserLoginData->getSalt().$password ) == $this->UserLoginData->getSha1Password() );

		sleep(2); // sleeping so that current time for setting passwordUpdatedAt will be changed
		$password = uniqid();
		$this->UserLoginData->setPassword($password);
		$this->assertNotEquals($salt, $this->UserLoginData->getSalt());
		$this->assertNotEquals($sha1, $this->UserLoginData->getSha1Password());
		$this->assertNotEquals($passwordUpdatedAt, $this->UserLoginData->getPasswordUpdatedAt());		
	}
	
	/**
	 * Tests UserLoginData->isPasswordValid()
	 */
	public function testIsPasswordValid()
	{
		$rightPassword = uniqid();
		$wrongPassword = uniqid();
		$this->UserLoginData->setPassword($rightPassword);
		$this->assertTrue($this->UserLoginData->isPasswordValid($rightPassword));
		$this->assertFalse($this->UserLoginData->isPasswordValid($wrongPassword));	
	}
	
	/**
	 * Tests UserLoginData->resetPassword()
	 */
	public function testResetPassword()
	{
		// check start
		$this->assertNull($this->UserLoginData->getPasswordHashKey());
				
		$this->dummyPartner->setNumPrevPassToKeep(3);
		$this->dummyPartner->save();
		
		// set first password
		$pass1 = $this->UserLoginData->resetPassword();
		$this->assertTrue($this->UserLoginData->isPasswordValid($pass1));
		
		// no reset when old password is wrong
		$pass2 = uniqid();
		$this->assertNull($this->UserLoginData->resetPassword($pass2, uniqid()));
		$this->assertNull($this->UserLoginData->resetPassword($pass2, null));
		$this->assertNull($this->UserLoginData->resetPassword($pass2, ''));
		$this->assertNull($this->UserLoginData->resetPassword($pass2, 0));		
		
		// reset password with right old password
		$pass2_2 = $this->UserLoginData->resetPassword($pass2, $pass1);
		$this->assertEquals($pass2, $pass2_2);
		$this->assertFalse($this->UserLoginData->isPasswordValid($pass1));
		$this->assertTrue($this->UserLoginData->isPasswordValid($pass2));
		
		// set parameters for later check [*] below
		$this->UserLoginData->setLoginAttempts(3);
		$this->UserLoginData->setLoginBlockedUntil(time());
		$this->assertEquals(3, $this->UserLoginData->getLoginAttempts());
		$this->assertNotNull($this->UserLoginData->getLoginBlockedUntil());
		
		// check password used before
		$pass3 = uniqid();
		$this->UserLoginData->resetPassword($pass3, $pass2_2);
		$this->assertFalse($this->UserLoginData->passwordUsedBefore($pass1));
		$this->assertTrue($this->UserLoginData->passwordUsedBefore($pass2));
		$this->assertTrue($this->UserLoginData->passwordUsedBefore($pass3));
		
		$pass4 = uniqid();
		$this->UserLoginData->resetPassword($pass4, $pass3);
		$pass5 = uniqid();
		$this->UserLoginData->resetPassword($pass5, $pass4);
		$this->assertFalse($this->UserLoginData->passwordUsedBefore($pass1));
		$this->assertFalse($this->UserLoginData->passwordUsedBefore($pass2));
		$this->assertTrue($this->UserLoginData->passwordUsedBefore($pass3));
		$this->assertTrue($this->UserLoginData->passwordUsedBefore($pass4));
		$this->assertTrue($this->UserLoginData->passwordUsedBefore($pass5));
		
		// check changed parameters [*]
		$this->assertEquals(0, $this->UserLoginData->getLoginAttempts());
		$this->assertNull($this->UserLoginData->getLoginBlockedUntil());
		$this->assertNotNull($this->UserLoginData->getPasswordHashKey());
		
		// check new hash key validity
		$dataFromHash = UserLoginDataPeer::isHashKeyValid($this->UserLoginData->getPasswordHashKey());
		$this->assertEquals($this->UserLoginData->getId(), $dataFromHash->getId());
	}
	
	/**
	 * Tests UserLoginData->getLoginAttempts() && UserLoginData->setLoginAttempts() && UserLoginData->incLoginAttempts()
	 */
	public function testGetSetIncLoginAttempts()
	{
		$this->assertNull($this->UserLoginData->getLoginAttempts());
		$this->UserLoginData->incLoginAttempts();
		$this->assertEquals(1, $this->UserLoginData->getLoginAttempts());
		$this->UserLoginData->incLoginAttempts();
		$this->assertEquals(2, $this->UserLoginData->getLoginAttempts());
		$incTimes = rand(5, 30);
		for ($i = 0; $i < $incTimes; $i++)
		{
			$this->UserLoginData->incLoginAttempts();
		}
		$this->assertEquals(2+$incTimes, $this->UserLoginData->getLoginAttempts());
		
		$rand = rand(0, 200);
		$this->UserLoginData->setLoginAttempts($rand);
		$this->assertEquals($rand, $this->UserLoginData->getLoginAttempts());
	}
	
	/**
	 * Tests UserLoginData->setPasswordUpdatedAt() && UserLoginData->getPasswordUpdatedAt()
	 */
	public function testSetGetPasswordUpdatedAt()
	{
		
		$this->assertNull($this->UserLoginData->getPasswordUpdatedAt());
				
		$time = time();
		$format = 'Y-m-d H:i:s';
		
		$dt = new DateTime('@'.$time, new DateTimeZone('UTC'));
		$dt->setTimeZone(new DateTimeZone(date_default_timezone_get()));
		
		$this->UserLoginData->setPasswordUpdatedAt($time);
		$this->assertEquals($dt->format($format), $this->UserLoginData->getPasswordUpdatedAt());
		$this->UserLoginData->save();
		$this->assertEquals($dt->format($format), $this->UserLoginData->getPasswordUpdatedAt());
		
		$c = new Criteria();
		$fromDb = UserLoginDataPeer::retrieveByPK($this->UserLoginData->getId());
		$this->assertEquals($dt->format($format), $fromDb->getPasswordUpdatedAt($format));
		// test a different format
		$format = 'Y/m/d';
		$this->assertEquals($dt->format($format), $fromDb->getPasswordUpdatedAt($format));
	}
	
	
	/**
	 * Tests UserLoginData->setPasswordHashKey() && UserLoginData->getPasswordHashKey()
	 */
	public function testSetGetPasswordHashKey()
	{
		// check clean
		$this->assertNull($this->UserLoginData->getPasswordHashKey());
		
		// check basic set + get
		$random = uniqid();
		$this->UserLoginData->setPasswordHashKey($random);
		$this->assertEquals($random, $this->UserLoginData->getPasswordHashKey());
		
		// check set + get after db save
		$random = uniqid();
		$this->UserLoginData->setPasswordHashKey($random);
		$this->UserLoginData->save();
		$this->assertEquals($random, $this->UserLoginData->getPasswordHashKey());
		$fromDb = UserLoginDataPeer::retrieveByPK($this->UserLoginData->getId());
		$this->assertEquals($random, $fromDb->getPasswordHashKey());
	}
	
	
	/**
	 * Tests UserLoginData->resetPreviousPasswords()
	 */
	public function testResetPreviousPasswords()
	{
		$this->assertNull($this->UserLoginData->getPreviousPasswords());
		$this->assertNull($this->UserLoginData->getSalt());
		$this->assertNull($this->UserLoginData->getSha1Password());
		
		$partnerId = null;
		$pass = uniqid();
		$this->UserLoginData->setPassword($pass);		
		$this->UserLoginData->addToPreviousPasswords($this->UserLoginData->getSha1Password(), $this->UserLoginData->getSalt(), $partnerId);
		$pass = uniqid();
		$this->UserLoginData->setPassword($pass);
		$this->UserLoginData->addToPreviousPasswords($this->UserLoginData->getSha1Password(), $this->UserLoginData->getSalt(), $partnerId);
		
		$this->assertNotNull($this->UserLoginData->getPreviousPasswords());
		
		$this->UserLoginData->resetPreviousPasswords();
		$this->assertNull($this->UserLoginData->getPreviousPasswords());
	}
		
	/**
	 * Tests UserLoginData->getPreviousPasswords() & UserLoginData->setPreviousPasswords()
	 */
	public function testGetSetPreviousPasswords()
	{
		// check clean
		$this->assertNull($this->UserLoginData->getPreviousPasswords());
		
		// check basic set + get
		$random = '';
		for ($i=0; $i<rand(100, 1000); $i++) {
			$random .= uniqid();
		}
		
		$this->UserLoginData->setPreviousPasswords($random);
		$this->assertEquals($random, $this->UserLoginData->getPreviousPasswords());
		
		// check set + get after db save
		$random = '';
		for ($i=0; $i<rand(100, 1000); $i++) {
			$random .= uniqid();
		}
		$this->UserLoginData->setPreviousPasswords($random);
		$this->UserLoginData->save();
		$this->assertEquals($random, $this->UserLoginData->getPreviousPasswords());
		$fromDb = UserLoginDataPeer::retrieveByPK($this->UserLoginData->getId());
		$this->assertEquals($random, $fromDb->getPreviousPasswords());	
	}
		
	/**
	 * Tests UserLoginData->passwordUsedBefore() && UserLoginData->addToPreviousPasswords()
	 */
	public function testPasswordUsedBeforeAddToPreviousPasswords()
	{
		$this->assertNull($this->UserLoginData->getPreviousPasswords());
		
		$this->dummyPartner->setNumPrevPassToKeep(3);
		$this->dummyPartner->save();
		
		$pass2 = UserLoginDataPeer::generateNewPassword();
		$pass3 = UserLoginDataPeer::generateNewPassword();
		
		$this->assertFalse($this->UserLoginData->passwordUsedBefore($pass2));
		$this->assertFalse($this->UserLoginData->passwordUsedBefore($pass3));
		
		$pass1 = $this->UserLoginData->resetPassword();
		$this->UserLoginData->resetPassword($pass1, $pass1);
		$this->UserLoginData->resetPassword($pass2, $pass1);
		$this->UserLoginData->resetPassword($pass3, $pass2);
		
		$this->assertTrue($this->UserLoginData->passwordUsedBefore($pass1));
		$this->assertTrue($this->UserLoginData->passwordUsedBefore($pass2));
		$this->assertTrue($this->UserLoginData->passwordUsedBefore($pass3));
		
		$pass4 = uniqid();
		$this->UserLoginData->resetPassword($pass4, $pass3);
		$this->assertFalse($this->UserLoginData->passwordUsedBefore($pass1));
		$this->assertTrue($this->UserLoginData->passwordUsedBefore($pass2));
		$this->assertTrue($this->UserLoginData->passwordUsedBefore($pass3));
		$this->assertTrue($this->UserLoginData->passwordUsedBefore($pass4));
		
		$pass5 = uniqid();
		$this->UserLoginData->resetPassword($pass5, $pass4);
		$this->assertFalse($this->UserLoginData->passwordUsedBefore($pass1));
		$this->assertFalse($this->UserLoginData->passwordUsedBefore($pass2));
		$this->assertTrue($this->UserLoginData->passwordUsedBefore($pass3));
		$this->assertTrue($this->UserLoginData->passwordUsedBefore($pass4));
		$this->assertTrue($this->UserLoginData->passwordUsedBefore($pass5));
		
		$this->dummyPartner->setNumPrevPassToKeep(2);
		$this->dummyPartner->save();
		$this->assertFalse($this->UserLoginData->passwordUsedBefore($pass1));
		$this->assertFalse($this->UserLoginData->passwordUsedBefore($pass2));
		$this->assertFalse($this->UserLoginData->passwordUsedBefore($pass3));
		$this->assertTrue($this->UserLoginData->passwordUsedBefore($pass4));
		$this->assertTrue($this->UserLoginData->passwordUsedBefore($pass5));
		
		$this->dummyPartner->setNumPrevPassToKeep(0);
		$this->dummyPartner->save();
		$this->assertFalse($this->UserLoginData->passwordUsedBefore($pass1));
		$this->assertFalse($this->UserLoginData->passwordUsedBefore($pass2));
		$this->assertFalse($this->UserLoginData->passwordUsedBefore($pass3));
		$this->assertFalse($this->UserLoginData->passwordUsedBefore($pass4));
		$this->assertFalse($this->UserLoginData->passwordUsedBefore($pass5));
		
		$pass6 = uniqid();
		$this->UserLoginData->resetPassword($pass6, $pass5);
		$this->assertFalse($this->UserLoginData->passwordUsedBefore($pass1));
		$this->assertFalse($this->UserLoginData->passwordUsedBefore($pass2));
		$this->assertFalse($this->UserLoginData->passwordUsedBefore($pass3));
		$this->assertFalse($this->UserLoginData->passwordUsedBefore($pass4));
		$this->assertFalse($this->UserLoginData->passwordUsedBefore($pass5));
		$this->assertFalse($this->UserLoginData->passwordUsedBefore($pass6));
	}
		
	/**
	 * Tests UserLoginData->getMaxLoginAttempts()
	 */
	public function testGetMaxLoginAttempts()
	{
		$this->dummyPartner->setMaxLoginAttempts(null);
		$this->dummyPartner->save();
		$this->assertEquals(kConf::get('user_login_max_wrong_attempts'), $this->UserLoginData->getMaxLoginAttempts());
		
		$this->dummyPartner->setMaxLoginAttempts(5);
		$this->dummyPartner->save();
		$this->assertEquals(5, $this->UserLoginData->getMaxLoginAttempts());
		
		$this->dummyPartner->setMaxLoginAttempts(null);
		$this->dummyPartner->save();
		$this->assertEquals(kConf::get('user_login_max_wrong_attempts'), $this->UserLoginData->getMaxLoginAttempts());
		
		$this->dummyPartner->setMaxLoginAttempts(3);
		$this->dummyPartner->save();
		$this->assertEquals(3, $this->UserLoginData->getMaxLoginAttempts());
	}
	
	/**
	 * Tests UserLoginData->getLoginBlockPeriod()
	 */
	public function testGetLoginBlockPeriod()
	{
		$this->dummyPartner->setLoginBlockPeriod(null);
		$this->dummyPartner->save();
		$this->assertEquals(kConf::get('user_login_block_period'), $this->UserLoginData->getLoginBlockPeriod());
		
		$this->dummyPartner->setLoginBlockPeriod(5);
		$this->dummyPartner->save();
		$this->assertEquals(5, $this->UserLoginData->getLoginBlockPeriod());
		
		$this->dummyPartner->setLoginBlockPeriod(null);
		$this->dummyPartner->save();
		$this->assertEquals(kConf::get('user_login_block_period'), $this->UserLoginData->getLoginBlockPeriod());
		
		$this->dummyPartner->setLoginBlockPeriod(3);
		$this->dummyPartner->save();
		$this->assertEquals(3, $this->UserLoginData->getLoginBlockPeriod());
	}
	
	/**
	 * Tests UserLoginData->getPassReplaceFreq()
	 */
	public function testGetPassReplaceFreq()
	{
		$this->dummyPartner->setPassReplaceFreq(null);
		$this->dummyPartner->save();
		$this->assertEquals(kConf::get('user_login_password_replace_freq'), $this->UserLoginData->getPassReplaceFreq());
		
		$this->dummyPartner->setPassReplaceFreq(5);
		$this->dummyPartner->save();
		$this->assertEquals(5, $this->UserLoginData->getPassReplaceFreq());
		
		$this->dummyPartner->setPassReplaceFreq(null);
		$this->dummyPartner->save();
		$this->assertEquals(kConf::get('user_login_password_replace_freq'), $this->UserLoginData->getPassReplaceFreq());
		
		$this->dummyPartner->setPassReplaceFreq(3);
		$this->dummyPartner->save();
		$this->assertEquals(3, $this->UserLoginData->getPassReplaceFreq());
	}
	
	/**
	 * Tests UserLoginData->getNumPrevPassToKeep()
	 */
	public function testGetNumPrevPassToKeep() {
		$this->dummyPartner->setNumPrevPassToKeep(null);
		$this->dummyPartner->save();
		$this->assertEquals(kConf::get('user_login_num_prev_passwords_to_keep'), $this->UserLoginData->getNumPrevPassToKeep());
		
		$this->dummyPartner->setNumPrevPassToKeep(5);
		$this->dummyPartner->save();
		$this->assertEquals(5, $this->UserLoginData->getNumPrevPassToKeep());
		
		$this->dummyPartner->setNumPrevPassToKeep(null);
		$this->dummyPartner->save();
		$this->assertEquals(kConf::get('user_login_num_prev_passwords_to_keep'), $this->UserLoginData->getNumPrevPassToKeep());
		
		$this->dummyPartner->setNumPrevPassToKeep(3);
		$this->dummyPartner->save();
		$this->assertEquals(3, $this->UserLoginData->getNumPrevPassToKeep());
	}
	
		
	/**
	 * Tests UserLoginData->isPassHashKeyValid()
	 */
	public function testIsPassHashKeyValid() {
		// TODO Auto-generated UserLoginDataTest->testIsPassHashKeyValid()
		$this->markTestIncomplete ( "isPassHashKeyValid test not implemented" );
		
		$this->UserLoginData->isPassHashKeyValid(/* parameters */);
		
	}
	
	public function testSave() {
		//TODO: test equals before and after save to db
		$this->markTestIncomplete ( "testSave test not implemented" );
	}
	
}

