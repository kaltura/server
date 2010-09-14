<?php
require_once("tests/bootstrapTests.php");

class DeserializerTests extends PHPUnit_Framework_TestCase
{
	public function testObjectTypeIsNotNeededForBuildingActionArguments()
	{
		$requestParams = array(
			"userId" => 4,
			"entry:id" => "zxf3h87d",
			"entry:type" => KalturaEntryType::MEDIA_CLIP 
		);
		
		$actionArguments = array(
			new KalturaParamInfo("int", "userId"),
			new KalturaParamInfo("KalturaMediaEntry", "entry")
		);

		$deserializer = new KalturaRequestDeserializer($requestParams);
		$deserializer->groupParams();
		$paramsGrouped = $this->readAttribute($deserializer, "paramsGrouped");
		
		$this->assertEquals(2, count($paramsGrouped));
		$this->assertArrayHasKey("userId", $paramsGrouped);
		$this->assertArrayHasKey("entry", $paramsGrouped);
		
		$actionCallArguments = $deserializer->buildActionArguments($actionArguments);
		
		$this->assertEquals(2, count($actionCallArguments));
		$this->assertType("KalturaMediaEntry", $actionCallArguments[1]);
	}
	
	public function testObjectGrouping()
	{
		$requestParams = array(
			"user_id" => 4,
			"entry:id" => "zxf3h87d",
			"entry:type" => 4,
			"entry:objectType" => "KalturaMediaEntry" 
		);

		$deserializer = new KalturaRequestDeserializer($requestParams);
		$deserializer->groupParams();
		$requestParams = $this->readAttribute($deserializer, "paramsGrouped");
		
		$this->assertEquals(2, count($requestParams));
		$this->assertEquals("zxf3h87d", $requestParams["entry"]["id"]);
		$this->assertEquals(4, $requestParams["entry"]["type"]);
		$this->assertEquals("KalturaMediaEntry", $requestParams["entry"]["objectType"]);
	}
	
	public function testMissingSimpleActionArgument()
	{
		$requestParams = array();
		$actionArguments = array(
			new KalturaParamInfo("int", "user_id")
		);
		$deserializer = new KalturaRequestDeserializer($requestParams);
		try 
		{
			$deserializer->buildActionArguments($actionArguments);
		}
		catch(Exception $ex)
		{
			$dummyEx = new KalturaAPIException(KalturaErrors::MISSING_MANDATORY_PARAMETER, "user_id");
			$this->assertEquals($dummyEx->getMessage(), $ex->getMessage());
			return;
		}
		
		$this->fail("Expecting exception");
	}
	
	public function testInvalidSimpleType()
	{
		$this->markTestIncomplete("Not sure if this should be handled at runtime");
		$requestParams = array();
		$actionArguments = array(
			new KalturaParamInfo("integer", "user_id")
		);
		$deserializer = new KalturaRequestDeserializer($requestParams);
	}
	
	public function testObjectInitialization()
	{
		$requestParams = array(
			"entry:id" => "zxf3h87d",
			"entry:type" => 1,
			"entry:duration" => 453,
			"entry:objectType" => "KalturaMediaEntry"
		);
		
		$actionArguments = array(
			new KalturaParamInfo("KalturaMediaEntry", "entry")
		);
		
		$deserializer = new KalturaRequestDeserializer($requestParams);
		$deserializer->groupParams();
		$actionCallArguments = $deserializer->buildActionArguments($actionArguments);
		
		$this->assertEquals(1, count($actionCallArguments));
		$this->assertType("KalturaMediaEntry", $actionCallArguments[0]);
	}
	
