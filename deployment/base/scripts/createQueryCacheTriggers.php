<?php

// Invalidation keys table
$INVALIDATION_KEYS = array(
	array('table' => "flavor_asset", 			'keys' => array(array("'flavorAsset:id='", '@OBJ@.id'), array("'flavorAsset:entryId='", '@OBJ@.entry_id')), 							'class' => 'asset'),
	array('table' => "kuser", 					'keys' => array(array("'kuser:partnerId='", '@OBJ@.partner_id', "',puserid='", '@OBJ@.puser_id'))),
	array('table' => "entry", 					'keys' => array(array("'entry:id='", '@OBJ@.id'))),
	array('table' => "access_control", 			'keys' => array(array("'accessControl:id='", '@OBJ@.id'))),
	array('table' => "permission", 				'keys' => array(array("'permission:partnerId='", '@OBJ@.partner_id'))),
	array('table' => "kuser_to_user_role",	 	'keys' => array(array("'kuserToUserRole:kuserId='", '@OBJ@.kuser_id'))),
	array('table' => "category", 				'keys' => array(array("'category:partnerId='", '@OBJ@.partner_id'))),
	array('table' => "file_sync", 				'keys' => array(array("'fileSync:objectId='", '@OBJ@.object_id'))),
	array('table' => "media_info", 				'keys' => array(array("'mediaInfo:flavorAssetId='", '@OBJ@.flavor_asset_id'))),
	array('table' => "storage_profile", 		'keys' => array(array("'storageProfile:id='", '@OBJ@.id'), array("'storageProfile:partnerId='", '@OBJ@.partner_id'))),
	array('table' => "ui_conf", 				'keys' => array(array("'uiConf:id='", '@OBJ@.id'))),
	array('table' => "widget", 					'keys' => array(array("'widget:id='", '@OBJ@.id'))),
	array('table' => "metadata", 				'keys' => array(array("'metadata:objectId='", '@OBJ@.object_id')), 																		'plugin' => 'metadata'),
	array('table' => "metadata_profile_field", 	'keys' => array(array("'metadataProfileField:metadataProfileId='", '@OBJ@.metadata_profile_id')),										'plugin' => 'metadata'),
	array('table' => "partner", 				'keys' => array(array("'partner:id='", '@OBJ@.id'))),
	array('table' => "cue_point", 				'keys' => array(array("'cuePoint:id='", '@OBJ@.id'), array("'cuePoint:entryId='", '@OBJ@.entry_id')),									'plugin' => 'cue_points/base'),
	array('table' => "drop_folder_file", 		'keys' => array(array("'dropFolderFile:id='", '@OBJ@.id'), array("'dropFolderFile:dropFolderId='", '@OBJ@.drop_folder_id')),			'plugin' => 'drop_folder'),
	array('table' => "flavor_params_output", 	'keys' => array(array("'flavorParamsOutput:id='", '@OBJ@.id'), array("'flavorParamsOutput:flavorAssetId='", '@OBJ@.flavor_asset_id')),	'class' => 'assetParamsOutput'),
	array('table' => "entry_distribution", 		'keys' => array(array("'entryDistribution:entryId='", '@OBJ@.entry_id')),																'plugin' => 'content_distribution'),
	array('table' => "flavor_params", 			'keys' => array(array("'flavorParams:id='", '@OBJ@.id')),																				'class' => 'assetParams'),
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
				$objArrayElems[] = '$this->get' . $curStrUpperCamel . '()';
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
	
	return array($objFunc, $peerFunc);
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
	
	
// Default parameters
$ACTION = 'help';
$HOST_NAME = '127.0.0.1';
$USER_NAME = 'root';
$PASSWORD = '';

// Parse command line
if ($argc > 1)
	$ACTION = $argv[1];

if ($ACTION == 'help')
	die("Usage:\n\tphp query_cache_triggers [<action> [<hostname> [<username> [<password>]]]]\n");

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

// Connect to database
$link = mysql_connect($HOST_NAME, $USER_NAME, $PASSWORD)
    or die('Error: Could not connect: ' . mysql_error() . "\n");

// Make sure 'Memcached Functions for MySQL' is installed
mysql_select_db('mysql') or die("Error: Could not select 'mysql' database\n");
$query = "SELECT * FROM func WHERE name='memc_server_count'";
$result = mysql_query($query) or die('Error: Select from func table query failed: ' . mysql_error() . "\n");

if (!mysql_fetch_array($result, MYSQL_ASSOC))
{
	die("Error: 'Memcached Functions for MySQL' not installed\nNote: this script should only be run on multi-datacenter environments.\n");
}

mysql_free_result($result);

// Change database to kaltura
mysql_select_db('kaltura') or die("Error: Could not select 'kaltura' database\n");

// Make sure the memcache server is configured
$query = "SELECT memc_server_count()";
$result = mysql_query($query) or die('Error: Select memc_server_count query failed: ' . mysql_error() . "\n");

$line = mysql_fetch_array($result, MYSQL_NUM);
if (!$line)
{
	die("Unexpected: memc_server_count returned nothing\n");
}

if ($line[0] <= 0)
{
	die("Error: Memcached server not configured\n");
}

mysql_free_result($result);

// Get list of installed triggers
$triggers = array();
$query = "SHOW TRIGGERS";
$result = mysql_query($query) or die('Error: Show triggers failed: ' . mysql_error() . "\n");
for(;;)
{
        $curRes = mysql_fetch_array($result, MYSQL_ASSOC);
        if (!$curRes)
                break;
		$triggerName = $curRes["Trigger"];
		$triggerStatement = $curRes["Statement"];
		
		$triggers[$triggerName] = $triggerStatement;
}
mysql_free_result($result);

// Install / remove triggers
foreach ($INVALIDATION_KEYS as $invalidationKey)
{
	$tableName = $invalidationKey['table'];
	
	$sqlCommands = array(
		"DROP TRIGGER IF EXISTS {$tableName}_insert_memcache",
		"DROP TRIGGER IF EXISTS {$tableName}_update_memcache",
		"DROP TRIGGER IF EXISTS {$tableName}_delete_memcache",
		);
	
	if ($ACTION == 'create')
	{
		// build the invalidation keys
		$triggerBody = array();
		foreach ($invalidationKey['keys'] as $curKeyStrings)
		{
			$curKey = array("'QCI-'");
			foreach ($curKeyStrings as $curStr)
			{
				if (strpos($curStr, '@OBJ@') === false)
					$curKey[] = $curStr;
				else 
					$curKey[] = "IF($curStr IS NULL,'',$curStr)";
			}
			$curKey = 'concat(' . implode(', ', $curKey) . ')';
			
			$triggerBody[] = "DO memc_set($curKey, UNIX_TIMESTAMP(NOW()));";
		}
		
		if (count($triggerBody) > 1)
		{
			$triggerBody = 'BEGIN ' . implode(' ', $triggerBody) . ' END';
		}
		else
		{
			$triggerBody = implode(' ', $triggerBody);
		}
		
		$insertUpdateBody = str_replace('@OBJ@', 'NEW', $triggerBody);
		$deleteBody = str_replace('@OBJ@', 'OLD', $triggerBody);
		
		$insertTriggerName = "{$tableName}_insert_memcache";
		$updateTriggerName = "{$tableName}_update_memcache";
		$deleteTriggerName = "{$tableName}_delete_memcache";
		
		if (array_key_exists($insertTriggerName, $triggers) && $insertUpdateBody == $triggers[$insertTriggerName] &&
			array_key_exists($updateTriggerName, $triggers) && $insertUpdateBody == $triggers[$updateTriggerName] &&
			array_key_exists($deleteTriggerName, $triggers) && $deleteBody == $triggers[$deleteTriggerName])
		{
			print "Skipping {$tableName} - no changes detected...\n";
			continue;
		}
		
		$sqlCommands[] = "CREATE TRIGGER {$insertTriggerName} AFTER INSERT ON {$tableName} FOR EACH ROW $insertUpdateBody";
		$sqlCommands[] = "CREATE TRIGGER {$updateTriggerName} AFTER UPDATE ON {$tableName} FOR EACH ROW $insertUpdateBody";
		$sqlCommands[] = "CREATE TRIGGER {$deleteTriggerName} AFTER DELETE ON {$tableName} FOR EACH ROW $deleteBody";
				
		print "Creating triggers on {$tableName}...\n";
	}
	else
	{
		print "Removing triggers on {$tableName}...\n";
	}
	
	foreach ($sqlCommands as $sqlCommand)
	{
		$result = mysql_query($sqlCommand) or die('Error: Trigger query failed: ' . mysql_error() . "\n");
		if ($result !== true)
		{
			die("Error: Unexpected result returned from mysql_query\n");
		}
	}
}

// Close database connection
mysql_close($link);

print "Done !\n";
?>
