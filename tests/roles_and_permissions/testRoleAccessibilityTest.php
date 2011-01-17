<?php

require_once (dirname(__FILE__). '/../unit_test/bootstrap.php');

class testRoleAccessibilityTest extends KalturaApiUnitTestCase
{
	private $fullAccessClient;
	private $minimalAccessClient;
	
	private $addedRoleIds = array();
	private $isCleanClient = false; 
	private $addedPermissionsId = array();
	
	const TEST_PARTNER_SECRET = '1af9794120591b09a1a5f65931e2dc4b';//'NjE3MjA4NjliYmQ4MWJlMjY0MmQwYWU4YWNkODA1ZjdjN2I2OTUyOHwxOTg7MTk4OzEyOTUyNjkwNzk7MjsxMjk1MTgyNjc5Ljg5MTM7Ozs7';
	const TEST_PARTNER_ID = 193;
	const TEST_SERVER = 'newkaldev.kaltura.dev'; 
	const MAXIMAL_ACCESS_ROLE_ID = 0;
	const MINIMAL_ACCESS_ROLE_ID = 0;
	const ADMIN_PARTNER = -2;
	const ADMIN_USER = 0;
	const ADMIN_SECRET = 90210;
		
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp();
		$this->client = $this->getClient(self::ADMIN_PARTNER, self::ADMIN_SECRET, self::TEST_SERVER);
		if(!$this->client)
		{
			//TODO: add a new client and get him from the DB
			//TODO: add this new id to the config file
		}
		
		$allPermissionsItems = array();
		
	//	$allPermissionsItems = $this->client->permissionItem->listAction();
	//	var_dump($allPermissionsItems);
		
