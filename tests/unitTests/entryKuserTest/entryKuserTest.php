<?php

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'bootstrap.php');

//TODO: fix this test

/**
 * 
 * Tests the kuser changes in the checkAndSetValidUser
 * @author Roni
 *
 */
class entryKuserTest extends KalturaApiTestCase
{
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
	public $entryId = null; // entry for update to do on
	
	/**
	 * 
	 * Holds an existing puser id
	 * @var string
	 */
	public $puser1 = null; //existing puser 1
	
	/**
	 * 
	 * Holds an existing puser id 2
	 * @var string
	 */
	public $puser2 = null; //Existing puser 2
	
	/**
	 * 
	 * Holds a non existing puser id
	 * @var string
	 */
	public $puser3 = null; // non existing puser

	/**
	 * 
	 * The original puser of the given entry
	 * @var string
	 */
	public $originalPuser = null; 
		
	/**
	 * 
	 * Gets the kuser for the given puser id 
	 * @param string $puserId
	 */
	private function getKuserIdFromPuser($puserId)
	{
		kuserPeer::clearInstancePool();
		$partnerId = KalturaGlobalData::getData("@TEST_PARTNER_ID@");
		$kuser = kuserPeer::getKuserByPartnerAndUid($partnerId, $puserId);
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
		
		$kuserId = null;
		
		if($entry)
			$kuserId = $entry->getKuserId();
		
		return $kuserId;
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
	 * Deletes the given Kuser 
	 */
	private function deleteKuser($puserToDelete)
	{
		kuserPeer::clearInstancePool();
		$partnerId = KalturaGlobalData::getData("@TEST_PARTNER_ID@");
		$kuser = kuserPeer::getKuserByPartnerAndUid($partnerId, $puserToDelete);

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
		$partnerId = KalturaGlobalData::getData("@TEST_PARTNER_ID@");
		$puserId = KalturaGlobalData::getData("TEST_PARTNER_USER_ID");
		KuserPeer::getKuserByPartnerAndUid($partnerId, $puserId);
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
	 * @dataProvider provideData 
	 */
	public function testAddAction($puser1, $puser2, $puser3)
	{
		$this->puser1 = $puser1; 
		$this->puser2 = $puser2;
		$this->puser3 = $puser3;
		
		print("Testing Admin KS adds\n");
		//Add with admin KS - admin / user existing / not existing
		print("Test1 KS [Admin], puser [null]\n");
		$this->addEntryTest(KalturaSessionType::ADMIN, null);
		
		print("Test2 KS [Admin], puser [test1] existent \n");
		$this->addEntryTest(KalturaSessionType::ADMIN, $puser1); // existing
		
		print("Test3 KS [Admin], puser [test3] non existent\n");
		$this->deleteKuser($this->puser3); //check that puser 3 is non existing
		$this->addEntryTest(KalturaSessionType::ADMIN, $puser3);
		
		print("Testing User KS adds\n");
		//Add with user KS - user / other user existing / not existing
		print("Test4 KS [User], puser [null]\n");
		$this->addEntryTest(KalturaSessionType::USER, null, true, array('SERVICE_FORBIDDEN'));
		
		print("Test5 KS [User], puser [test1] existing\n");
		$this->addEntryTest(KalturaSessionType::USER, $puser1); // existing
				
		print("Test6 KS [User], puser [test3] non existent\n");
		$this->deleteKuser($this->puser3); //check that puser 3 is non existing
		$this->addEntryTest(KalturaSessionType::USER, $puser3, true, array('SERVICE_FORBIDDEN'));
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
	 * @dataProvider provideData 
	 */
	public function testUpdateAction($puser1, $puser2, $puser3, $entryId)
	{
		$this->puser1 = $puser1; 
		$this->puser2 = $puser2;
		$this->puser3 = $puser3;
		$this->entryId = $entryId;
		
		print("\nUpdate tests started\n");
		$this->startSession(KalturaSessionType::ADMIN);
		
		$entry = $this->client->baseEntry->get($entryId);
		$updatedEntry = $this->createEntryForUpdate($entry);
		
		$this->originalPuser = $entry->userId;
						
		$originalKuser = $this->getKuserIdFromEntry($entryId);
		
		print ("original puser [$this->originalPuser], original kuser [$originalKuser]\n");
		$updatedEntry = $this->switchUsers($updatedEntry);
		
		print("Before update call\n");
		$result = $this->client->baseEntry->update($entryId, $updatedEntry);
		
		if(!$result instanceof KalturaMediaEntry)
		{
			$this->fail("Entry was not updated " . var_dump($result) . "\n");
		}
		
		$updatedEntry = $this->client->baseEntry->get($entryId);
		//var_dump($updatedEntry);
		
		print("updatedEntry->userId [$updatedEntry->userId], originalPuser [$this->originalPuser] \n");
		if($updatedEntry->userId != $this->originalPuser) // puser was update we now check if the kuser was changed as well
		{
			$newKuserId = $this->getKuserIdFromPuser($updatedEntry->userId);
			$entryKuserId = $this->getKuserIdFromEntry($entryId);
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
}