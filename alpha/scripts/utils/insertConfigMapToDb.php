<?php
chdir(__DIR__.'/../');
require_once(__DIR__ . '/../bootstrap.php');

if($argc < 7)
	die ("\nUsage : $argv[0] <db user name> <db password> <map name> <host name> <ini file> <justification> [status]\n".
		"<db user name> - user with write permissions\n".
		"<db password> - pw of the ralted user\n".
		"<map name> - name of the map \n".
		"<host name> - host name regex (# instead of *) \n".
		"<ini file> - path to the ini file contaitning the configuration \n".
		"<justification> - must explain why was it added and by who \n".
		"[status] - 1 for enable , 0 for disabe , if not specified it will be disabled.\n\n"	);

$dbUserName 	= $argv[1];
$dbPasssword 	= $argv[2];
$rawMapName 	= $argv[3];
$hostNameRegEx 	= $argv[4];
$iniFilePath 	= $argv[5];
$justification 	= $argv[6];
$status = isset($argv[7]) ? $argv[7] : 0;

if(empty($rawMapName))
	die("\nMap name - must have value, aborting.\n");

//read ini file
if(!file_exists($iniFilePath))
	die("File {$iniFilePath} not found.");

$iniFileStr = new Zend_Config_Ini($iniFilePath);
$iniFileArr = $iniFileStr->toArray();
$iniFileJson = json_encode($iniFileArr);


//get latest version of the map from db
$cmdLine = 'mysql -u'.$dbUserName.' -p'.$dbPasssword.' kaltura -e "select version from conf_maps where conf_maps.map_name=\''.$rawMapName.'\' and conf_maps.host_name=\''.$hostNameRegEx.'\' order by version desc limit 1 ;"';

echo "executing: {$cmdLine}\n";
exec($cmdLine, $output1);
if($output1)
	print_r($output1);
$version = isset($output1[1]) ? $output1[1]+1 : 0;
print("Found version - {$version}\r\n");
$iniFileJson = str_replace('\/','/',$iniFileJson);
$iniFileJson = str_replace('"','\"',$iniFileJson);
//insert new map to db
$cmdLine = "mysql  -u$dbUserName -p$dbPasssword kaltura -e  \"insert into conf_maps (map_name,host_name,status,version,created_at,remarks,content)values('$rawMapName','$hostNameRegEx',$status,$version,'".date("Y-m-d H:i:s")."','$justification','$iniFileJson');\"";
echo "executing: {$cmdLine}\n";
exec($cmdLine, $output2);
if($output2)
	print_r($output2);


