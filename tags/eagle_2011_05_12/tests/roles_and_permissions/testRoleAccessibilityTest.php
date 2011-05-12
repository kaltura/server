<?php

require_once (dirname(__FILE__). '/../bootstrap/bootstrapServer.php');

class testRoleAccessibilityTest extends KalturaApiTestCase
{
	private static $adminClient;

	const TEST_SERVER = 'www.kaltura.co.cc';
//	const TEST_SERVER = 'local.trunk';  
	const ADMIN_PARTNER = -2;
	const ADMIN_USER = 0;
	const ADMIN_SECRET = "35dc0d295d874788332a813066a77b88";
//	const ADMIN_SECRET = "adminconsoleadminsecret";

	private $enumTypes = array(
	'KalturaControlPanelCommandStatus',
	'KalturaControlPanelCommandStatus',
	'KalturaStorageProfileStatus',
	'KalturaPartnerStatus',
	'KalturaBatchJobType',
	'KalturaEntryType',
	'KalturaSourceType',
	'KalturaNotificationType',
	'KalturaPlaylistType',
	'KalturaReportType',
	'KalturaMetadataObjectType'
	);
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp();
		
		self::$adminClient = $this->getClient(self::ADMIN_PARTNER, self::ADMIN_SECRET, self::TEST_SERVER, 1);
		
