<?php
require_once 'createEntryObject.php';

define('DEBUG', true);
define('DRY_RUN', true);
// Path, width, height, bitrate, duration,framerate,container,codec
define('SOURCE_CSV_FILEPATH_COL_INDEX', 0);
define('SOURCE_CSV_WIDTH_COL_INDEX', 1);
define('SOURCE_CSV_HEIGHT_COL_INDEX', 2);
define('SOURCE_CSV_BITRATE_COL_INDEX', 3);
define('SOURCE_CSV_DURATION_COL_INDEX', 4);
define('SOURCE_CSV_FRAMERATE_COL_INDEX', 5);
define('SOURCE_CSV_CONTAINER_COL_INDEX', 6);
define('SOURCE_CSV_CODEC_COL_INDEX', 7);

define('CONVERTED_FLAVOR_PARAMS_ID', 51422);

$partnerId = @$argv[1];
try
{
    $createEntry = new createEntryObject($partnerId);
}
catch(Exception $ex)
{
    echo_usage();
    die($ex->getMessage().PHP_EOL);
}

$metadata_csv_path = @$argv[2];
$isEntryDump = false;
if(substr_count($metadata_csv_path, '--entry-dump'))
{
    $isEntryDump = true;
    $arg2_exploded = explode('=', $metadata_csv_path);
    $metadata_csv_path = $arg2_exploded[1];
}
if(!$metadata_csv_path || !file_exists($metadata_csv_path))
{
    echo_usage();
    die('must supply metadata CSV file path'.PHP_EOL);
}

$download_csv_path = @$argv[3];
if(!$download_csv_path || !file_exists($download_csv_path))
{
    echo_usage();
    die('must supply source flavors CSV file path'.PHP_EOL);
}

$qtmp4_csv_path = @$argv[4];
if(!$qtmp4_csv_path || !file_exists($qtmp4_csv_path))
{
    echo_usage();
    die('must supply second flavor CSV file path'.PHP_EOL);
}

$startId = @$argv[5];

$entries = array();

$metadataFile = fopen($metadata_csv_path, 'r');
while(!feof($metadataFile))
{
    $row = fgetcsv($metadataFile);
    if(is_array($row))
    {
	if(!$isEntryDump)
	{
	    $url = $row[3];
	}
	else
	{
	    $url = $row[1];
	}
	$embedCodeId = extractEmbedIdFromUrl($url);
	if($embedCodeId === FALSE)
	{
	    echo 'malformed URL, could not extract embed code ['.$row.']'.PHP_EOL;
	    continue;
	}
	$entries[$embedCodeId] = $row;
    }
}

fclose($metadataFile);

$downloads = array();
$downloadFile = fopen($download_csv_path, 'r');
while(!feof($downloadFile))
{
    $row = fgetcsv($downloadFile);
    if(is_array($row))
    {
	$file_name = pathinfo($row[0], PATHINFO_FILENAME);
	$downloads[$file_name] = $row;
    }
}

$qtmp4s = array();
$qtmp4File = fopen($qtmp4_csv_path, 'r');
while(!feof($qtmp4File))
{
    $row = fgetcsv($qtmp4File);
    if(is_array($row))
    {
	$file_name = pathinfo(pathinfo($row[0], PATHINFO_FILENAME), PATHINFO_BASENAME);
	$qtmp4s[$file_name] = $row;
    }
}