	public function testSimpleCasting()
	{
		$mockClassCode = '
		class TypeCastingMock extends KalturaObject {
			/**
			 * @var string
			 */
			public $dummyString;
			
			/**
			 * @var int
			 */
			public $dummyInteger;
			
			/**
			 * 
			 * @var bool
			 */
			public $dummyBoolean;
			
			/**
			 * 
			 * @var float
			 */
			public $dummyFloat;
		}
		';
		
		
		eval($mockClassCode);
		
		$requestParams = array(
			"mock:objectType" => "TypeCastingMock",
			"mock:dummyString" => "text 123",
			"mock:dummyInteger" => "466",
			"mock:dummyBoolean" => "true",
			"mock:dummyFloat" => "147.94"
		);
		
		$actionArguments = array(
			new KalturaParamInfo("TypeCastingMock", "mock")
		);
		
		$deserializer = new KalturaRequestDeserializer($requestParams);
		$deserializer->groupParams();
		$actionCallArguments = $deserializer->buildActionArguments($actionArguments);
		
		$mockArgument = $actionCallArguments[0];
		$this->assertEquals("text 123", $mockArgument->dummyString);
		$this->assertEquals(466, $mockArgument->dummyInteger);
		$this->assertEquals(true, $mockArgument->dummyBoolean);
		$this->assertEquals(147.94, $mockArgument->dummyFloat);
	}
	
	public function testThatMissingPropertiesAreNull()
	{
		// some of the object properties doesn't exists, and should be deserialized as null
		$requestParams = array(
			"entry:objectType" => "KalturaMediaEntry",
			"entry:id" => "1q2w3e4r",
		);
		
		$actionArguments = array(
			new KalturaParamInfo("KalturaMediaEntry", "entry")
		);
		
		$deserializer = new KalturaRequestDeserializer($requestParams);
		$deserializer->groupParams();
		$actionCallArguments = $deserializer->buildActionArguments($actionArguments);

		$mediaEntry = $actionCallArguments[0];
		
		// assert some of the properties
		$this->assertNull($mediaEntry->status);
		$this->assertNull($mediaEntry->name);
		$this->assertNull($mediaEntry->mediaType);
	}
	
	public function testThatEmptyPropertiesAreEmptyAndNotNull()
	{
		// some of the request parameters exists, but empty
		$requestParams = array(
			"entry:objectType" => "KalturaMediaEntry",
			"entry:id" => "1234",
			"entry:name" => "",
			"entry:partnerId" => ""
		);
		
		$actionArguments = array(
			new KalturaParamInfo("KalturaMediaEntry", "entry")
		);
		
		$deserializer = new KalturaRequestDeserializer($requestParams);
		$deserializer->groupParams();
		$actionCallArguments = $deserializer->buildActionArguments($actionArguments);

		$mediaEntry = $actionCallArguments[0];
		
		// assert some of the properties
		$this->assertNotNull($mediaEntry->name);
		$this->assertNotNull($mediaEntry->partnerId);
	}
	
	public function testThatInvalidEnumValueThrowsException1()
	{
		// test using empty value
		$requestParams = array(
			"entry:objectType" => "KalturaMediaEntry",
			"entry:mediaType" => "",
		);
		
		$actionArguments = array(
			new KalturaParamInfo("KalturaMediaEntry", "entry")
		);
		
		$deserializer = new KalturaRequestDeserializer($requestParams);
		$deserializer->groupParams();
		try
		{
			$deserializer->buildActionArguments($actionArguments);			
		}
		catch(Exception $ex)
		{
			$dummyEx = new KalturaAPIException(KalturaErrors::INVALID_ENUM_VALUE, "", "mediaType", "KalturaMediaType");
			$this->assertEquals($dummyEx->getMessage(), $ex->getMessage());
			return;
		}
		
		$this->fail("Expecting an exception");
	}
	
	public function testThatInvalidEnumValueThrowsException2()
	{
		// test using invalid value
		$requestParams = array(
			"entry:objectType" => "KalturaMediaEntry",
			"entry:mediaType" => "9",
		);
		
		$actionArguments = array(
			new KalturaParamInfo("KalturaMediaEntry", "entry")
		);
		
		$deserializer = new KalturaRequestDeserializer($requestParams);
		$deserializer->groupParams();
		try
		{
			$deserializer->buildActionArguments($actionArguments);			
		}
		catch(Exception $ex)
		{
			$dummyEx = new KalturaAPIException(KalturaErrors::INVALID_ENUM_VALUE, "9", "mediaType", "KalturaMediaType");
			$this->assertEquals($dummyEx->getMessage(), $ex->getMessage());
			return;
		}
		
		$this->fail("Expecting an exception");
	}
}

?>