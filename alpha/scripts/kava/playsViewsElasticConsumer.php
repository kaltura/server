<?php

require_once(__DIR__ . '../../../../../kava-utils/lib/StreamQueue.php');
require_once(__DIR__ . '/../bootstrap.php');
require_once(__DIR__ . '/playsViewsCommon.php');

class
playsViewsElasticConsumer extends BaseConsumer
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

// parse the command line
if ($argc < 3)
{
	echo "Usage:\n\t" . basename(__file__) . " <consumerId> <bulk_size>\n";
	exit(1);
}

$consumerId = $argv[1];
$bulkSize = $argv[2];

try
{
	$topicsPath = kConf::get(CONF_TOPICS_PATH);
}
catch (Exception $ex)
{
	errorLog('Missing topics path config');
	exit(1);
}

writeLog('Info: started, pid=' . getmypid());

// connect to elastic
$elasticClient = new elasticClient();
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
}
writeLog('Info: done');
