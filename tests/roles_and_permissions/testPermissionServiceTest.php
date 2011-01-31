<?php

require_once(dirname(__FILE__) . "/../bootstrap/bootstrapClient.php");

class testPermissionServiceTest extends KalturaApiUnitTestCase
{
	/**
	 * 
	 * @dataProvider testPermissionServiceAddActionProvider
	 * @return KalturaPermission - the new permission item
	 */
	public function testPermissionServiceAddAction(KalturaPermission $permission, KalturaClient $client)
	{
		$client->permission->add($permission);
		return array($permission, $client);
	}
	
	/**
	 * @param KalturaPermission $permission
	 * @param KalturaClient $client
	 * @depends testPermissionServiceAddAction
	 * @return array(KalturaPermission, KalturaClient) - the new permission item
	 * 
	 */
	public function testPermissionServiceGetAction(KalturaPermission $permission, KalturaClient $client)
	{
		$actualPermission = $client->permission->get($permission->name);
		$validErrorFields = array();
		$this->CompareAPIObjects($permission, $actualPermission, $validErrorFields);
		
		return array($permission, $client);
	}
	
	/**
	 * @param KalturaPermission $permission
	 * @param KalturaClient $client
	 * @depends testPermissionServiceGetAction
	 * @return array(KalturaPermission, KalturaClient) - the new permission item
	 * 
	 */
	public function testPermissionServiceUpdateAction(KalturaPermission $permission, KalturaClient $client, $newPermission)
	{
		$result = $client->permission->update($permission->name, $newPermission);
		
		$actualPermission = $client->permission->get($permission->name);
		 
		$validErrorFields = array();
		$this->CompareAPIObjects($newPermission, $actualPermission, $validErrorFields);
		
		return array($newPermission, $client);
	}
	
	/**
	 * @param KalturaPermission $permission
	 * @param KalturaClient $client
	 * @depends testPermissionServiceUpdateAction
	 * @return array(KalturaPermission, KalturaClient) - the new permission item
	 * 
	 */
	public function testPermissionServiceDeleteAction(KalturaPermission $permission, KalturaClient $client)
	{
		$result = $client->permission->delete($permission->name);
		
		$actualPermission=$client->permission->get($permission->name);
		$validErrorFields = array();
		$this->CompareAPIObjects($permission, $actualPermission, $validErrorFields);
		
		return array($permission, $client);
	}
	
	/**
	 * 
	 * The add method test data provider 
	 */
	public function testPermissionServiceAddActionProvider()
	{
		//return the permission item
		$inputs = $this->provider(dirname(__FILE__). "/testsData/testPermissionServiceTest.data");
		
		foreach ($inputs as $input)
		{
			$partnerId = $input[1]["partnerId"];
			$secret = $input[1]["secret"];
			$configServiceUrl = $input[1]["serviceUrl"];
			$isAdmin = $input[1]["isAdmin"];
			 		
			$client = $this->getClient($partnerId, $secret, $configServiceUrl, $isAdmin);
			$input[1] = $client;
		}
		
		return $inputs;
	}
}