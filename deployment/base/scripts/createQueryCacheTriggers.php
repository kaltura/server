<?php

$dryRun = true;

// Invalidation keys table
$INVALIDATION_KEYS = array(
	array('table' => "flavor_asset", 					'keys' => array(array("'flavorAsset:id='", '@OBJ@.id'), array("'flavorAsset:entryId='", '@OBJ@.entry_id')), 							'class' => 'asset'),
	array('table' => "kuser", 							'keys' => array(array("'kuser:id='", '@OBJ@.id'), array("'kuser:partnerId='", '@OBJ@.partner_id', "',puserid='", '@OBJ@.puser_id'), array("'kuser:loginDataId='", '@OBJ@.login_data_id'))),
	array('table' => "entry", 							'keys' => array(array("'entry:id='", '@OBJ@.id'), array("'entry:partnerId='", '@OBJ@.partner_id'))),
	array('table' => "access_control", 					'keys' => array(array("'accessControl:id='", '@OBJ@.id'))),
	array('table' => "permission", 						'keys' => array(array("'permission:partnerId='", '@OBJ@.partner_id'))),
	array('table' => "kuser_to_user_role",	 			'keys' => array(array("'kuserToUserRole:kuserId='", '@OBJ@.kuser_id'))),
	array('table' => "category", 						'keys' => array(array("'category:id='", '@OBJ@.id'), array("'category:partnerId='", '@OBJ@.partner_id'))),
	array('table' => "file_sync", 						'keys' => array(array("'fileSync:id='", '@OBJ@.id'), array("'fileSync:objectId='", '@OBJ@.object_id'))),
	array('table' => "media_info", 						'keys' => array(array("'mediaInfo:flavorAssetId='", '@OBJ@.flavor_asset_id'))),
	array('table' => "storage_profile", 				'keys' => array(array("'storageProfile:id='", '@OBJ@.id'), array("'storageProfile:partnerId='", '@OBJ@.partner_id'))),
	array('table' => "ui_conf", 						'keys' => array(array("'uiConf:id='", '@OBJ@.id'), array("'uiConf:partnerId='", '@OBJ@.partner_id'))),
	array('table' => "widget", 							'keys' => array(array("'widget:id='", '@OBJ@.id'))),
	array('table' => "metadata", 						'keys' => array(array("'metadata:objectId='", '@OBJ@.object_id')), 																		'plugin' => 'metadata'),
	array('table' => "metadata_profile", 				'keys' => array(array("'metadataProfile:id='", '@OBJ@.id'), array("'metadataProfile:partnerId='", '@OBJ@.partner_id')), 				'plugin' => 'metadata'),
	array('table' => "metadata_profile_field", 			'keys' => array(array("'metadataProfileField:metadataProfileId='", '@OBJ@.metadata_profile_id')),										'plugin' => 'metadata'),
	array('table' => "partner", 						'keys' => array(array("'partner:id='", '@OBJ@.id'))),
	array('table' => "cue_point", 						'keys' => array(array("'cuePoint:id='", '@OBJ@.id'), array("'cuePoint:entryId='", '@OBJ@.entry_id')),									'plugin' => 'cue_points/base'),
	array('table' => "drop_folder_file", 				'keys' => array(array("'dropFolderFile:id='", '@OBJ@.id'), array("'dropFolderFile:fileName='", '@OBJ@.file_name'), array("'dropFolderFile:dropFolderId='", '@OBJ@.drop_folder_id')),			'plugin' => 'drop_folder'),
	array('table' => "flavor_params_output", 			'keys' => array(array("'flavorParamsOutput:id='", '@OBJ@.id'), array("'flavorParamsOutput:flavorAssetId='", '@OBJ@.flavor_asset_id')),	'class' => 'assetParamsOutput'),
	array('table' => "entry_distribution", 				'keys' => array(array("'entryDistribution:entryId='", '@OBJ@.entry_id')),																'plugin' => 'content_distribution'),
	array('table' => "flavor_params", 					'keys' => array(array("'flavorParams:id='", '@OBJ@.id'), array("'flavorParams:partnerId='", '@OBJ@.partner_id')),						'class' => 'assetParams'),
	array('table' => "flavor_params_conversion_profile",'keys' => array(array("'flavorParamsConversionProfile:flavorParamsId='", '@OBJ@.flavor_params_id', "',conversionProfileId='", '@OBJ@.conversion_profile_id'), array("'flavorParamsConversionProfile:conversionProfileId='", '@OBJ@.conversion_profile_id'))),
	array('table' => "user_role", 						'keys' => array(array("'userRole:id='", '@OBJ@.id'), array("'userRole:systemName='", '@OBJ@.system_name'))),
	array('table' => "invalid_session", 				'keys' => array(array("'invalidSession:ks='", '@OBJ@.ks'))),
	array('table' => "upload_token", 					'keys' => array(array("'uploadToken:id='", '@OBJ@.id'))),
	array('table' => "conversion_profile_2", 			'keys' => array(array("'conversionProfile2:id='", '@OBJ@.id'), array("'conversionProfile2:partnerId='", '@OBJ@.partner_id'))),
	array('table' => "distribution_profile", 			'keys' => array(array("'distributionProfile:id='", '@OBJ@.id')),																		'plugin' => 'content_distribution'),
	array('table' => "drop_folder", 					'keys' => array(array("'dropFolder:id='", '@OBJ@.id'), array("'dropFolder:dc='", '@OBJ@.dc')),											'plugin' => 'drop_folder'),
	array('table' => "category_entry", 					'keys' => array(array("'categoryEntry:entryId='", '@OBJ@.entry_id'), array("'categoryEntry:categoryId='", '@OBJ@.category_id'))),
	array('table' => "permission_to_permission_item", 	'keys' => array(array("'permissionToPermissionItem:permissionId='", '@OBJ@.permission_id'))),
	array('table' => "delivery_profile", 				'keys' => array(array("'deliveryProfile:id='", '@OBJ@.id'), array("'deliveryProfile:partnerId='", '@OBJ@.partner_id'))),
	array('table' => "event_notification_template", 	'keys' => array(array("'eventNotificationTemplate:id='", '@OBJ@.id'), array("'eventNotificationTemplate:partnerId='", '@OBJ@.partner_id')), 'plugin' => 'event_notification'),
	array('table' => "category_kuser", 					'keys' => array(array("'categoryKuser:id='", '@OBJ@.id'), array("'categoryKuser:categoryId='", '@OBJ@.category_id'))),
	array('table' => "kuser_kgroup", 					'keys' => array(array("'kuserKgroup:kuserId='", '@OBJ@.kuser_id'))),
	array('table' => "response_profile", 				'keys' => array(array("'responseProfile:systemName='", '@OBJ@.system_name'))),
	array('table' => "entry_server_node", 				'keys' => array(array("'entryServerNode:id='", '@OBJ@.id'), array("'entryServerNode:entryId'", '@OBJ@.entry_id'))),
	array('table' => "server_node", 					'keys' => array(array("'serverNode:id'", '@OBJ@.id'), array("'serverNode:hostName='", '@OBJ@.host_name'))),
	array('table' => "schedule_event",                  'keys' => array(array("'scheduleEvent:id'", '@OBJ@.id')),                                                                               'plugin' => 'schedule/base'),
	array('table' => "schedule_resource",               'keys' => array(array("'scheduleResource:id='", '@OBJ@.id')),                                                                           'plugin' => 'schedule/base'),
	array('table' => "schedule_event_resource",         'keys' => array(array("'scheduleEventResource:eventId='", '@OBJ@.event_id')),                                                           'plugin' => 'schedule/base'),
	array('table' => "user_login_data", 				'keys' => array(array("'userLoginData:id='", '@OBJ@.id'), array("'userLoginData:loginEmail='", '@OBJ@.login_email'))),
	array('table' => "drm_profile", 					'keys' => array(array("'drmProfile:partnerId='", '@OBJ@.partner_id'))),
	array('table' => "scheduler", 						'keys' => array(array("'scheduler:configuredId='", '@OBJ@.configured_id'))),
	array('table' => "syndication_feed", 				'keys' => array(array("'syndicationFeed:id='", '@OBJ@.id'))),
	array('table' => "app_token", 						'keys' => array(array("'appToken:id='", '@OBJ@.id'))),
	array('table' => "user_entry", 						'keys' => array(array("'userEntry:kuserId='", '@OBJ@.kuser_id'))),
	
	);

