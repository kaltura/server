<?php
/**
 * Migrates you-tube distribution-profiles to use numeric category ID
 */

require_once (__DIR__ . '/../../bootstrap.php');
require_once KALTURA_ROOT_PATH.'/vendor/google-api-php-client-1.1.2/src/Google/autoload.php'; 

$partnerId = null;
if(isset($argv[1]) && is_numeric($argv[1]))
{
	$partnerId = intval($argv[1]);
}

		
$oldCategories = array(
	'Film' => 'Film & Animation',
	'Autos' => 'Autos & Vehicles',
	'Music' => 'Music',
	'Animals' => 'Pets & Animals',
	'Sports' => 'Sports',
	'Travel' => 'Travel & Events',
	'Games' => 'Gaming',
	'Comedy' => 'Comedy',
	'People' => 'People & Blogs',
	'News' => 'News & Politics',
	'Entertainment' => 'Entertainment',
	'Education' => 'Education',
	'Howto' => 'Howto & Style',
	'Nonprofit' => 'Nonprofits & Activism',
	'Tech' => 'Science & Technology',
);

$appId = YoutubeApiDistributionPlugin::GOOGLE_APP_ID;
$authConfig = kConf::get($appId, 'google_auth', null);

$googleClientId = isset($authConfig['clientId']) ? $authConfig['clientId'] : null;
$googleClientSecret = isset($authConfig['clientSecret']) ? $authConfig['clientSecret'] : null;

$options = array(
	CURLOPT_VERBOSE => true,
	CURLOPT_STDERR => STDOUT,
);
	
$client = new Google_Client();
$client->getIo()->setOptions($options);
$client->setClientId($googleClientId);
$client->setClientSecret($googleClientSecret);


$distributionProvider = YoutubeApiDistributionPlugin::getDistributionProviderTypeCoreValue(YoutubeApiDistributionProviderType::YOUTUBE_API);

$criteria = new Criteria();
$criteria->add(DistributionProfilePeer::STATUS, DistributionProfileStatus::DELETED, Criteria::NOT_EQUAL);
$criteria->add(DistributionProfilePeer::PROVIDER_TYPE, $distributionProvider);

if($partnerId)
{
	$criteria->add(DistributionProfilePeer::PARTNER_ID, $partnerId);
}

$criteria->addAscendingOrderByColumn(DistributionProfilePeer::ID);
$criteria->setLimit(100);

$demoCategories = null;
$distributionProfiles = DistributionProfilePeer::doSelect($criteria);
while($distributionProfiles){
	$lastId = 0;
	foreach($distributionProfiles as $distributionProfile)
	{
		/* @var $distributionProfile YoutubeApiDistributionProfile */
		
		$lastId = $distributionProfile->getId();
		$url = $distributionProfile->getApiAuthorizeUrl();
		if($url)
		{
			KalturaLog::warning("Partner [" . $distributionProfile->getPartnerId() . "] Distribution-Profile [$lastId] not authorized");
			continue;
		}
		
		if(!isset($oldCategories[$distributionProfile->getDefaultCategory()]))
		{
			KalturaLog::warning("Partner [" . $distributionProfile->getPartnerId() . "] Distribution-Profile [$lastId] has no match to the category [" . $distributionProfile->getDefaultCategory() . "]");
			continue;
		}
		$oldCategory = $oldCategories[$distributionProfile->getDefaultCategory()];
	
		$client->setAccessToken(str_replace('\\', '', json_encode($distributionProfile->getGoogleOAuth2Data())));
		
		if($demoCategories && $distributionProfile->getUsername() == 'demodistro')
		{
			$categories = $demoCategories;
		}
		else
		{
			try{
				$youtube = new Google_Service_YouTube($client);
				$categoriesListResponse = $youtube->videoCategories->listVideoCategories('id,snippet', array('regionCode' => 'us'));
				$categories = array();
				foreach($categoriesListResponse->getItems() as $category)
				{
					$categories[$category['snippet']['title']] = $category['id'];
				}
				if($distributionProfile->getUsername() == 'demodistro')
				{
					$demoCategories = $categories;
				}
			}
			catch (Exception $e)
			{
				KalturaLog::err($e);
				continue;
			}
		}
	
		if(!isset($categories[$oldCategory]))
		{
			KalturaLog::warning("Partner [" . $distributionProfile->getPartnerId() . "] Distribution-Profile [$lastId] old category [$oldCategory] not found");
			continue;
		}
		
		$distributionProfile->setDefaultCategory($categories[$oldCategory]);
		$distributionProfile->save();
	}
	
	$criteria->add(DistributionProfilePeer::ID, $lastId, Criteria::GREATER_THAN);
	$distributionProfiles = DistributionProfilePeer::doSelect($criteria);
}

