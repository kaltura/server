<?php

require_once 'PHPUnit\Framework\TestCase.php';
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'bootstrap.php');
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'admin_console/lib/kaltura/AdminConsoleUserPartnerData.php');

/**
 * test case.
 */
class TestSystemUserMigration extends PHPUnit_Framework_TestCase {
	
	/**
	 * @var systemUser
	 */
	private $systemUser = null;
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
	
	private $adminConsolePartnerId = -2;
	
	
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		$this->systemUser = null;
		$this->kuser = null;
		$this->loginData = null;
		parent::tearDown ();
	}
	
	
/**
	 * @dataProvider providerTestMigration
	 */
	public function testMigration($systemUserId)
	{
		$this->curId = $systemUserId;
		$this->nextUser();
		$this->assertNotNull($this->kuser, 'Kuser not found for '.$this->getParams());
		$this->assertNotNull($this->loginData, 'Login data not found for '.$this->getParams());
		$this->assertKuser();
		$this->assertLoginData();			
	}
	
	public function providerTestMigration() {		
		$systemUsers = SystemUserPeer::doSelect(new Criteria()); // select all
		$ids = array();
		foreach ($systemUsers as $user) {
			$ids[] = array($user->getId());
		}
		return $ids;
	}
	
	private function getParams()
	{
		return 'systemUser ['.$this->curId.'] kuser ['.@$this->kuser->getId().'] loginData['.@$this->loginData->getId().']';
	}
	
	private function nextUser()
	{
		$this->systemUser = systemUserPeer::retrieveByPK($this->curId);
		$this->assertNotNull($this->systemUser, 'System user not found with id ['.$this->curId.']');
		
		$cLoginData = new Criteria();
		$cLoginData->addAnd(UserLoginDataPeer::LOGIN_EMAIL, $this->systemUser->getEmail());
		$loginDatas = UserLoginDataPeer::doSelect($cLoginData);
		
		$this->assertEquals(1, count($loginDatas), 'Number of login datas found for id ['.$this->curId.']');
		$this->loginData = $loginDatas[0];
		
		$cKuser = new Criteria();
		$cKuser->addAnd(kuserPeer::LOGIN_DATA_ID, $this->loginData->getId());
		$cKuser->addAnd(kuserPeer::PARTNER_ID, $this->adminConsolePartnerId);
		$kusers = kuserPeer::doSelect($cKuser);
		
		$this->assertEquals(1, count($kusers), 'Number of kusers found for id ['.$this->curId.']');
		$this->kuser = $kusers[0];
				
		return true;
	}
	
	private function assertKuser()
	{
		// check copied values
		$this->assertEquals($this->systemUser->getFirstName(),$this->kuser->getFirstName(), 'first_name for '.$this->getParams());
		$this->assertEquals($this->systemUser->getLastName(),$this->kuser->getLastName(), 'last_name for '.$this->getParams());
		$this->assertEquals($this->systemUser->getEmail(),$this->kuser->getPuserId(), 'puserid for '.$this->getParams());
		$this->assertEquals($this->adminConsolePartnerId,$this->kuser->getPartnerId(), 'partner_id for '.$this->getParams());
		$this->assertEquals($this->systemUser->getEmail(),$this->kuser->getEmail(), 'email for '.$this->getParams());
		$this->assertEquals($this->systemUser->getName(), $this->kuser->getScreenName(), 'screen_name for '.$this->getParams());
		$this->assertEquals($this->systemUser->getName(), $this->kuser->getFullName(), 'full_name for '.$this->getParams());
		if ($this->systemUser->getStatus == systemUser::SYSTEM_USER_ACTIVE) {
			$this->assertEquals(KuserStatus::ACTIVE, $this->kuser->getStatus(), 'status '.$this->getParams());
		}
		else {
			$this->assertEquals(KuserStatus::BLOCKED, $this->kuser->getStatus(), 'status '.$this->getParams());
		}
		$this->assertEquals($this->systemUser->getDeletedAt(), $this->kuser->getDeletedAt(), 'deleted_at '.$this->getParams());
		$partnerData = unserialize($this->kuser->getPartnerData());
		var_dump($partnerData);
		$this->assertTrue(get_class($partnerData) === 'Kaltura_AdminConsoleUserPartnerData', 'PartnerData is not of type Kaltura_AdminConsoleUserPartnerData');
		$this->assertEquals($this->systemUser->getIsPrimary(), $partnerData->isPrimary, 'is_primary '.$this->getParams());
		$this->assertEquals($this->systemUser->getRole(), $partnerData->role, 'role '.$this->getParams());
		
		// check new values
		$this->assertEquals(true, $this->kuser->getIsAdmin(), 'is_admin for '.$this->getParams());	
		$this->assertEquals($this->kuser->getLoginDataId(), $this->loginData->getId());
	}
	
	
	private function assertLoginData()
	{
		// check copied values
		$this->assertEquals($this->systemUser->getEmail(),$this->loginData->getLoginEmail(), 'login_email for '.$this->getParams());
		$this->assertEquals($this->adminConsolePartnerId,$this->loginData->getConfigPartnerId(), 'config_partner_id for '.$this->getParams());
		$this->assertEquals($this->systemUser->getSalt(),$this->loginData->getSalt(), 'salt for '.$this->getParams());
		$this->assertEquals($this->systemUser->getSha1Password(),$this->loginData->getSha1Password(), 'sha1_password for '.$this->getParams());
		$this->assertEquals(0,$this->loginData->getLoginAttempts(), 'login_attempts for '.$this->getParams());
		$this->assertEquals(kConf::get('user_login_block_period'),$this->loginData->getLoginBlockPeriod(), 'login_block_period for '.$this->getParams());
		$this->assertEquals(kConf::get('user_login_max_wrong_attempts'),$this->loginData->getMaxLoginAttempts(), 'max_login_attempts for '.$this->getParams());
		$this->assertEquals(kConf::get('user_login_num_prev_passwords_to_keep'),$this->loginData->getNumPrevPassToKeep(), 'num_prev_pass_to_keep for '.$this->getParams());
		$this->assertEquals(kConf::get('user_login_password_replace_freq'),$this->loginData->getPassReplaceFreq(), 'pass_replace_freq for '.$this->getParams());
		$this->assertEquals(null,$this->loginData->getPasswordHashKey(), 'password_hash_key for '.$this->getParams());
		$this->assertEquals(null,$this->loginData->getPreviousPasswords(), 'previous_passwords for '.$this->getParams());
		$this->assertEquals(null,$this->loginData->getLoginBlockedUntil(), 'login_blocked_until for '.$this->getParams());
		$this->assertEquals($this->systemUser->getFirstName(),$this->loginData->getFirstName(), 'first_name for '.$this->getParams());
		$this->assertEquals($this->systemUser->getLastName(),$this->loginData->getLastName(), 'last_name for '.$this->getParams());
		$this->assertEquals($this->systemUser->getName(), $this->loginData->getFullName(), 'full_name for '.$this->getParams());
		
		// check new values
		$this->assertEquals($this->adminConsolePartnerId, $this->loginData->getLastLoginPartnerId(), 'last_login_partner_id for '.$this->getParams());
	}
	

}

