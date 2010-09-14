<?php
require_once("tests/bootstrapTests.php");

class MultiRequestTests extends PHPUnit_Framework_TestCase 
{
    function testBasicMultiRequest()
    {
		$ks = KalturaTestsHelpers::getNormalKs();
		
        $_GET["service"] = "multirequest";
        $_POST["format"] = KalturaResponseType::RESPONSE_TYPE_PHP;
        $_POST["ks"] = $ks;
        
        $_POST["1:service"] = "media";
        $_POST["1:action"] = "addFromUrl";
        $_POST["1:mediaEntry:name"] = "Entry Name";
        $_POST["1:mediaEntry:mediaType"] = KalturaMediaType::VIDEO;
        $_POST["1:url"] = "Url";
        
        $_POST["2:service"] = "media";
        $_POST["2:action"] = "addFromUrl";
        $_POST["2:mediaEntry:name"] = "Entry Name";
        $_POST["2:mediaEntry:mediaType"] = KalturaMediaType::VIDEO;
        $_POST["2:url"] = "Url";
        
        $controller = KalturaFrontController::getInstance();
        $result = $controller->handleMultiRequest();
        
        $this->assertEquals(2, count($result), "Number of results.");
        $this->assertNotNull($result[1]->id);
        $this->assertNotNull($result[2]->id);
        $this->assertNotEquals($result[1]->id, $result[2]->id, "Same entry was returned.");
        
        $mediaService = KalturaTestsHelpers::getServiceInitializedForAction("media", "get");
        $mediaEntry1 = $mediaService->getAction($result[1]->id);
        $mediaEntry2 = $mediaService->getAction($result[2]->id);
        $this->assertEquals($mediaEntry1->id, $result[1]->id);
        $this->assertEquals($mediaEntry2->id, $result[2]->id);
    }
}

?>