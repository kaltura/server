<?php

require_once 'PHPUnit\Framework\TestCase.php';
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'bootstrap.php');


/**
 * test case.
 */
class TestAdminKuserMigration extends PHPUnit_Framework_TestCase {
	
	/**
	 * @var adminKuser::
	 */
	private $adminKuser = null;
	/**
	 * @var kuser
	 */
	private $kuser = null;
	/**
	 * @var UserLoginData
	 */
	private $loginData = null;
	/**
	 * @var int
	 */
	private $curId = null;
	
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		$this->adminKuser = null;
		$this->kuser = null;
		$this->loginData = null;
		parent::tearDown ();
	}
			
	/**
	 * @dataProvider providerTestMigration
	 */
	public function testMigration($adminKuserId)
	{
		$this->curId = $adminKuserId;
		$this->nextUser();
		$this->assertNotNull($this->kuser, 'Kuser not found for '.$this->getParams());
		$this->assertNotNull($this->loginData, 'Login data not found for '.$this->getParams());
		$this->assertKuser();
		$this->assertLoginData();			
	}
	
	public function providerTestMigration() {		
		$adminKusers = adminKuserPeer::doSelect(new Criteria()); // select all
		$ids = array();
		foreach ($adminKusers as $user) {
			$ids[] = array($user->getId());
		}
		return $ids;
	}
	
	private function getParams()
	{
		return 'adminKuser ['.$this->curId.'] kuser ['.@$this->kuser->getId().'] loginData['.@$this->loginData->getId().']';
	}
	
	private function nextUser()
	{
		$this->adminKuser = adminKuserPeer::retrieveByPK($this->curId);
		$this->assertNotNull($this->adminKuser, 'Admin kuser not found with id ['.$this->curId.']');
		
		$cLoginData = new Criteria();
		$cLoginData->addAnd(UserLoginDataPeer::LOGIN_EMAIL, $this->adminKuser->getEmail());
		$loginDatas = UserLoginDataPeer::doSelect($cLoginData);
		
		$this->assertEquals(1, count($loginDatas), 'Number of login datas found for id ['.$this->curId.']');
		$this->loginData = $loginDatas[0];
		
		$cKuser = new Criteria();
		$cKuser->addAnd(kuserPeer::LOGIN_DATA_ID, $this->loginData->getId());
		$cKuser->addAnd(kuserPeer::PARTNER_ID, $this->adminKuser->getPartnerId());
		$kusers = kuserPeer::doSelect($cKuser);
		
		$this->assertEquals(1, count($kusers), 'Number of kusers found for id ['.$this->curId.']');
		$this->kuser = $kusers[0];
				
		return true;
	}
	
	private function assertKuser()
	{
		// check copied values
		$this->assertEquals($this->adminKuser->getFullName(),$this->kuser->getFullName(), 'full_name for '.$this->getParams());
		$this->assertEquals($this->adminKuser->getIcon(),$this->kuser->getIcon(), 'icon for '.$this->getParams());
		$this->assertEquals(kuserPeer::ROOT_ADMIN_PUSER_ID,$this->kuser->getPuserId(), 'puserid for '.$this->getParams());
		$this->assertEquals($this->adminKuser->getPartnerId(),$this->kuser->getPartnerId(), 'partner_id for '.$this->getParams());
		$this->assertEquals($this->adminKuser->getPicture(),$this->kuser->getPicture(), 'picture for '.$this->getParams());
		$this->assertEquals($this->adminKuser->getEmail(),$this->kuser->getEmail(), 'email for '.$this->getParams());
		$this->assertEquals($this->adminKuser->getScreenName(), $this->kuser->getScreenName(), 'screen_name for '.$this->getParams());
			
		// check new values
		$this->assertEquals(true, $this->kuser->getIsAdmin(), 'is_admin for '.$this->getParams());
		$this->assertEquals($this->kuser->getLoginDataId(), $this->loginData->getId());
	}
	
	
	private function assertLoginData()
	{
		// check copied values
		$this->assertEquals($this->adminKuser->getEmail(),$this->loginData->getLoginEmail(), 'login_email for '.$this->getParams());
		$this->assertEquals($this->adminKuser->getPartnerId(),$this->loginData->getConfigPartnerId(), 'config_partner_id for '.$this->getParams());
		$this->assertEquals($this->adminKuser->getSalt(),$this->loginData->getSalt(), 'salt for '.$this->getParams());
		$this->assertEquals($this->adminKuser->getSha1Password(),$this->loginData->getSha1Password(), 'sha1_password for '.$this->getParams());
		$this->assertEquals($this->adminKuser->getLoginAttempts(),$this->loginData->getLoginAttempts(), 'login_attempts for '.$this->getParams());
		$this->assertEquals($this->adminKuser->getLoginBlockPeriod(),$this->loginData->getLoginBlockPeriod(), 'login_block_period for '.$this->getParams());
		$this->assertEquals($this->adminKuser->getMaxLoginAttempts(),$this->loginData->getMaxLoginAttempts(), 'max_login_attempts for '.$this->getParams());
		$this->assertEquals($this->adminKuser->getNumPrevPassToKeep(),$this->loginData->getNumPrevPassToKeep(), 'num_prev_pass_to_keep for '.$this->getParams());
		$this->assertEquals($this->adminKuser->getPassReplaceFreq(),$this->loginData->getPassReplaceFreq(), 'pass_replace_freq for '.$this->getParams());
		$this->assertEquals($this->adminKuser->getPasswordHashKey(),$this->loginData->getPasswordHashKey(), 'password_hash_key for '.$this->getParams());
		$this->assertEquals($this->adminKuser->getPasswordUpdatedAt(),$this->loginData->getPasswordUpdatedAt(), 'password_updated_at for '.$this->getParams());
		$this->assertEquals($this->adminKuser->getPreviousPasswords(),$this->loginData->getPreviousPasswords(), 'previous_passwords for '.$this->getParams());
		$this->assertEquals($this->adminKuser->getLoginBlockedUntil(),$this->loginData->getLoginBlockedUntil(), 'login_blocked_until for '.$this->getParams());
		$this->assertEquals($this->adminKuser->getFullName(),$this->loginData->getFullName(), 'full_name for '.$this->getParams());
		
		// check new values
		$this->assertEquals($this->adminKuser->getPartnerId(), $this->loginData->getLastLoginPartnerId(), 'last_login_partner_id for '.$this->getParams());
	}
}