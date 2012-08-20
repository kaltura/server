<?php

require_once(dirname(__FILE__) . '/../../bootstrap.php');

/**
 * accessControlProfile service test case.
 */
class AccessControlProfileServiceAddTest extends KalturaApiTestCase
{
	/**
	 * Tests accessControlProfile->add action
	 * @param KalturaAccessControlProfile $accessControlProfile 
	 * @return KalturaAccessControlProfile
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaAccessControlProfile $accessControlProfile)
	{
		$resultObject = $this->client->accessControlProfile->add($accessControlProfile);
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaAccessControlProfile', $resultObject);
		else
			$this->assertType('KalturaAccessControlProfile', $resultObject);
		$this->assertNotNull($resultObject->id);
		
		$this->statusMessage = "Id: $resultObject->id";
		return $resultObject->id;
	}	
}