foreach($entries as $embedId => $entry)
{
    if($startId && $embedId != $startId)
    {
	echo "not starting from embed $embedId when requested $startId \n";
	continue;
    }
    if(!array_key_exists($embedId, $downloads))
    {
	echo "entry $embedId does not exist in source files array, skipping...".PHP_EOL;
	continue;
    }
    if(!$isEntryDump)
    {
	$sourceFileRow = $downloads[$embedId];
	$convertedRow = @$qtmp4s[$embedId];
	
	$name = $entry[0];
	$desc = $entry[1];
	$fileName = $sourceFileRow[SOURCE_CSV_FILEPATH_COL_INDEX];
	$conversionProfile = $entry[5];
	$tags = $entry[2];
	$categories = $entry[7];
	$thumbUrl = $entry[10];
	$width = $sourceFileRow[SOURCE_CSV_WIDTH_COL_INDEX];
	$height = $sourceFileRow[SOURCE_CSV_HEIGHT_COL_INDEX];
	$duration = $sourceFileRow[SOURCE_CSV_DURATION_COL_INDEX];
	
	if(DEBUG) var_dump($name, $desc, $fileName, $conversionProfile, $tags, $categories, $thumbUrl, $width, $height, $duration);
	if(!DRY_RUN) $entryObj = $createEntry->createEntry($name, $desc, $fileName, $conversionProfile, $tags, $categories, $thumbUrl, $width, $height, $duration);
	
	$bitrate = $sourceFileRow[SOURCE_CSV_BITRATE_COL_INDEX];
	$frameRate = $sourceFileRow[SOURCE_CSV_FRAMERATE_COL_INDEX];
	$container = $sourceFileRow[SOURCE_CSV_CONTAINER_COL_INDEX];
	$codec = $sourceFileRow[SOURCE_CSV_CODEC_COL_INDEX];
	$flavor_tags = (is_array($convertedRow) && count($convertedRow))? '': 'web,mbr';
	if(DEBUG) var_dump($fileName, 1, $entryObj, 0, $width, $height, $bitrate, $frameRate, $flavor_tags, $container, $codec);
	if(!DRY_RUN) $createEntry->createFlavorAsset($fileName, 1, $entryObj, 0, $width, $height, $bitrate, $frameRate, $flavor_tags, $container, $codec);
    }
    else
    {
	$convertedRow = @$qtmp4s[$embedId];
	$entryObj = entryPeer::retrieveByPK($entry[0]);
	if(!$entryObj)
	{
	    echo 'working from entry dump but could not retrieve entry '.$entry[0].', skipping...'.PHP_EOL;
	    continue;
	}
    }

    if(!is_array($convertedRow) || !count($convertedRow)) continue;

    $qtmp4_filename = $convertedRow[SOURCE_CSV_FILEPATH_COL_INDEX];
    $flavorParamsId = CONVERTED_FLAVOR_PARAMS_ID;
    $width2 = $convertedRow[SOURCE_CSV_WIDTH_COL_INDEX];
    $height2 = $convertedRow[SOURCE_CSV_HEIGHT_COL_INDEX];
    $bitrate2 = $convertedRow[SOURCE_CSV_BITRATE_COL_INDEX];
    $frameRate2 = $convertedRow[SOURCE_CSV_FRAMERATE_COL_INDEX];
    $container2 = $convertedRow[SOURCE_CSV_CONTAINER_COL_INDEX];
    $codec2 = $convertedRow[SOURCE_CSV_CODEC_COL_INDEX];
    $flavor_tags2 = 'web,mbr';
    if(DEBUG) var_dump($qtmp4_filename, 0, $entryObj, $flavorParamsId, $width2, $height2, $bitrate2, $frameRate2, $flavor_tags2, $container2, $codec2);
    if(!DRY_RUN) $createEntry->createFlavorAsset($qtmp4_filename, 0, $entryObj, $flavorParamsId, $width2, $height2, $bitrate2, $frameRate2, $flavor_tags2, $container2, $codec2);
    
    
}

function extractEmbedIdFromUrl($url)
{
    $q = parse_url($url,PHP_URL_QUERY);
    if($q)
    {
	$qa = explode('&', $q);
	foreach($qa as $pair)
	{
	    $pairx = explode('=',$pair);
	    if($pairx[0] == 'embed_code')
	    {
		return $pairx[1];
	    }
	}
    }
    else
    {
	// try from pattern http://pa-admin.kaltura.com:8080/Desimad/Zpc2hoMTpuh5bOT3U31T91gNroUxN6M0
	$last_slash = strrpos($url, '/');
	$id = substr($url, $last_slash+1);
	if($id && strlen($id) == 32) return $id;
    }
    return FALSE;
}

function echo_usage()
{
    echo 'usage: php '.$_SERVER['SCRIPT_NAME'].' {partner_id} {[--entry-dump=]bulkUpload CSV path} {source files CSV path} {second flavor CSV path} [start ID]'.PHP_EOL;
}
