<?php

require_once(__DIR__ . '/../../../../../kava-utils/lib/StreamQueue.php');
require_once(__DIR__ . '/../bootstrap.php');
require_once(__DIR__ . '/playsViewsCommon.php');

define('ROOT_DIR', realpath(dirname(__FILE__) . '/../../../'));

const DEFAULT_PROM_FILE = '/etc/node_exporter/data/playsViewsElasticConsumer.prom';

class playsViewsElasticConsumer extends BaseConsumer
{
	protected function processMessage($message)
	{
		global $elasticClient, $indexConfig, $explicitPartnerIds;
		$data = json_decode($message, true);
		$entryId = $data['entry_id'];
		unset($data['entry_id']);
		if (!isset($data['partner_id']))
		{
			KalturaLog::log("Entry id [$entryId] Missing partner id - skipping");
			return;
		}
		$partnerId = $data['partner_id'];
		unset($data['partner_id']);
		if (!empty($explicitPartnerIds) && !in_array($partnerId, $explicitPartnerIds))
		{
			return;
		}
		
		$doc = array_map('intval', $data);
		$indexName = isset($indexConfig[$partnerId]) ? $indexConfig[$partnerId] : ElasticIndexMap::ELASTIC_ENTRY_INDEX;
		$index = kBaseESearch::getSplitIndexNamePerPartner($indexName, $partnerId);
		$params = array(
			'index' => $index,
			'type' => ElasticIndexMap::ELASTIC_ENTRY_TYPE,
			'id' => $entryId,
			'action' => ElasticMethodType::UPDATE,
			'body' => array(
				'retry_on_conflict' => 3,
				'doc' => $doc
			)
		);

		try
		{
			$elasticClient->addToBulk($params);
		}
		catch(Exception $e)
		{
			KalturaLog::err($e->getMessage());
		}
	}
}

/* =============================== MAIN =============================== */
// parse the command line
if ($argc < 2)
{
	echo "Usage:\n\t" . basename(__file__) . " <bulk_size>\n";
	exit(1);
}

// get the bulk size from the command line argument or use the default value
$bulkSize = is_numeric($argv[1]) ? intval($argv[1]) : 250;

// load cluster configurations
$config = kConf::get('elasticPopulateSettings', 'elastic_populate', array());
if (empty($config))
{
	$hostname = $_SERVER["HOSTNAME"] ?? gethostname();
	$configFile = ROOT_DIR . "/configurations/elastic/populate/$hostname.ini";
	
	if (!file_exists($configFile))
	{
		$message = "Configuration file [$configFile] not found";
		KalturaLog::err($message);
		writeFailure($message);
		exit(1);
	}
	
	$config = parse_ini_file($configFile);
	KalturaLog::debug("Configuration file [$configFile] loaded successfully - values: " . print_r($config, true));
}

$consumerId = $config['elasticCluster'] ?? null;	// the consumer id to use
$host = $config['elasticServer'] ?? null;			// the host endpoint
$port = $config['elasticPort'] ?? null;				// the port to connect

// add support for opensearch distribution, if 'opensearch' is set in the config we will use elastic 7+ syntax
$distribution = $config['distribution'] ?? null;
if ($distribution === elasticClient::OPENSEARCH_DISTRIBUTION)
{
	KalturaLog::debug("Found distribution config value [$distribution] - using elastic 7 syntax");
	$version = elasticClient::ELASTIC_MAJOR_VERSION_7;
}
else
{
	$version = $config['elasticVersion'] ?? null;
}

try
{
	// kConf will throw Exception if paramName not found
	$topicsPath = kConf::get(CONF_TOPICS_PATH);
	
	// validate the configuration values
	if (is_null($consumerId) || is_null($host) || is_null($port) || is_null($version))
	{
		throw new Exception("Missing configuration values: consumerId [$consumerId] host [$host], port [$port], version [$version]");
	}
}
catch (Exception $e)
{
	KalturaLog::err($e->getMessage());
	writeFailure($e);
	exit(1);
}

KalturaLog::info('Started, pid=' . getmypid());
KalturaLog::log("Starting playsViewsElasticConsumer for consumerId [$consumerId] and bulkSize [$bulkSize]");
KalturaLog::log("Elastic Client host [$host] port [$port] version [$version]");