		$allPermissionsItemsIds  = array();
		foreach ($allPermissionsItems as $permissionItem)
		{
			//TODO: create the ids list
			$allPermissionsItemsIds[] = $permissionItem->id; 
		}
		
//		$minimalPermission = $this->addPermission("MINIMAL", "MINIMAL", null);
	//	$maximalPermission = $this->addPermission("MAXIMAL", "MAXIMAL", $allPermissionsItemsIds);
		
		
		$this->client = $this->getClient(self::TEST_PARTNER_ID, self::TEST_PARTNER_SECRET, self::TEST_SERVER);
		
//		$minimalRole = $this->addRole("Minimal", "Minimal", null);
///		$maximalRole = $this->addRole("Maximal", "Maximal", "MAXIMAL");
		
//		$this->addedRoleIds = array();
//		$this->dummyPartner = PartnerPeer::retrieveByPK(self::TEST_PARTNER_ID);
//		$this->assertEquals(self::TEST_PARTNER_ID, $this->dummyPartner->getId());
//		UserRolePeer::clearInstancePool();
//		PermissionPeer::clearInstancePool();
//		PermissionItemPeer::clearInstancePool();
//		kuserPeer::clearInstancePool();
//		PartnerPeer::clearInstancePool();
	}

	/**
	 * 
	 * Checks if the exception is valid (not a SERVICE_FORBIDDEN exception)
	 * @param Exception $e
	 */
	private function checkException(Exception $e)
	{
		if($e->getCode() != 'SERVICE_FORBIDDEN')
		{
			return true;
		}
		
		return false;
		
	}
	
	/**
	 * Cleans up the environment after running a test.
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
//		UserRolePeer::clearInstancePool();
//		PermissionPeer::clearInstancePool();
//		PermissionItemPeer::clearInstancePool();
//		kuserPeer::clearInstancePool();
//		PartnerPeer::clearInstancePool();
		
		$this->client = null;
//		UserRolePeer::setUseCriteriaFilter(false);
		
		
		//Clean role ids
		foreach ($this->addedRoleIds as $id) {
			try
			{
				$obj = UserRolePeer::retrieveByPK($id);
				if ($obj) {
					$obj->delete();
				}
			}
			catch (PropelException $e) {
				print("Unable to delete role id = {$id} \n" );
			}
		}
		
		//Clean added permissions ids
		foreach ($this->addedPermissionsId as $id) {
			try
			{
				
				$obj = PermissionPeer::retrieveByPK($id);
				if ($obj) {
					$obj->delete();
				}
			}
			catch (PropelException $e) {
				print("Unable to delete role id = {$id} \n" );
			}
		}
		
//		UserRolePeer::setUseCriteriaFilter(true);
		$this->addedRoleIds = array();
		parent::tearDown ();
	}

	/**
	 * Test accessibility for the given partner, secret and server
	 * @dataProvider accessibilityProvider
	 */
	public function testAccessibility($partnerId = '103', $secret = '1af9794120591b09a1a5f65931e2dc4b', $configServiceUrl = null)
	{
		print("Start test");
		
		$client = $this->getClient($partnerId, $secret, $configServiceUrl);
		
		$serviceMap = KalturaServicesMap::getMap();
		$services = array_keys($serviceMap);
		foreach($services as $service)
		{
			$serviceReflector = new KalturaServiceReflector($service);
			$actions = array_keys($serviceReflector->getActions());

			$serviceAsArray = explode('_', $service);
			$serviceName = end($serviceAsArray);
			
			foreach($actions as $action)
			{
				// params
				$actionParams = $serviceReflector->getActionParams($action);
				$params = array();
				foreach ($actionParams as $actionParam)
				{
					$params[] = $actionParam->getDefaultValue();
				}

				try 
				{
					$actionName = $action;
					if($action == 'list')
					{
						$actionName  = $action . "Action";
					}

					call_user_func_array(array($client->$serviceName, $actionName), $params);
					
				}
				catch(Exception $ex)
				{
					//TODO: add compare function to base
					$this->compareOnField("{$serviceName}::{$actionName}", $ex->getCode(), 'SERVICE_FORBIDDEN');
				}
			}
		}
	}
	
	/**
	 * 
	 * Provides the data to the accessibility test
	 */
	public function accessibilityProvider()
	{
		//TODO: get func name
		print("Test Name is: {$this->name}\n" );
		
		$rawInputs = $this->provider(dirname(__FILE__) . "/testsData/testAccessibility.data");
		
		$inputs = array();
		$index = -1;
		foreach ($rawInputs as $rawInput)
		{
			$index++;
			$inputs[] = array();
			$inputs[$index][] = $rawInputs[$index][0]->additionalData["partnerId"];	
			$inputs[$index][] = $rawInputs[$index][0]->additionalData["secret"];
			$inputs[$index][] = $rawInputs[$index][0]->additionalData["serviceUrl"];
		}
		
		return $inputs;
	}

	/**
	 * 
	 * Creates a new user role given the role name and permissions 
	 * @param unknown_type $roleName
	 * @param unknown_type $permissionsNames
	 */
	private function createUserRole($roleName, $roleDesc,$permissionsNames)
	{
		$userRole = new KalturaUserRole();
		$userRole->name = $roleName;
		$userRole->permissionNames = $permissionsNames;
		$userRole->description = $roleDesc;
		$userRole->status = KalturaUserRoleStatus::ACTIVE;
		return $userRole;
	}
	
	/**
	 * 
	 * Creates a new permission item given the name, desc and permissions items ids
	 * @param unknown_type $name
	 * @param unknown_type $desc
	 * @param unknown_type $permissionsItemsIds
	 */
	private function createPermission($name, $desc, $permissionsItemsIds)
	{
		$permission = new KalturaPermission();
		$permission->name = $name;
		$permission->description = $desc;
		$permission->permissionItemsIds = $permissionsItemsIds;
		$permission->status = KalturaPermissionStatus::ACTIVE;
		$permission->type = KalturaPermissionType::API_ACCESS;
		
		return $permission;
	}

	/**
	 * 
	 * Creates and adds a new role to the DB 
	 * @param unknown_type $name
	 * @param unknown_type $description
	 * @param unknown_type $permissionsNames
	 */
	private function addRole($name, $description, $permissionsNames)
	{
		$newRole = $this->createUserRole($name, $description, $permissionsNames);
		$addedRole = $this->addRoleWrap($newRole);
		
		return $addedRole;
	}
	
	/**
	 * 
	 * Creates and adds a new permission to the DB
	 * @param unknown_type $name
	 * @param unknown_type $description
	 * @param unknown_type $permissionsItemsIds
	 */
	private function addPermission($name, $description, $permissionsItemsIds)
	{
		$newPermission = $this->createPermission($name, $description, $permissionsItemsIds);
		$newPermissionId = $this->addPermissionWrap($newPermission);
		
		return $newPermissionId;
	}
	
	/**
	 * 
	 * Adds a role to the DB using .
	 * @param KalturaUserRole $role
	 */
	private function addRoleWrap(KalturaUserRole $role)
	{
		$addedRole = $this->client->userRole->add($role);
		$this->addedRoleIds[$role->name] = $addedRole->id;
		return $addedRole;
	}
		
	/**
	 * 
	 * Adds a permissions to the DB.
	 * @param KalturaPermission $permission
	 */
	private function addPermissionWrap(KalturaPermission $permission)
	{
		$addedPermission = $this->client->permission->add($permission);
		$this->addedPermissionsId[$permission->name] = $addedPermission->id;
		return $addedPermission;
	}
	
	/**
	 * Scans the file system and loads the description of the services to an array
	 *
	 */
	protected function loadServicesInfo()
	{
		$serviceMap = KalturaServicesMap::getMap();
		$services = array_keys($serviceMap);
		foreach($services as $service)
		{
			$serviceReflector = new KalturaServiceReflector($service);
			$actions = array_keys($serviceReflector->getActions());

			foreach($actions as $action)
			{
				// params
				$actionParams = $serviceReflector->getActionParams($action);
				foreach ($actionParams as $actionParam)
				{
					$actionParam = null;
				}

				try {
					call_user_func_array($service->$action, $actionParams);
				}
				catch(Exception $e)
				{
					var_dump($e);
				}
			}
		}
	}
}