<?php
require_once("tests/bootstrapTests.php");

class BulkUploadTests extends PHPUnit_Framework_TestCase 
{
	private $createdBulkUploads;
	
	public function setUp() 
	{
		$this->createdBulkUploads = array();
	}
	
	public function tearDown() 
	{
		parent::tearDown();
		
		foreach($this->createdBulkUploads as $batchJobId)
		{
			$batchJob = BatchJobPeer::retrieveByPK($batchJobId);
			
			$syncKey = $batchJob->getSyncKey(BatchJob::FILE_SYNC_BATCHJOB_SUB_TYPE_BULKUPLOADCSV);
			kFileSyncUtils::deleteSyncFileForKey($syncKey);
				
			if ($batchJob)
				$batchJob->delete();
		}
		$this->createdBulkUploads = array();
	}
	
	public function testAdd()
	{
		$bulkUploadService = KalturaTestsHelpers::getServiceInitializedForAction("bulkupload", "add", null, null, KalturaTestsHelpers::getAdminKs());

		$uniqueId = substr(base_convert(md5(uniqid(rand(), true)), 16, 36), 1, 20);
		$ext = "csv";
		
		$token = "${ksUnique}_$uniqueId.$ext";
		
		$uploadPath  = myUploadUtils::getUploadPathAndUrl($token, "", null, "csv");
		$fullPath = $uploadPath[0];
		
		$currentPath = pathinfo(__FILE__, PATHINFO_DIRNAME);
		copy("$currentPath/../files/sample.csv", $fullPath);
		
		$fileData = array(
			'name' => 'sample.csv',
			'tmp_name' => $fullPath,
			'error' => null,
			'size' => filesize($fullPath)
		);
		
		$newBulkUpload = $bulkUploadService->addAction(ConversionProfile::CONVERSION_PROFILE_UNKNOWN, $fileData);
		$this->createdBulkUploads[] = $newBulkUpload->id;
		
		$batchJob = BatchJobPeer::retrieveByPK($newBulkUpload->id);
		
		$syncKey = $batchJob->getSyncKey(BatchJob::FILE_SYNC_BATCHJOB_SUB_TYPE_BULKUPLOADCSV);
		if(!kFileSyncUtils::file_exists($syncKey))
			$this->fail('File not saved on the disc');
			
		return $newBulkUpload;
	}
	
	public function testGet()
	{
		$bulkUpload = $this->testAdd();
		
		$bulkUploadService = KalturaTestsHelpers::getServiceInitializedForAction("bulkupload", "get", null, null, KalturaTestsHelpers::getAdminKs());
		
		$getBulkUpload = $bulkUploadService->getAction($bulkUpload->id);
		
		$this->assertBulkUpload($bulkUpload, $getBulkUpload);
	}
	
	public function testList()
	{
		$bulkUploadService = KalturaTestsHelpers::getServiceInitializedForAction("bulkupload", "list", null, null, KalturaTestsHelpers::getAdminKs());

		$bulkUploadIds = array();
		for($i = 0; $i < 5; $i++)
		{
			$newBulkUpload = $this->testAdd();
			$bulkUploadIds[$newBulkUpload->id] = $newBulkUpload;
		}
		
		$listResult = $bulkUploadService->listAction();
		
		$newIds = array();
		foreach($listResult->objects as $bulkUpload)
			$newIds[$bulkUpload->id] = $bulkUpload;
			
		foreach($bulkUploadIds as $bulkUploadId => $bulkUpload)
			$this->assertArrayHasKey($bulkUploadId, $newIds);
	}
	
	private function assertBulkUpload($expectedBulkUpload, $actualBulkUpload)
	{
		$this->assertEquals($expectedBulkUpload->id, $actualBulkUpload->id);
		$this->assertEquals($expectedBulkUpload->uploadedBy, $actualBulkUpload->uploadedBy);
		$this->assertEquals($expectedBulkUpload->uploadedOn, $actualBulkUpload->uploadedOn);
		$this->assertEquals($expectedBulkUpload->numOfEntries, $actualBulkUpload->numOfEntries);
		$this->assertEquals($expectedBulkUpload->status, $actualBulkUpload->status);
		$this->assertEquals($expectedBulkUpload->logFileUrl, $actualBulkUpload->logFileUrl);
		$this->assertEquals($expectedBulkUpload->csvFileUrl, $actualBulkUpload->csvFileUrl);
		$this->assertEquals($expectedBulkUpload->error, $actualBulkUpload->error);
	}
}

?>