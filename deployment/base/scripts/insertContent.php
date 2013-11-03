<?php
/**
 * @package deployment
 * @subpackage base.permissions
 *
 * Adds all system default permissions
 */

chdir(__DIR__);
require_once(__DIR__ . '/../../bootstrap.php');

myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_MASTER;

$dbConf = kConf::getDB();
$dsn = $dbConf['datasources']['propel']['connection']['dsn'];
$dsn = explode(":", $dsn);
$dsnArray = explode(";", $dsn[1]);

// init with default port
$dbParams = $dbConf['datasources']['propel']['connection'];
$dbParams['port'] = '3306';

foreach($dsnArray as $param)
{
	$items = explode("=", $param);
	if(count($items) == 2)
		$dbParams[$items[0]] = $items[1];
}

foreach($dbParams as $key => $value)
{
	echo $key . "=>" . $value . "\n";
}

$deployedScripts = getDeployedScripts($dbParams);

$dirPath = __DIR__ . '/init_content';
$scriptPath = realpath(__DIR__ . '/../../../') . '/tests/standAloneClient/exec.php';

KalturaLog::info("Adding content from directory [$dirPath]");
$dir = dir($dirPath);
/* @var $dir Directory */


$fileNames = array();
while (false !== ($fileName = $dir->read()))
{
	if (!in_array ($fileName, array_keys($deployedScripts)))
	{
		$filePath = realpath("$dirPath/$fileName");
		if($fileName[0] == '.' || is_dir($filePath) || preg_match('/template.xml$/', $fileName))
			continue;
	
		$fileNames[] = $fileName;
	}
}
$dir->close();

sort($fileNames);
KalturaLog::info("Handling files [" . print_r($fileNames, true) . "]");


$returnValue = null;
foreach($fileNames as $fileName)
{
	$filePath = realpath("$dirPath/$fileName");
	KalturaLog::info("Adding content from file [$filePath]");
	passthru("php $scriptPath $filePath", $returnValue);
	if($returnValue !== 0)
		exit(-1);
		
	saveScriptAsRun ($fileName);
}


function getDeployedScripts(array $dbParams)
{
	$link = mysql_connect($dbParams['host'] . ':' . $dbParams['port'], $dbParams['user'], $dbParams['password'], null);
	
	$db_selected = mysql_select_db($dbParams['dbname'], $link);
	$result = mysql_query('select filename from version_management');
	if($result)
	{
		$res = array();
		
		while($row = mysql_fetch_assoc($result))
		{
			$res[$row['filename']] = true;
		}
	}
	
	mysql_free_result($result);
	mysql_close($link);
	return $res;
}

function saveScriptAsRun (array $dbParams, $fileName)
{
	$link = mysql_connect($dbParams['host'] . ':' . $dbParams['port'], $dbParams['user'], $dbParams['password'], null);
	$db_selected = mysql_select_db($dbParams['dbname'], $link);
	$result =   mysql_query("insert into version_management (version, filename, created_at) values ('7000', '$fileName', '". time() ."')");
	
	if ($result)
		KalturaLog::info ("$fileName saved as run");
	
	mysql_free_result($result);
	mysql_close($link);
}