$TRIGGER_TYPES = array('INSERT', 'UPDATE', 'DELETE');

$SPECIAL_TRIGGERS = array(
	"invalid_session/INSERT" => "DO memc_set(concat('invalid_session_', IF(NEW.ks IS NULL, '', NEW.ks)), 1, IF(NEW.ks_valid_until IS NULL, 0, UNIX_TIMESTAMP(NEW.ks_valid_until) + 600));",
	"file_sync/INSERT" => "IF (NEW.original) THEN DO memc_set(concat('fileSyncMaxId-dc', NEW.dc), NEW.id); END IF;",
);

function generateInvalidationKeyCode($invalidationKey)
{
	$objKeys = array();
	$peerKeys = array();
	foreach ($invalidationKey['keys'] as $curKeyStrings)
	{
		$objArrayElems = array();
		$peerArrayElems = array('');
		foreach ($curKeyStrings as $curStr)
		{
			if (strpos($curStr, '@OBJ@') === false)
			{
				$peerArrayElems[0] .= str_replace("'", "", $curStr);
				$objArrayElems[] = str_replace("'", '"', $curStr);
			}
			else 
			{
				$curStr = str_replace("@OBJ@.", "", $curStr);
				$peerArrayElems[0] .= "%s";
				$peerArrayElems[] = "self::" . strtoupper($curStr);
				$curStrUpperCamel = str_replace(' ', '', ucwords(str_replace('_', ' ', $curStr)));
				$objArrayElems[] = 'strtolower($this->get' . $curStrUpperCamel . '())';
			}
		}
		
		$peerArrayElems[0] = '"' . $peerArrayElems[0] . '"';
		$peerArrayElems = implode(', ', $peerArrayElems);
		
		$peerKeys[] = "array($peerArrayElems)";
		
		$objArrayElems = implode('.', $objArrayElems);
		
		$objKeys[] = $objArrayElems;
	}
	
	$objKeys = implode(', ', $objKeys);
	$peerKeys = implode(', ', $peerKeys);
	
		$objFunc = 
	"public function getCacheInvalidationKeys()
	{
		return array($objKeys);
	}";
			
			$peerFunc = 
	"public static function getCacheInvalidationKeys()
	{
		return array($peerKeys);		
	}";
	
	return array(str_replace("\n", PHP_EOL, $objFunc), str_replace("\n", PHP_EOL, $peerFunc));
}