		if(!$this->client)
		{
			//TODO: add a new client and get him from the DB
			//TODO: add this new id to the config file
		}
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
		parent::tearDown ();
	}

	/**
	 * Test accessibility for the given partner, secret and server
	 * @dataProvider accessibilitySuccessProvider
	 */
	public function testAccessibilitySuccess($partnerId, $secret, $configServiceUrl, $isAdmin, $userId)
	{
		$testedClient = $this->getClient($partnerId, $secret, $configServiceUrl, $isAdmin, $userId);
				
		$permissions = $this->getPermissionsItems($testedClient, $userId, $partnerId, $isAdmin, $configServiceUrl);
		$alwaysAllowedActions = $this->getAlwaysAllowedActions($partnerId);
		
		$serviceMap = KalturaServicesMap::getMap();
		$services = array_keys($serviceMap);
		
//		$this->testSingleService("batchcontrol", $testedClient, $permissions, $alwaysAllowedActions);
				
		foreach($services as $service)
		{
			//We skip the session service
			if($service == "session")
			{
				continue;
			}
			
			$serviceReflector = new KalturaServiceReflector($service);
			$actions = array_keys($serviceReflector->getActions());
			
			$serviceName = $serviceReflector->getServiceName();

			foreach($actions as $action)
			{
				$params = $this->getActionParams($action, $serviceReflector);

				if($action == "list")
				{
					$action .= "Action";
				}
				
				try
				{
					if(method_exists($testedClient->$serviceName, $action))
					{
						call_user_func_array(array($testedClient->$serviceName, $action), $params);				
						//TODO: Handle non exception cases
						$this->compareServiceAction($permissions, $alwaysAllowedActions, $serviceName, $action);
					}
					else
					{
						//TODO: handle method doesn't exists...						
					}
				}
				catch(Exception $ex)
				{
					//Check if the service / action is found in the user permissions
					$this->compareServiceAction($permissions, $alwaysAllowedActions, $serviceName, $action, $ex);
				}
			}
		}
	}
	
	/**
	 * 
	 * Gets all the needed parameters for the given action
	 * @param unknown_type $action
	 */
	private function getActionParams($action, $serviceReflector)
	{
		$params = array();

		//Get the action parameters
		$actionParams = $serviceReflector->getActionParams($action);
		
		//for each action parameter
		foreach ($actionParams as $actionParam)
		{
			$actionName = $actionParam->getName();
			$typeName = $actionParam->getType();
			$typeReflector = $actionParam->getTypeReflector();
			
			//Class is abstract 
			if($typeName == "KalturaPermissionItem")
			{
				$params[] = new KalturaApiActionPermissionItem();
				continue;
			}
			
			//Checks if the parameter
			if($actionParam->isComplexType())
			{
				//Is Array
				if($actionParam->isArray())
				{
					$params[] = array();
				}
				else //it is a complex and not array type
				{
					if($typeReflector != null)
					{
						if($typeName == "fileData" || $typeName == "file")
						{
							$params[] = array( 	'name' => "",
												'tmp_name' => "",
												'filename' => "");
						}
						elseif($typeReflector->isStringEnum() || $typeReflector->isDynamicEnum())
						{
							$contants = $typeReflector->getConstants();
							$enumValue = $contants[0]->getDefaultValue();
							$params[] = (string)$enumValue;
						}
						elseif($typeReflector->isEnum())
						{
							$contants = $typeReflector->getConstants();
							$params[] = (int)$contants[0]->getDefaultValue();
						}
						elseif(!$typeReflector->isAbstract())
						{
							//TODO: fix bug in the type reflector where those types aren't considered as ENUM
							if(in_array($typeName, $this->enumTypes))
							{
								if($typeName == 'KalturaPlaylistType')
								{
									$params[] = 3;
								}
								else
								{
									$params[] = 1;
								}
							}
							else 
							{
								$instance = $typeReflector->getInstance();
								
								if(!isset($instance->name))
								{
									$instance->name = "0";
								}
								
								if(!isset($instance->id))
								{
									$instance->id = 0;
								}
								
								$params[] = $instance;
							}
						}
						else // handle all abstract classes and not just the permission item
						{
							//TODO: handle abstract classes
							$params[] = new KalturaApiActionPermissionItem();								
						}
					}
					else //type == null
					{
						$params[] = 0; 
					}
				}
			}
			else //For int, bool, string, float (SimpleType)
			{
				$params[] = 0;
			}
		}
					
		return $params;
	}
	
	private function testSingleService($service, $testedClient, $permissions, $alwaysAllowedActions)
	{
		$serviceReflector = new KalturaServiceReflector($service);
		$actions = array_keys($serviceReflector->getActions());
		
		$serviceName = $serviceReflector->getServiceName();

		foreach($actions as $action)
		{				
			// Params
			$actionParams = $serviceReflector->getActionParams($action);
				 
			$params = array();

			foreach ($actionParams as $actionParam)
			{
				$actionName = $actionParam->getName();
				$typeName = $actionParam->getType();
				if($typeName == "KalturaPermissionItem")
				{
					$params[] = new KalturaApiActionPermissionItem();
					continue;
				}
				
				if($actionParam->isComplexType())
				{
					if($actionParam->isArray())
					{
						$params[] = array();
					}
					else //it is a complex not array type
					{
						$type = $actionParam->getTypeReflector();
													
						if($type != null)
						{
							if(!$type->isAbstract())
							{
								$params[] = $type->getInstance();
							}
							else // handle all abstract classes and not just the permission item
							{
								//TODO: handle abstract classes
								$params[] = new KalturaApiActionPermissionItem();								
							}
						}
						else //type == null
						{
							$params[] = null; 
						}
					}
				}
				else //For int, bool, string, float (SimpleType)
				{
					$params[] = 0;
				}
			}
	
			if($action == "list")
			{
				$action .= "Action";
			}
			
			try
			{
				call_user_func_array(array($testedClient->$serviceName, $action), $params);				
				//TODO: Handle non exception cases
				$this->compareServiceAction($permissions, $alwaysAllowedActions, $serviceName, $action);
			}
			catch(Exception $ex)
			{
				//Check if the service / action is found in the user permissions
				$this->compareServiceAction($permissions, $alwaysAllowedActions, $serviceName, $action, $ex);
			}
		}
	}
	
	/**
	 * Compares if the given exception   
	 */
	private function compareServiceAction($permissions, $alwaysAllowedActions, $serviceName, $actionName, $ex = null)
	{
		$isFound = $this->isFound($permissions, $serviceName, $actionName);
		$isAlwaysAllowed =  $this->isFound($alwaysAllowedActions, $serviceName, $actionName);
			
		if($ex != null)
		{
			if($ex->getCode() == 4096)
			{
				print($ex->getMessage(). "\n");
			}
			
			if($isFound || $isAlwaysAllowed)
			{
				$this->compareOnField("{$serviceName}::{$actionName}", $ex->getCode(), 'SERVICE_FORBIDDEN', "assertNotEquals", $ex->getMessage());
			}
			else
			{
				$this->compareOnField("{$serviceName}::{$actionName}", $ex->getCode(), 'SERVICE_FORBIDDEN', "assertEquals", $ex->getMessage());
			}
		}
		else // No exception was raised in this service
		{
			if($isFound || $isAlwaysAllowed) // if is found
			{}
			else //the service is not open and we should have gotten an exception 
			{
				$this->compareOnField("{$serviceName}::{$actionName}", 'NO_EXCEPTION_WAS_RAISED', 'SERVICE_FORBIDDEN', "assertEquals");
			}
		}
	}
	
	/**
	 * 
	 * Checks if the service and action found in the given array of permissions items
	 * @param array<KalturaPermissionItem> $permissions
	 * @param string $serviceName
	 * @param string $actionName
	 */
	private function isFound($permissions, $serviceName, $actionName)
	{
		$newActionName = $actionName;
		
		if($actionName == "listAction")
		{
			$newActionName = "list";
		}
		
		if(count($permissions) > 0)
		{
			foreach ($permissions as $permission)
			{
				if(strtolower($permission->service) == strtolower($serviceName) && strtolower($permission->action) == strtolower($newActionName))
				{
					return true;
				}
			}
		}
		return false;
	}
	
	/**
	 * 
	 * Provides the data to the accessibility success test
	 */
	public function accessibilitySuccessProvider()
	{
		$rawInputs = $this->provider(dirname(__FILE__) . "/testsData/testAccessibilitySuccess.data");
		
		$inputs = array();
		$index = -1;
		
		foreach ($rawInputs as $rawInput)
		{
			$index++;
			$inputs[] = array();
			$inputs[$index][] = $rawInputs[$index][0]->additionalData["partnerId"];	
			$inputs[$index][] = $rawInputs[$index][0]->additionalData["secret"];
			$inputs[$index][] = $rawInputs[$index][0]->additionalData["serviceUrl"];
			$inputs[$index][] = $rawInputs[$index][0]->additionalData["isAdmin"];
			$inputs[$index][] = $rawInputs[$index][0]->additionalData["userId"];
		}
		
		return $inputs;
	}

	/**
	 * 
	 * Returns the given clients permission items
	 * @param unknown_type $client
	 * @return array<KalturaPermissionItem>
	 */
	private function getPermissionsItems($client, $userId, $partnerId, $isAdmin, $configServiceUrl)
	{
		$permissionItems = array();

		$roleIds = "";
		
		$config = new KalturaConfiguration($partnerId);

		//Add the server url (into the test additional data)
		$config->serviceUrl = $configServiceUrl;
		$impersonatedClient = new KalturaClient($config);
		$sessionType = KalturaSessionType::ADMIN;
				
		$impersonateKS = self::$adminClient->session->impersonate(self::ADMIN_SECRET, $partnerId, $userId, KalturaSessionType::ADMIN, self::ADMIN_PARTNER);
		$impersonatedClient->setKs($impersonateKS);
				
		$testedUser = $impersonatedClient->user->get($userId);
		
		self::$adminClient = $this->getClient(self::ADMIN_PARTNER, self::ADMIN_SECRET, self::TEST_SERVER, 1);
		
		//If the user has a role then
		if(isset($testedUser->roleIds) && $testedUser->roleIds != "")
		{
			$roleIds = explode(',', $testedUser->roleIds);
		}
		else // Else the user doesn't have a role
		{
			//Get role through the system partner default admin / user permissions 
			$partnerConfig = self::$adminClient->systemPartner->getConfiguration($partnerId);
			
			if($isAdmin)
			{
				$roleIds = explode(",", $partnerConfig->adminSessionRoleId);
			}
			else
			{
				$roleIds = explode(",", $partnerConfig->userSessionRoleId);
			}
		}
		
		//For each user roles ids
		foreach($roleIds as $roleId)
		{
			$this->addPermissionsFromRole($roleId, &$permissionItems, $client);
		}

		return $permissionItems;
	}

	/**
	 * 
	 * Add to the given collection the permissions items assoiated with the given role id
	 * @param unknown_type $roleId
	 * @param unknown_type $permissionItems
	 * @param unknown_type $client
	 */
	private function addPermissionsFromRole($roleId, $permissionItems, $client)
	{
		//We get all the role's permission names
		$role = $client->userRole->get($roleId);
		$permissionNames = explode(',', $role->permissionNames);

		//And for each permission name
		foreach ($permissionNames as $permissionName)
		{
			if($permissionName != "")
			{
				try 
				{
					//We get the permission items ids
					$permission = self::$adminClient->permission->get($permissionName);
					$permissionItemIds = explode(',', $permission->permissionItemsIds);
				}
				catch(Exception $e)
				{
					$this->compareOnField("Role::PermissionNames", $permissionName, "", "assertEquals", $e->getMessage());
					$permissionItemIds = array();
				}
				
				//And for each permission item id
				foreach ($permissionItemIds as $permissionItemId)
				{
					$this->addPermissionItem(&$permissionItems, $permissionItemId);
				}
			}
		}
	}
	
	/**
	 * 
	 * Gets the permission item by its id and adds it into the given $permissionItem array
	 * @param array<KalturaApiActionPermissionItem> $permissionItems
	 * @param unknown_type $permissionItemId
	 */
	private function addPermissionItem($permissionItems, $permissionItemId)
	{
		//We insert only action permissions and only once
		if($permissionItemId != "")
		{
			if(!isset($permissionItems[$permissionItemId]))
			{
				$permissionItem = self::$adminClient->permissionItem->get($permissionItemId);

				//We add only the action related permission items
				if($permissionItem instanceof KalturaApiActionPermissionItem)
				{
					$permissionItems[$permissionItemId] = $permissionItem;
				}
			}
		}
	}
	
	/**
	 * 
	 * Gets the always allowed
	 */
	private function getAlwaysAllowedActions($partnerId)
	{
		$permissionItems = array();
				
		$partnerConfig = self::$adminClient->systemPartner->getConfiguration($partnerId);
		
		$permissionNames = explode(",", $partnerConfig->alwaysAllowedPermissionNames);
	
		foreach ($permissionNames as $permissionName)
		{
			//Skip empty permissions names
			if($permissionName == ''){continue;}
						
			$permission = self::$adminClient->permission->get($permissionName);

			$permissionItemIds = explode(',', $permission->permissionItemsIds);

			foreach ($permissionItemIds as $permissionItemId)
			{
				$this->addPermissionItem(&$permissionItems, $permissionItemId); 
			}
		}
		
		return $permissionItems; 
	}
	
	/**
	 * 
	 * Wraps the call_user_func_array for faster use.
	 * @param unknown_type $c
	 * @param unknown_type $a
	 * @param unknown_type $p
	 */
	private function wrap_call_user_func_array($c, $a, $p) {
    switch(count($p)) {
        case 0: $c->{$a}(); break;
        case 1: $c->{$a}($p[0]); break;
        case 2: $c->{$a}($p[0], $p[1]); break;
        case 3: $c->{$a}($p[0], $p[1], $p[2]); break;
        case 4: $c->{$a}($p[0], $p[1], $p[2], $p[3]); break;
        case 5: $c->{$a}($p[0], $p[1], $p[2], $p[3], $p[4]); break;
        default: call_user_func_array(array($c, $a), $p);  break;
    	}
	}
}

