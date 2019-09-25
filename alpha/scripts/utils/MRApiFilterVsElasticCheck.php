<?php
require_once(dirname(__FILE__).'/../bootstrap.php');
define("SCRIPT_PATH", dirname(__FILE__). "/../../../");
require_once(SCRIPT_PATH . 'alpha/lib/interfaces/IApiObject.php');
require_once(SCRIPT_PATH . 'batch/client/KalturaClient.php');
require_once(SCRIPT_PATH . 'batch/client/KalturaPlugins/KalturaScheduledTaskClientPlugin.php');
require_once(SCRIPT_PATH . 'api_v3/lib/types/IKalturaEnum.php');
require_once(SCRIPT_PATH . 'api_v3/lib/types/KalturaEnum.php');
require_once(SCRIPT_PATH . 'api_v3/lib/types/KalturaStringEnum.php');
require_once(SCRIPT_PATH . 'api_v3/lib/types/IKalturaDynamicEnum.php');
require_once(SCRIPT_PATH . 'api_v3/lib/types/KalturaDynamicEnum.php');
require_once(SCRIPT_PATH . 'api_v3/lib/exceptions/KalturaAPIException.php');
require_once(SCRIPT_PATH . 'api_v3/lib/KalturaErrors.php');

function createMrKs($partnerId)
{
	$puserId = 'batchUser';
	$privileges = 'disableentitlement';
	$sessionType = KalturaSessionType::ADMIN;
	return kSessionUtils::createKSession($partnerId, null, $puserId, 86400, $sessionType, $privileges);
}

function getResultFromElastic($filter)
{
	$entryQueryToFilterESearch = new ESearchEntryQueryFromFilter();
	$pager = new kPager();
	list ($currEntryIds, $count) = $entryQueryToFilterESearch->retrieveElasticQueryEntryIds($filter, $pager);
	echo 'Entries count from elastic ' . $count . PHP_EOL;
	return $count;
}

function getResultFromApi($ks, $profileId)
{
	$config = new KalturaConfiguration(-2);
	$config->serviceUrl = "localhost";
	$client = new KalturaClient($config);
	$client->setKs($ks);
	$scheduledTaskClient = KalturaScheduledTaskClientPlugin::get($client);
	$profile = $scheduledTaskClient->scheduledTaskProfile->get($profileId);
	$pager = new KalturaFilterPager();
	$result =  $client->baseEntry->listAction($profile->objectFilter, $pager);
	echo 'Entries count from api ' . $result->totalCount . PHP_EOL;
	return $result->totalCount;
}

if($argc < 2){
	echo "Missing arguments.\n";
	echo "php $argv[0] {MR profile Id}.\n";
	die;
}


$profileId = $argv[1];
echo 'Retrieving schedule task id ' . $profileId . PHP_EOL;
$dbScheduledTaskProfile = ScheduledTaskProfilePeer::retrieveByPK($profileId);
if(!$dbScheduledTaskProfile)
{
	echo 'failed to retrieve schedule task id ' . $profileId . PHP_EOL;
	die;
}

$partnerId = $dbScheduledTaskProfile->getPartnerId();
$ks = createMrKs($partnerId)->toSecureString();
kCurrentContext::initKsPartnerUser($ks);
myPartnerUtils::applyPartnerFilters($partnerId);
if(getResultFromApi($ks, $profileId) == getResultFromElastic($dbScheduledTaskProfile->getObjectFilter()))
{
	echo "Profile {$profileId} api vs elastic same results\n";
}
else
{
	echo "Profile {$profileId} api vs elastic different results\n";
}
