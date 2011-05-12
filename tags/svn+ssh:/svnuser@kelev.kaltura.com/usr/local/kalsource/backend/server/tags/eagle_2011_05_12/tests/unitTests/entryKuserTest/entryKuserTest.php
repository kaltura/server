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

	/**
	 * 
	 * The admin kuser id
	 * @var int
	 */
	public $adminKuserId = 2821602; //2821599;
	
	/**
	 * 
	 * Holds all created entries (for clean up)
	 * @var array
	 */
	public $createdEntries = array();

	/**
	 * 
	 * Holds the entry id for update
	 * @var string
	 */
	public $entryId = '0_cdnzza3c'; // entry for update to do on
	
	/**
	 * 
	 * Holds an existing puser id
	 * @var string
	 */
	public $puser1 = 'test1'; //existing puser 1
	
	/**
	 * 
	 * Holds an existing puser id 2
	 * @var string
	 */
	public $puser2 = 'test2'; //Existing puser 2
	
	/**
	 * 
	 * Holds a non existing puser id
	 * @var string
	 */
	public $puser3 = 'test3'; // non existing puser
	
	/**
	 * 
	 * The test partner id
	 * @var int
	 */
	public $partnerId = 495787;

	/**
	 * 
	 * The admin secret for the test partner
	 * @var string
	 */
	public $adminSecret = '2dc17b5563696fceb430a8431a2e4a32';
		
	/**
	 * The user secret for the test partner
	 * @var string
	 */
	public $userSecret = '526603c21b71f4c43b9751bfcca6f387';
	
	/**
	 * 
	 * The original puser of the given entry
	 * @var string
	 */
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
		if($kuser)
		{
			print("in getKuserIdFromPuser kuserId [" . $kuser->getId() . "]\n");
			return $kuser->getId();
		}
		
		return null;
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
//		$this->clearEntries();
		
		$this->client = null;		
		
		parent::tearDown ();
	}

	/**
	 * 
	 * Creates and returns a default entry for insertion
	 * @param $puserId - the entry puser
	 * @return KalturaMediaEntry
	 */
	private function createDefaultEntry($puserId = null)
	{
		print("Creating default entry\n");
		$entry = new KalturaMediaEntry();
		
		$entry->name = 'newEntry';
		$entry->mediaType = 1;
		$entry->categories = "Test User Logic";
		$entry->description = "test for users logic changes";
		$entry->userId = $puserId;
		
		return $entry;
	}
	
	/**
	 * 
	 * Clears all created entries
	 */
	private function clearEntries()
	{
		foreach ($this->createdEntries as $id => $entry) {
			$obj = entryPeer::retrieveByPK($id);
				if ($obj) {
					$obj->delete();
				}
		}
		
	} 
	
	/**
	 * 
	 * Deletes the given Kuser 
	 */
	private function deleteKuser($puserToDelete)
	{
		kuserPeer::clearInstancePool();
		$kuser = kuserPeer::getKuserByPartnerAndUid(self::TEST_PARTNER_ID, $puserToDelete);

		if($kuser)
		{
			$kuserId = $kuser->getId();
			print ("In delete kuser: deleting puser [$puserToDelete], kuserId [$kuserId]\n");
			$kuser->delete();
		}
		else
			print("Nothing to delete\n");
	}

	/**
	 * 
	 * Gets the admin kuser Id
	 */
	private function getAdminKuserId()
	{
		return $this->adminKuserId;
	}
	
	/**
	 * 
	 * Checks if the entry was created with the given puser
	 * @param KalturaMediaEntry $addedEntry
	 * @param string $entryPuser
	 */
	private function checkEntryAfterAdd($addedEntry, $puserId = null)
	{
		$entryKuser = $this->getKuserIdFromEntry($addedEntry->id);
		$entryPuserId = $addedEntry->userId;
		
		print("Checking Entry addedEntry->userId [$entryPuserId], puserId [$puserId]\n");
		//If the user is the same we check if the kuser is valid
		if($entryPuserId == $puserId)
		{
			if(is_null($puserId)) //no user was added we search for the admin kuser
			{
				$puserKuser = $this->getAdminKuserId();
				
			}
			else 
			{
				$puserKuser = $this->getKuserIdFromPuser($puserId);
			}
			
			if($entryKuser == $puserKuser)
			{
				$this->assertEquals(true, true); // insert 1 success
				return; //Valid we exit
			}
			else
			{
				print("Failed1");
				$this->fail("puserId [$puserId], entry Kuser [$entryKuser], puser Kuser [$puserKuser] are not equal");
			}
			
		}
		else
		{
			print("Failed2");
			
			$this->fail("puserId [$puserId], entry Puser [$entryPuserId ] are not equal");
		}
	}

	/**
	 * 
	 * tests if the entry insert is valid
	 * @param unknown_type $ksType
	 * @param unknown_type $puserId
	 */
	private function addEntryTest($ksType, $puserId, $shouldFail = false, $errorCode = null)
	{
		if(!$this->startSession($ksType, $puserId))
		{
			$this->fail("Unable to start session");
		}
		
		$entry = $this->createDefaultEntry($puserId);
		
		print("Before call\n");
		
		try {
			$result = $this->client->baseEntry->add($entry);
			
			if($result != null && $result instanceof KalturaMediaEntry)
			{
				//var_dump($result);
				$this->createdEntries[$result->id] = $result; //Adds the entry to the inserted items
				$this->checkEntryAfterAdd($result, $puserId); //checks that the given user is the user on the entry
			}
			else
			{
				print("Result is invalid :" . var_dump($result) . " \n");
							
				$this->fail("Server didn't return an entry, " . var_dump($result) . "\n");
			}
		}
		catch(Exception $e)
		{
			if($shouldFail)
			{
				$this->checkException($e, $errorCode);
			}
			else 
			{
				print("Exception was raised: " . $e->getMessage(). "\n");
				$this->fail("Exception not expected: " . $e->getMessage());
			}
		}
	}
	
	/**
	 * 
	 * Tests insertion of new entry (and different users) 
	 */
	public function testAddAction()
	{
		print("Testing Admin KS adds\n");
		//Add with admin KS - admin / user existing / not existing
		print("Test1 KS [Admin], puser [null]\n");
		$this->addEntryTest(KalturaSessionType::ADMIN, null);
		
		print("Test2 KS [Admin], puser [test1] existent \n");
		$this->addEntryTest(KalturaSessionType::ADMIN, $this->puser1); // existing
		
		print("Test3 KS [Admin], puser [test3] non existent\n");
		$this->deleteKuser($this->puser3); //check that puser 3 is non existing
		$this->addEntryTest(KalturaSessionType::ADMIN, $this->puser3);
		
		print("Testing User KS adds\n");
		//Add with user KS - user / other user existing / not existing
		print("Test4 KS [User], puser [null]\n");
		$this->addEntryTest(KalturaSessionType::USER, null, true, array('SERVICE_FORBIDDEN'));
		
		print("Test5 KS [User], puser [test1] existing\n");
		$this->addEntryTest(KalturaSessionType::USER, $this->puser1); // existing
				
		print("Test6 KS [User], puser [test3] non existent\n");
		$this->deleteKuser($this->puser3); //check that puser 3 is non existing
		$this->addEntryTest(KalturaSessionType::USER, $this->puser3, true, array('SERVICE_FORBIDDEN'));
	}
	
	/**
	 * 
	 * creates the update entry so we can update it (set only updatble fields)
	 * @param KalturaMediaEntry $entry
	 * @return KalturaMediaEntry $updatedEntry
	 */
	private function createEntryForUpdate(KalturaMediaEntry $entry)
	{
		$updateEntry = new KalturaMediaEntry();
		$updateEntry->userId = $entry->userId;
		return $updateEntry;
	}
	
	/**
	 * 
	 * Test if the user update is valid
	 */
	public function testUpdateAction()
	{
		print("\nUpdate tests started\n");
		$this->startSession(KalturaSessionType::ADMIN);
		
		$entry = $this->client->baseEntry->get($this->entryId);
		$updatedEntry = $this->createEntryForUpdate($entry);
		
		$this->originalPuser = $entry->userId;
						
		$originalKuser = $this->getKuserIdFromEntry($this->entryId);
		
		print ("original puser [$this->originalPuser], original kuser [$originalKuser]\n");
		$updatedEntry = $this->switchUsers($updatedEntry);
		
		print("Before update call\n");
		$result = $this->client->baseEntry->update($this->entryId, $updatedEntry);
		
		if(!$result instanceof KalturaMediaEntry)
		{
			$this->fail("Entry was not updated " . var_dump($result) . "\n");
		}
		
		$updatedEntry = $this->client->baseEntry->get($this->entryId);
		//var_dump($updatedEntry);
		
		print("updatedEntry->userId [$updatedEntry->userId], originalPuser [$this->originalPuser] \n");
		if($updatedEntry->userId != $this->originalPuser) // puser was update we now check if the kuser was changed as well
		{
			$newKuserId = $this->getKuserIdFromPuser($updatedEntry->userId);
			$entryKuserId = $this->getKuserIdFromEntry($this->entryId);
			if($newKuserId == $entryKuserId)
			{
				//if the kusers are the same then all is okay
				$this->assertEquals(true, true); // insert 1 success
				return; //success!!! so nothing todo
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