function getFuncEnd($fileData, $funcPos)
{
	$braceCount = 0;
	for (;; $funcPos++)
	{
		switch ($fileData[$funcPos])
		{
		case '{':
			$braceCount++;
			break;

		case '}':
			$braceCount--;
			if ($braceCount == 0)
			{
				return $funcPos + 1;
			}
			break;
		}
	}
}

function updateInvalidationFunc($fileName, $newFunc, $funcSpec)
{
	$newFunc = str_replace("\r\n", "\n", $newFunc);
	$fileData = file_get_contents($fileName);
	$funcPos = strpos($fileData, "$funcSpec function getCacheInvalidationKeys()");
	if ($funcPos !== false)
	{
		print "$fileName - replacing existing func\n";
		$funcEnd = getFuncEnd($fileData, $funcPos);
		$oldFunc = substr($fileData, $funcPos, $funcEnd - $funcPos);
		$fileData = str_replace($oldFunc, $newFunc, $fileData);
	}
	else
	{
		print "$fileName - adding new func\n";
		$funcPos = strrpos($fileData, "}");
		$fileData = substr($fileData, 0, $funcPos) . "\t" . $newFunc . "\n" . substr($fileData, $funcPos); 
	}
	file_put_contents($fileName, $fileData);
}

function updateTableCode($invalidationKey, $objFunc, $peerFunc)
{
	$serverRoot = realpath(dirname(__FILE__)."/../../..");
	
	if (array_key_exists('plugin', $invalidationKey))
	{
		$modelPath = "$serverRoot/plugins/" . $invalidationKey['plugin'] . "/lib/model/";
	}
	else
	{
		$modelPath = "$serverRoot/alpha/lib/model/";
	}
	
	if (array_key_exists('class', $invalidationKey))
	{
		$className = $invalidationKey['class'];
	}
	else
	{
		$className = str_replace("_", "", $invalidationKey['table']);
	}

	$objClassFile = $modelPath . $className . ".php";
	updateInvalidationFunc($objClassFile, $objFunc, "public");
	
	$peerClassFile = $modelPath . $className . "Peer.php";
	updateInvalidationFunc($peerClassFile, $peerFunc, "public static");
}

function generateCode()
{
	global $INVALIDATION_KEYS;
	
	foreach ($INVALIDATION_KEYS as $invalidationKey)
	{
		list($objFunc, $peerFunc) = generateInvalidationKeyCode($invalidationKey);
		updateTableCode($invalidationKey, $objFunc, $peerFunc);
	}
}

