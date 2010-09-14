<?php
require_once("tests/bootstrapTests.php");

class SessionRestrictionTests extends PHPUnit_Framework_TestCase 
{
	public function testSessionRestriction()
	{
		$restriction = new sessionRestriction();
		$restriction->setAccessControl(new accessControl());
		
		$entryId = "abc";
		$ks = KalturaTestsHelpers::getNormalKs(null, null, null, "view:".$entryId);
		$ks = ks::fromSecureString($ks);
		$restriction->getAccessControlScope()->setEntryId($entryId);
		$restriction->getAccessControlScope()->setKs($ks);
		$this->assertTrue($restriction->isValid());
		
		$restriction->getAccessControlScope()->setEntryId("xyz");
		$this->assertFalse($restriction->isValid());
		
		$restriction = new sessionRestriction();
		$restriction->setAccessControl(new accessControl());
		$ks = KalturaTestsHelpers::getNormalKs(null, null, null, "edit:".$entryId);
		$ks = ks::fromSecureString($ks);
		$restriction->getAccessControlScope()->setEntryId($entryId);
		$restriction->getAccessControlScope()->setKs($ks);
		$this->assertFalse($restriction->isValid());
	}
}
?>