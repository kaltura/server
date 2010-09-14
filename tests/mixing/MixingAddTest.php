<?php
require_once("tests/bootstrapTests.php");

class MixingAddTest extends PHPUnit_Framework_TestCase 
{
	public function setUp() 
	{
	}
	
	public function tearDown() 
	{
	}
	
	public function testAdd()
	{
		$mixService = KalturaTestsHelpers::getServiceInitializedForAction("mixing", "add");
		
		$mixEntry = new KalturaMixEntry();
		$mixEntry->name = "Test Mix";
		$mixEntry->editorType = KalturaEditorType::SIMPLE;
		
		$newMixEntry = $mixService->addAction(clone $mixEntry);
		
		$this->assertEquals($mixEntry->name, $newMixEntry->name);
		$this->assertEquals($mixEntry->editorType, $newMixEntry->editorType);
		$this->assertEquals(100000, $newMixEntry->version);
		
		
		$dbEntry = entryPeer::retrieveByPK($newMixEntry->id);
		$this->assertEquals(KalturaEntryType::MIX, $dbEntry->getType());
		
		$xmlFilePath = myContentStorage::getFSContentRootPath() . myContentStorage::getGeneralEntityPath("entry".DIRECTORY_SEPARATOR."data", $dbEntry->getIntId(), $dbEntry->getId(), "100000.xml");
		$this->assertFileExists($xmlFilePath, "Mix xml was not created");
		
		$this->assertXmlStringEqualsXmlFile($xmlFilePath, $newMixEntry->dataContent);
	}
}