// connect to elastic
$elasticClient = new elasticClient($host, $port, $version);
$elasticClient->setBulkSize($bulkSize);

//read dedicated indices config
$indexConfig = array();
$dedicatedEntryPartners = kConf::get(ElasticSearchPlugin::DEDICATED_ENTRY_INDEX_PARTNER_LIST,ElasticSearchPlugin::ELASTIC_DYNAMIC_MAP, array());
$dedicatedEntryPartnersIndexName = kConf::get(ElasticSearchPlugin::DEDICATED_ENTRY_INDEX_NAME,ElasticSearchPlugin::ELASTIC_DYNAMIC_MAP, ElasticIndexMap::ELASTIC_ENTRY_INDEX);
foreach ($dedicatedEntryPartners as $dedicatedEntryPartner)
{
	$indexConfig[$dedicatedEntryPartner] = $dedicatedEntryPartnersIndexName;
}

$dedicatedPartners = kConf::get(ElasticSearchPlugin::DEDICATE_INDEX_PARTNER_LIST, ElasticSearchPlugin::ELASTIC_DYNAMIC_MAP, array());
foreach ($dedicatedPartners as $partnerId => $indices)
{
	$indices = explode(',', $indices);
	$indices = array_map('trim', $indices);
	if (in_array(ElasticIndexMap::ELASTIC_ENTRY_INDEX, $indices))
	{
		$indexConfig[$partnerId] = ElasticIndexMap::ELASTIC_ENTRY_INDEX . '_' . $partnerId;
	}
}

$explicitPartnerIds = array();
$explicitPartnerIdsString = kConf::get('explicitPartnerIds', ElasticSearchPlugin::ELASTIC_DYNAMIC_MAP, null);
if ($explicitPartnerIdsString)
{
        $explicitPartnerIdsArray = explode(',', $explicitPartnerIdsString);
        $explicitPartnerIds = array_map('trim', $explicitPartnerIdsArray);
}

//When loading the server bootstrap it disables the stream wrappers, we need to enable it back for the s3Wrapper to be able to fetch files form s3
if (!array_intersect(array('https', 'http'), stream_get_wrappers()))
{
	stream_wrapper_restore('http');
	stream_wrapper_restore('https');
}

$consumer = new playsViewsElasticConsumer($topicsPath, PLAYSVIEWS_TOPIC, $consumerId);

//uncomment if rate limit is needed
//$consumer->setMessagesPerSecond(100);
$consumer->consumeQueue();

//flush if bulk is not empty
try
{
	$response = $elasticClient->flushBulk();
}
catch(Exception $e)
{
	KalturaLog::err($e->getMessage());
	writeFailure($e);
}

writeSuccess();
KalturaLog::log('Info: done');

/* =============================== FUNCTIONS =============================== */
function writeSuccess($filePath = null): void
{
	$filePath = $filePath ?? DEFAULT_PROM_FILE;
	createDirPath($filePath);
	
	$description = 'Successfully finished playsViewsElasticConsumer.php script';
	$timestamp = time();
	$date = date("Y-m-d H:i:s", $timestamp);
	$hostname = gethostname();
	$data = "plays_views_elastic_consumer{timestamp=\"$date\", host=\"$hostname\", description=\"$description\", success=\"true\"} $timestamp" . PHP_EOL;
	
	file_put_contents($filePath, $data, LOCK_EX);
}

function writeFailure($e, $filePath = null): void
{
	$filePath = $filePath ?? DEFAULT_PROM_FILE;
	createDirPath($filePath);
	
	$description = 'Error in playsViewsElasticConsumer.php script';
	$timestamp = time();
	$date = date("Y-m-d H:i:s", $timestamp);
	$hostname = gethostname();
	$message = $e instanceof Exception ? $e->getMessage() : $e;
	$code = $e instanceof Exception ? $e->getCode() : 0;
	$data = "plays_views_elastic_consumer{timestamp=\"$date\", host=\"$hostname\", description=\"$description\", success=\"false\", message=\"$message\", code=\"$code\"} $timestamp" . PHP_EOL;
	
	file_put_contents($filePath, $data, LOCK_EX);
}

function createDirPath($filePath): void
{
	$dirPath = dirname($filePath);
	if (!is_dir($dirPath))
	{
		mkdir($dirPath, 0775, true);
	}
}
