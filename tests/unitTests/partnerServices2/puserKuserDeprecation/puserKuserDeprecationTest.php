<?php

//Require server bootstrap
require_once(dirname(__FILE__) . '/../../../bootstrap/bootstrapServer.php');

/**
 * 
 * Represents the puser_kuser table deprecation tests
 * @author Roni
 *
 */
class puserKuserDeprecationTest extends KalturaServerTestCase
{
	/**
	 * 
	 * Creates a new puser kuser deprecarion Test case
	 * @param string $name
	 * @param array<unknown_type> $data
	 * @param string $dataName
	 */
	public function __construct($name = "puserKuserDeprecationTest", array $data = array(), $dataName ="Default data")
	{
		parent::__construct($name, $data, $dataName);
	}
	
	/**
	 * 
	 * Tests all ps2 services for the given: TODO: add parameters here
	 * @dataProvider providerTestAllPS2Services
	 */
	public function testAllPS2Services(array $partnerData)
	{
		//Go over all SP2 services and see that we get the result (run after consulidation)
		//As all the ps2 services gets a puser_kuser as parameter.
		$ps2ActionsFolderPath = "C:/opt/kaltura/app/alpha/apps/kaltura/modules/partnerservices2/actions";
			
		chdir($ps2ActionsFolderPath);
		$ks = PS2Helper::getKs($partnerData["secret"], $partnerData["userId"], KalturaSessionType::ADMIN, $partnerData["partnerId"]);
		print ($ks);
		
		return;
		foreach (glob("*.php") as $ps2FileName)
		{			
			//now get just the PS2 service name
			$ps2ActionArray = explode(".", $ps2FileName);
			
			
			$actionName = $ps2ActionArray[0];
			$actionName = str_replace("Action", "", $actionName );
			
			$params = array (
			'user_id' => 1, 
			"partner_id" =>$partnerData["partnerId"],
			"secret" =>$partnerData["secret"],
			);
			
			var_dump($partnerData["partnerId"]);
						
			$ks = "ZjA3N2I1ZTUxZWM0ZDg2MTQ3M2Y1NTg4YmUzNWQ1NWFhYTFmZTM1NXwtMjstMjsxMzAwMzgyNjcyOzI7MTMwMDI5NjI3Mi43NTIxO2FkbWluQGthbHR1cmEuY29tOyo7Ow=="; 
			
			$result = PS2Helper::doHttpRequest($actionName, $params, $ks);
			
			print(var_dump($result) . "\n");
			print($actionName . "\n");
			
		}
	}
	
	/**
	 * 
	 * Provides the testAllPS2Services function with it's parameters
	 */
	public function providerTestAllPS2Services()
	{
		$inputs = $this->provider("testAllPS2Services");
		$inputsForTest = array();
		foreach($inputs as $input)
		{
			$additionalData = $input[0]->getAdditionalData();
			$partnerDataArray = array("partnerId" => $additionalData["partnerId"],
									  "secret" => $additionalData["secret"],
									   "serviceUrl" => $additionalData["serviceUrl"],
									   "isAdmin" => $additionalData["isAdmin"],
									   "userId" => $additionalData["userId"],
									  );
		     $inputsForTest[] = array($partnerDataArray);
		}
		
		return $inputsForTest;
	}
}