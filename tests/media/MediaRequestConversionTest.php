<?php
require_once("tests/bootstrapTests.php");

class MediaRequestConversionTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var KalturaMediaEntry
	 */
	private $_mediaEntry;
	
	/**
	 * @var string
	 */
	private $_archiveFilePath;
	
	/**
	 * @var int
	 */
	private $_createdJobId;
	 
	public function setUp ()
	{
		parent::setUp();
		
		$this->_mediaEntry = MediaTestsHelpers::createDummyEntry();
		
		$this->createDummyArchiveFile();
	}
	
	public function tearDown ()
	{
		parent::tearDown();
		
		$this->deleteDummyArchiveFile();
		
		$batchJob = BatchJobPeer::retrieveByPK($this->_createdJobId);
		if ($batchJob)
			$batchJob->delete();
	}
	
	public function __construct ()
	{
	}
	
	public function testRequestConversion()
	{
		$mediaService = KalturaTestsHelpers::getServiceInitializedForAction("media", "requestConversion");
		
		$this->_createdJobId = $mediaService->requestConversionAction($this->_mediaEntry->id, "mov");
		self::assertGreaterThan(0, $this->_createdJobId);
		
		$job = BatchJobPeer::retrieveByPK($this->_createdJobId);
		
		self::assertEquals(BatchJob::BATCHJOB_TYPE_DOWNLOAD, $job->getJobType());
		self::assertEquals(BatchJob::BATCHJOB_STATUS_PROCESSING, $job->getStatus());
		self::assertEquals("Queued", $job->getMessage());
		self::assertEquals("Queued, waiting to run", $job->getDescription());
		self::assertEquals(KalturaTestsHelpers::getPartner()->getId(), $job->getPartnerId());
		self::assertEquals($this->_mediaEntry->id, $job->getEntryId());
		
		// assert the serialized data 
		$data = json_decode($job->getData());
		self::assertEquals($this->_mediaEntry->userId, $data->puserId);
		self::assertEquals($this->_mediaEntry->id, $data->entryId);
		$entry = entryPeer::retrieveByPK($this->_mediaEntry->id);
		self::assertEquals($entry->getIntId(), $data->entryIntId);
		self::assertEquals("mov", $data->fileFormat);
		self::assertFileExists($data->archivedFile);
	}
	
	public function testErrorWhenRequestConversionForMissingArchiveFile()
	{
		$mediaService = KalturaTestsHelpers::getServiceInitializedForAction("media", "requestConversion");
		
		$this->deleteDummyArchiveFile();
		
		try
		{
			$this->_createdJobId = $mediaService->requestConversionAction($this->_mediaEntry->id, "mov");
			$this->fail("Excepting exception");
		}
		catch(Exception $ex)
		{
			return;
		}
		$this->fail("Excepting exception");
	}
	
	private function createDummyArchiveFile()
	{
		// put dummy archive file
		$entry = entryPeer::retrieveByPK($this->_mediaEntry->id);
		$archiveDir = DummyKConversionClient::getArchiveDirAccessor();
		$this->_archiveFilePath = $archiveDir . myContentStorage::dirForId($entry->getIntId(), $entry->getId()) . ".flv";
		
		myContentStorage::moveFile(KalturaTestsHelpers::getDummyFlvFilePath(), $this->_archiveFilePath, true, true);		
	}
	
	private function deleteDummyArchiveFile()
	{
		// delete dummy archive file
		if (file_exists($this->_archiveFilePath))
			unlink($this->_archiveFilePath);
	}
}

