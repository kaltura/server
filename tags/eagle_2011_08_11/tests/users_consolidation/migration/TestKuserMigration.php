<?php

require_once 'PHPUnit\Framework\TestCase.php';
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'bootstrap.php');

/**
 * test case.
 */
class TestKuserMigration extends PHPUnit_Framework_TestCase {
	
	/**
	 * @var kuser
	 */
	private $kuser = null;
	
	private $loginPartnerIds = null;
	
	private $adminConsolePartnerId = -2;
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		$this->kuser = null;
		parent::tearDown ();
	}
	
	protected function setUp() {
		parent::setUp();
		if (!$this->loginPartnerIds) {
			$this->loginPartnerIds = $this->getLoginPartners();
		}
	}
	
	/**
	 * @dataProvider providerTestMigration
	 */
	public function testMigration($kuserId)
	{
		$this->assertNull($this->kuser);
		$this->kuser = kuserPeer::retrieveByPK($kuserId);
		$this->assertNotNull($this->kuser);
		
		$this->assertEquals($this->kuser->getFullName(), trim($this->kuser->getFirstName().' '.$this->kuser->getLastName()));
		if ( $this->kuser->getSalt() && $this->kuser->getSha1Password() &&
		     in_array($this->kuser->getPartnerId(), $this->loginPartnerIds) )
		{
			$this->assertTrue($this->kuser->getLoginDataId());
			$loginData1 = UserLoginDataPeer::retrieveByPK($this->kuser->getLoginDataId());
			$this->assertNotNull($loginData1);
			$loginData2 = UserLoginDataPeer::getByEmail($this->kuser->getEmail());
			$this->assertNotNull($loginData2);
			$this->assertEquals($loginData1->getId(), $loginData2->getId());
			
			$this->assertEquals($this->kuser->getSalt(), $loginData2->getSalt());
			$this->assertEquals($this->kuser->getSha1Password(), $loginData2->getSha1Password());
			$this->assertEquals($this->kuser->getEmail(), $loginData2->getLoginEmail());
			
			$c = new Criteria();
			$c->addAnd(UserLoginDataPeer::LOGIN_EMAIL, $this->kuser->getEmail());
			$loginDatas = UserLoginDataPeer::doSelect($c);
			$this->assertEquals(count($loginDatas), 1);
			$this->assertEquals($loginDatas[0]->getId(), $loginData1->getId());
			
			$allKusers = kuserPeer::getByLoginDataAndPartner($this->kuser->getLoginDataId(), $this->kuser->getPartnerId());
			$this->assertEquals(count($allKusers), 1);
		}
		else {
			if ( $this->kuser->getPartnerId() != $this->adminConsolePartnerId && 
			 substr($this->kuser->getPuserId(), 0, 9) != '__ADMIN__'             )
		 	{
				$this->assertNull($this->kuser->getLoginDataId());
		 	}
		}
		
		
		if ( $this->kuser->getPartnerId() == $this->adminConsolePartnerId || 
			 substr($this->kuser->getPuserId(), 0, 9) == '__ADMIN__'         )
		{
			$this->assertTrue($this->kuser->getIsAdmin());
		}
		else {
		 	$this->assertFalse($this->kuser->getIsAdmin());
		}
		
		if ($this->kuser->getIsAdmin()) {
			$this->assertTrue($this->kuser->getIsAdmin());
		}
	}
		
	
	public function providerTestMigration() {		
		$kusers = kuserPeer::doSelect(new Criteria()); // select all
		$ids = array();
		foreach ($kusers as $user) {
			$ids[] = array($user->getId());
		}
		return $ids;
	}
	
		
	private function getLoginPartners()
	{
		$c = new Criteria();
		$c1 = $c->getNewCriterion(PartnerPeer::SERVICE_CONFIG_ID, 'services-paramount-mobile.ct');
		$c2 = $c->getNewCriterion(PartnerPeer::SERVICE_CONFIG_ID, 'services-disney-mediabowl.ct');
		
		$c1->addOr($c2);
		
		$c->add($c1);
		$partners = partnerPeer::doSelect($c);
		$ids = array();
		foreach ($partners as $par) {
			$ids[] = $par->getId();
		}
		return $ids;
	}
	


}