function stripTrailingSemicolon($str)
{
	if (strlen($str) && $str[strlen($str) - 1] == ';')
	{
		return substr($str, 0, strlen($str) - 1);
	}
	return $str;
}

function compareTriggerBodies($body1, $body2)
{
	return stripTrailingSemicolon($body1) == stripTrailingSemicolon($body2);
}

function buildTriggerBody($invalidationKey, $triggerType)
{
	global $SPECIAL_TRIGGERS;

	$tableName = $invalidationKey['table'];
	$triggerBody = array();
	foreach ($invalidationKey['keys'] as $curKeyStrings)
	{
		$keyChangeCondition = array();
		$curKey = array("'QCI-'");
		foreach ($curKeyStrings as $curStr)
		{
			if (strpos($curStr, '@OBJ@') === false)
				$curKey[] = $curStr;
			else 
			{
				$curStrValue = "LOWER(REPLACE($curStr,' ','_'))";
				$curKey[] = "IF($curStr IS NULL,'',$curStrValue)";
				$keyChangeCondition[] = str_replace('@OBJ@', 'OLD', $curStr) . ' <> ' . str_replace('@OBJ@', 'NEW', $curStr);
			}
		}
		$curKey = 'concat(' . implode(', ', $curKey) . ')';
		
		$memSetCmd = "memc_set($curKey, UNIX_TIMESTAMP(SYSDATE()), 90000)";
		$memSetCmdOld = str_replace('@OBJ@', 'OLD', $memSetCmd);
		$memSetCmdNew = str_replace('@OBJ@', 'NEW', $memSetCmd);
		
		switch ($triggerType)
		{
		case 'DELETE':
			$curStatement = $memSetCmdOld;
			break;
			
		case 'INSERT':
			$curStatement = $memSetCmdNew;
			break;
			
		case 'UPDATE':
			$keyChangeCondition = implode(' || ', $keyChangeCondition);
			$curStatement = "IF($keyChangeCondition, $memSetCmdNew && $memSetCmdOld, $memSetCmdNew)";
			break;
		}
		$triggerBody[] = "DO $curStatement;";
	}
	
	$specialTriggerKey = "{$tableName}/{$triggerType}";
	if (array_key_exists($specialTriggerKey, $SPECIAL_TRIGGERS))
	{
		$triggerBody[] = $SPECIAL_TRIGGERS[$specialTriggerKey];
	}
	
	if (count($triggerBody) > 1)
	{
		$triggerBody = 'BEGIN ' . implode(' ', $triggerBody) . ' END';
	}
	else
	{
		$triggerBody = implode(' ', $triggerBody);
	}

	return $triggerBody;
}
	
// Default parameters
$ACTION = 'help';
$HOST_NAME = '127.0.0.1';
$USER_NAME = 'root';
$PASSWORD = '';

// Parse command line
if ($argc > 1)
	$ACTION = $argv[1];

if ($ACTION == 'help')
	die("Usage:\n\tphp query_cache_triggers [<action> [<hostname> [<username> [<password>] [realrun]]]]\n");

$ALLOWED_ACTIONS = array('create', 'remove', 'gencode');
if (!in_array($ACTION, $ALLOWED_ACTIONS))
	die("Error: Invalid action $ACTION possible actions: " . implode(', ', $ALLOWED_ACTIONS) . "\n");

if ($ACTION == 'gencode')
{
	generateCode();
	die();
}
	
if ($argc > 2)
	$HOST_NAME = $argv[2];
if ($argc > 3)
	$USER_NAME = $argv[3];
if ($argc > 4)
	$PASSWORD = $argv[4];
if ($argc > 5 && $argv[5] === 'realrun')
	$dryRun = false;

// Connect to database
$link = mysqli_connect($HOST_NAME, $USER_NAME, $PASSWORD)
    or die('Error: Could not connect: ' . mysqli_connect_error() . "\n");

// Make sure 'Memcached Functions for MySQL' is installed
mysqli_select_db($link,'mysql') or die("Error: Could not select 'mysql' database\n");
$query = "SELECT * FROM func WHERE name='memc_server_count'";
$result = mysqli_query($link,$query) or die('Error: Select from func table query failed: ' . mysqli_error($link) . "\n");

if (!mysqli_fetch_array($result, MYSQLI_ASSOC))
{
	die("Error: 'Memcached Functions for MySQL' not installed\nNote: this script should only be run on multi-datacenter environments.\n");
}

