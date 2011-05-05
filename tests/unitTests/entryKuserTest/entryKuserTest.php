<?php

define('KALTURA_CLIENT_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'lib');

require_once 'PHPUnit\Framework\TestCase.php';
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'bootstrap.php');
//require_once(KALTURA_CLIENT_PATH);

/**
 * 
 * Tests the kuser changes in the checkAndSetValidUser
 * @author Roni
 *
 */
class entryKuserTest extends PHPUnit_Framework_TestCase
{
	const TEST_PARTNER_ID = 495787;
	const TEST_ADMIN_SECRET = '2dc17b5563696fceb430a8431a2e4a32';
	const TEST_USER_SECRET = '526603c21b71f4c43b9751bfcca6f387';

	/**
	 * @return Partner
	 */
	private function getDbPartner()
	{
		return PartnerPeer::retrieveByPK(self::TEST_PARTNER_ID);
	}
	
	public $entryId = '0_cdnzza3c';
	public $puser1 = 'test1';
	public $puser2 = 'test2';
	public $partnerId = 495787;  
	public $adminSecret = '2dc17b5563696fceb430a8431a2e4a32';
	public $userSecret = '526603c21b71f4c43b9751bfcca6f387';
	public $originalPuser = null; 
	
	/**
	 * Starts a new session
	 * @param KalturaSessionType $type
	 * @param string $userId
	 */
	private function startSession($type, $userId = null)
	{
		print("start session\n");
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
	 * @var KalturaClient
	 */
	private $client = null;
		
	/**
	 * 
	 * Gets the kuser for the given puser id 
	 * @param string $puserId
	 */
	private function getKuserIdFromPuser($puserId)
	{
		kuserPeer::clearInstancePool();
		$kuser = kuserPeer::getKuserByPartnerAndUid(self::TEST_PARTNER_ID, $puserId);
		print("in getKuserIdFromPuser kuserId [" . $kuser->getId() . "]\n");
		return $kuser->getId();
	}
	
	/**
	 * 
	 * Gets the kuser for the given entry id 
	 * @param string $entryId
	 */
	private function getKuserIdFromEntry($entryId)
	{
		entryPeer::clearInstancePool();
		$entry = entryPeer::retrieveByPK($entryId);
		print("in getKuserIdFromEntry kuserId [ ". $entry->getKuserId() ."]\n");
		return $entry->getKuserId();
	}
	
	/**
	 * 
	 * Checks if the given exception has the same message and code
	 * @param KalturaException $exceptionThrown
	 * @param int $code
	 * @param string $message
	 */
	private function checkException($exceptionThrown, array $code = null, $message = null)
	{
		print ("in checkException\n");
		if (!$exceptionThrown) {
			$this->fail('No exception was thrown');
		}
		
		if ($code && !in_array($exceptionThrown->getCode(), $code)) {
				$this->fail('Exception thrown with code ['.$exceptionThrown->getCode().'] instead of ['.$code.']');
			}
							
		if ($message && $exceptionThrown->getMessage() != $message) {
			$this->fail('Exception thrown with message ['.$exceptionThrown->getMessage().'] instead of ['.$message.']');
		}
	}

	/**
	 * 
	 * Switchthe users on the entry
	 * @param KalturaBaseEntry $entry
	 */
	private function switchUsers(KalturaBaseEntry $entry)
	{
		if($entry->userId == $this->puser1)
		{
			$entry->userId = $this->puser2;
		}
		else if($entry->userId == $this->puser2)
		{
			$entry->userId = $this->puser1;
		}
		
		print("In switchUsers originalPuser[$this->originalPuser], newUserId[$entry->userId]\n");
		
		return $entry;
	}
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		if (!self::TEST_PARTNER_ID || !self::TEST_ADMIN_SECRET || !self::TEST_USER_SECRET)
		{
	     	die('Test partners were not defined - quitting test!');
		}
		
		parent::setUp ();
		$this->client = $this->getClient(self::TEST_PARTNER_ID);
		entryPeer::clearInstancePool();
		kuserPeer::clearInstancePool();
		PartnerPeer::clearInstancePool();
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		kuserPeer::clearInstancePool();
		PartnerPeer::clearInstancePool();
		entryPeer::clearInstancePool();
		
		$this->client = null;		
		
		parent::tearDown ();
	}

	/**
	 * 
	 * Test if the user update is valid
	 */
	public function testUpdateAction()
	{
		$this->startSession(KalturaSessionType::ADMIN);
		
		$entry = $this->client->baseEntry->get($this->entryId);
		$this->originalPuser = $entry->userId; 
		$originalKuser = $this->getKuserIdFromEntry($this->entryId);
		
		print ("original puser [$this->originalPuser], original kuser [$originalKuser]\n");
		$entry = $this->switchUsers($entry);
		
		$this->client->baseEntry->update($this->entryId, $entry);
		
		$updatedEntry = $this->client->baseEntry->get($this->entryId);
		
		print("updatedEntry->userId [$updatedEntry->userId], originalPuser [$this->originalPuser] \n");
		if($updatedEntry->userId != $this->originalPuser) // puser was update we now check if the kuser was changed as well
		{
			$newKuserId = $this->getKuserIdFromPuser($updatedEntry->userId);
			$entryKuserId = $this->getKuserIdFromEntry($this->entryId);
			if($newKuserId == $entryKuserId)
			{
				//if the kusers are the same then all is okay
				//return; success!!! so nothing todo
				$this->assertEquals(true, true); // insert 1 success
			}
			else  //the kusers are not the same
			{
				$this->fail("The Kuser wasn't changed");
			}
			
		}
		else 
		{
			$this->fail("The Puser wasn't changed");
		}
		
		// after we did this we need to fail an update
		$this->failUpdate($this->puser1);
		$this->failUpdate($this->puser2);
	}
	
	/**
	 * 
	 * Tries to update an entry with a user KS (update should fail with exception)
	 * @param string $puserId
	 */
	private function failUpdate($puserId)
	{
		$this->startSession(KalturaSessionType::USER, 'test1');
				
		$entry = $this->client->baseEntry->get($this->entryId);
		$entry = $this->switchUsers($entry);
		
		try 
		{
			$this->client->baseEntry->update($this->entryId, $entry);
			$this->fail("UpdateShould fail");
		}
		catch(Exception $e)
		{
			$this->checkException($e, array('INVALID_KS', 'PROPERTY_VALIDATION_ADMIN_PROPERTY'));
		}
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
		
		$config->serviceUrl = 'devtests.kaltura.co.cc';//kConf::get('apphome_url');
		$client = new KalturaClient($config);
		return $client;
	}
}