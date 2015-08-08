<?php
	const INTEGRATION_SERVICE_NAME = "integration_integration";
	const NOTIFY_ACTION_NAME = "notify";
	const EXTERNAL_SERVICE_ACTIONS_PERMISSION_NAME = "VOICEBASE_ACTIONS";
	
	if($argc < 5)
	{
		die("Usage: php addVoicebaseParamsToPartner [kaltura base directory] [partner id] [apiKey] [apiPassword] [should create user role]" . PHP_EOL);
	}
	
	$currentWorkingEnv = $argv[1];
	if(!file_exists($currentWorkingEnv))
		die("input kaltura base directory \"$currentWorkingEnv\" does not exists");
	
	require_once($currentWorkingEnv . '/alpha/scripts/bootstrap.php');
	
	$currentWorkingEnv = $argv[1];
	$partnerId = $argv[2];
	$apiKey = $argv[3];
	$apiPassword = $argv[4];
	$shouldCreatePermissionMap = $argv[5];
	
	$params = array('apiKey' => $apiKey, 'apiPassword' => $apiPassword);
	
	$plugin = new VoicebasePlugin();
	$plugin->setPartnerVoicebaseOptions($partnerId, $params);
	
	if($shouldCreatePermissionMap == "create")
	{
		$c = new Criteria();
		$c->add(PermissionItemPeer::PARTNER_ID, PartnerPeer::GLOBAL_PARTNER);
		$c->add(PermissionItemPeer::PARAM_1, INTEGRATION_SERVICE_NAME);
		$c->add(PermissionItemPeer::PARAM_2, NOTIFY_ACTION_NAME);
		
		$permissionItem = PermissionItemPeer::doSelectOne($c);
		if(!$permissionItem)
			die("no permission-item found" . PHP_EOL);
	
		$permissionItemId = $permissionItem->getId();
		KalturaLog::debug("permission item id [$permissionItemId]");
			
		$c->clear();
		$c->add(PermissionPeer::PARTNER_ID, PartnerPeer::GLOBAL_PARTNER);
		$c->add(PermissionPeer::NAME, EXTERNAL_SERVICE_ACTIONS_PERMISSION_NAME);
		$c->add(PermissionPeer::TYPE, PermissionType::NORMAL);
		$permission = PermissionPeer::doSelectOne($c);
		if(!$permission)
			die("no permission found" . PHP_EOL);
		
		$permissionId = $permission->getId();
		KalturaLog::debug("permission id [$permissionId]");
		
		$c->clear();
		$c->add(PermissionToPermissionItemPeer::PERMISSION_ITEM_ID, $permissionItemId);
		$c->add(PermissionToPermissionItemPeer::PERMISSION_ID, $permissionId);
		
		$permissionToPermissionItem = PermissionToPermissionItemPeer::doSelectOne($c);
		if(!$permissionToPermissionItem)
			die("no permission-to-permission-item found" . PHP_EOL);
		
		$permissionToPermissionItemId = $permissionToPermissionItem->getId();
		KalturaLog::debug("permission-to-permission-item id [$permissionToPermissionItemId]");
		
		$c->clear();
		$c->add(UserRolePeer::PARTNER_ID, $partnerId);
		$c->add(UserRolePeer::NAME, VoicebasePlugin::EXTERNAL_INTEGRATION_SERVICES_ROLE_NAME);
		$userRole = UserRolePeer::doSelectOne($c);
		if(!$userRole)
		{
			$userRoleToAdd = new UserRole();
			$userRoleToAdd->setStrId(VoicebasePlugin::EXTERNAL_INTEGRATION_SERVICES_ROLE_NAME);
			$userRoleToAdd->setName(VoicebasePlugin::EXTERNAL_INTEGRATION_SERVICES_ROLE_NAME);
			$userRoleToAdd->setSystemName(VoicebasePlugin::EXTERNAL_INTEGRATION_SERVICES_ROLE_NAME);
			$userRoleToAdd->setPartnerId($partnerId);
			$userRoleToAdd->setStatus(UserRoleStatus::ACTIVE);
			$userRoleToAdd->setPermissionNames(EXTERNAL_SERVICE_ACTIONS_PERMISSION_NAME);
			$userRoleToAdd->save();
			$userRoleToAddId = $userRoleToAdd->getId();
						KalturaLog::debug("added user-role [$userRoleToAddId]");
		}
		else
		{
			$userRoleId = $userRole->getId();
						KalturaLog::debug("user-role [$userRoleId] already exists");
		}
	
		$c->clear();
				$c->add(PermissionPeer::PARTNER_ID, PartnerPeer::GLOBAL_PARTNER);
				$c->add(PermissionPeer::NAME, VoicebasePlugin::PARTNER_LEVEL_PERMISSION_NAME);
		$c->add(PermissionPeer::TYPE, PermissionType::SPECIAL_FEATURE);
		$partnerPermission = PermissionPeer::doSelectOne($c);
		if(!$partnerPermission)
		{
			$permissionToAdd = new Permission();
			$permissionToAdd->setPartnerId($partnerId);
			$permissionToAdd->setName(VoicebasePlugin::PARTNER_LEVEL_PERMISSION_NAME);
			$permissionToAdd->setFriendlyName(VoicebasePlugin::PARTNER_LEVEL_PERMISSION_NAME);
			$permissionToAdd->setType(PermissionType::SPECIAL_FEATURE);
			$permissionToAdd->setStatus(PermissionStatus::ACTIVE);
			$permissionToAdd->save();
			$permissionToAddId = $permissionToAdd->getId();
			KalturaLog::debug("added partner-level permission [$permissionToAddId]");
		}
		else
		{
			$partnerPermissionId = $partnerPermission->getId();
			KalturaLog::debug("partner level permission [$partnerPermissionId] alreaady exists");
		}
	}