mysqli_free_result($result);

// Change database to kaltura
mysqli_select_db($link,'kaltura') or die("Error: Could not select 'kaltura' database\n");

// Make sure the memcache server is configured
$query = "SELECT memc_server_count()";
$result = mysqli_query($link,$query) or die('Error: Select memc_server_count query failed: ' . mysqli_error($link) . "\n");

$line = mysqli_fetch_array($result, MYSQLI_NUM);
if (!$line)
{
	die("Unexpected: memc_server_count returned nothing\n");
}

if ($line[0] <= 0)
{
	die("Error: Memcached server not configured\n");
}

mysqli_free_result($result);

// Get the slave status
$query = "SHOW SLAVE STATUS";
$result = mysqli_query($link,$query) or die('Error: show slave status query failed: ' . mysqli_error($link) . "\n");

$status = mysqli_fetch_array($result, MYSQLI_ASSOC);
$slaveRunning = isset($status['Slave_SQL_Running']) ? $status['Slave_SQL_Running'] : null;
if (!in_array($slaveRunning, array('Yes', 'No')))
{
	die("Unexpected: show slave status returned an unexpected result [$slaveRunning]\n");
}

$slaveRunning = ($slaveRunning == 'Yes');
$initialSlaveRunning = $slaveRunning;

mysqli_free_result($result);

// Get list of installed triggers
$triggers = array();
$query = "SHOW TRIGGERS";
$result = mysqli_query($link,$query) or die('Error: Show triggers failed: ' . mysqli_error($link) . "\n");
for(;;)
{
        $curRes = mysqli_fetch_array($result, MYSQLI_ASSOC);
        if (!$curRes)
                break;
		$triggerName = $curRes["Trigger"];
		$triggerStatement = $curRes["Statement"];
		
		$triggers[$triggerName] = $triggerStatement;
}
mysqli_free_result($result);

// Install / remove triggers
foreach ($INVALIDATION_KEYS as $invalidationKey)
{
	$tableName = $invalidationKey['table'];
		
	$sqlCommands = array();
	
	if ($ACTION == 'create')
	{		
		foreach ($TRIGGER_TYPES as $triggerType)
		{
			$triggerBody = buildTriggerBody($invalidationKey, $triggerType);
			$triggerName = "{$tableName}_".strtolower($triggerType)."_memcache";
				
			if (!array_key_exists($triggerName, $triggers) || 
				!compareTriggerBodies($triggerBody, $triggers[$triggerName]))
			{
				$sqlCommands[] = "DROP TRIGGER IF EXISTS {$tableName}_".strtolower($triggerType)."_memcache";
				$sqlCommands[] = "CREATE TRIGGER {$triggerName} AFTER {$triggerType} ON {$tableName} FOR EACH ROW {$triggerBody}";
			}
		}
		
		if (!$sqlCommands)
		{
			print "Skipping {$tableName} - no changes detected...\n";
			continue;
		}
				
		print "Creating triggers on {$tableName}...\n";
	}
	else
	{
		foreach ($TRIGGER_TYPES as $triggerType)
		{
			$sqlCommands[] = "DROP TRIGGER IF EXISTS {$tableName}_".strtolower($triggerType)."_memcache";
		}
		print "Removing triggers on {$tableName}...\n";
	}
	
	foreach ($sqlCommands as $sqlCommand)
	{
		if ($dryRun)
		{
			print $sqlCommand . PHP_EOL;
		}
		else
		{
			if ($slaveRunning)
			{
				print "Stopping slave...\n";
				$result = mysqli_query($link,'STOP SLAVE') or die('Error: Stop slave query failed: ' . mysqli_error($link) . "\n");
				if ($result !== true)
				{
					die("Error: Unexpected result returned while stopping slave\n");
				}
				$slaveRunning = false;
			}
			
			$result = mysqli_query($link,$sqlCommand) or die('Error: Trigger query failed: ' . mysqli_error($link) . "\n");
			if ($result !== true)
			{
				die("Error: Unexpected result returned from mysqli_query()\n");
			}
		}
	}
}

if (!$slaveRunning && $initialSlaveRunning)
{
	print "Starting slave...\n";
	$result = mysqli_query($link,'START SLAVE') or die('Error: Start slave query failed: ' . mysqli_error($link) . "\n");
	if ($result !== true)
	{
		die("Error: Unexpected result returned while starting slave\n");
	}
}

// Close database connection
mysqli_close($link);

print "Done !\n";
