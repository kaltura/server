<?php

$realrun = false;

if($argc < 2)
{
	echo "Arguments missing.\n\n";
	echo "Usage: php 2016_04_05_migrate_tvinci_distribution_tags_to_be_dynamic.php {partner id}\n";
	exit;
}
require_once (__DIR__ . '/../../bootstrap.php');
$partnerId = $argv[1];
$realrun = isset($argv[2]) && $argv[2] == 'realrun';

$c = new Criteria();
$c->add(DistributionProfilePeer::PARTNER_ID, $partnerId);
$c->add(DistributionProfilePeer::STATUS, DistributionProfileStatus::ENABLED);
$c->add(DistributionProfilePeer::PROVIDER_TYPE, TvinciDistributionPlugin::getDistributionProviderTypeCoreValue(TvinciDistributionProviderType::TVINCI));
$distributionProfiles = DistributionProfilePeer::doSelect($c, $con);

foreach ($distributionProfiles as $distributionProfile)
{
	/**
	 * @var TvinciDistributionProfile $distributionProfile
	 */
	$tags = array();
	$tag = createTvinciTag("ism", 'ismFileName', 'ismPpvModule', $distributionProfile);
	if (!is_null($tag))
		$tags[] = $tag;

	$tag = createTvinciTag("ipadnew", 'ipadnewFileName', 'ipadnewPpvModule', $distributionProfile);
	if (!is_null($tag))
		$tags[] = $tag;

	$tag = createTvinciTag("iphonenew", 'iphonenewFileName', 'iphonenewPpvModule', $distributionProfile);
	if (!is_null($tag))
		$tags[] = $tag;

	$tag = createTvinciTag("mbr", 'mbrFileName', 'mbrPpvModule', $distributionProfile);
	if (!is_null($tag))
		$tags[] = $tag;

	$tag = createTvinciTag("dash", 'dashFileName', 'dashPpvModule', $distributionProfile);
	if (!is_null($tag))
		$tags[] = $tag;

	$tag = createTvinciTag("widevine", 'widevineFileName', 'widevinePpvModule', $distributionProfile);
	if (!is_null($tag))
		$tags[] = $tag;

	$tag = createTvinciTag("widevine_mbr", 'widevineMbrFileName', 'widevineMbrPpvModule', $distributionProfile);
	if (!is_null($tag))
		$tags[] = $tag;

	$distributionProfile->putInCustomData('tags',$tags);
	KalturaLog::debug("noam [".print_r($distributionProfile,true)."]");
	if ($realrun)
	{
		$distributionProfile->save();
	}
}


function createTvinciTag($tagname, $customDataFileName, $customDataPpvModule, $distributionProfile)
{
	$filename = trim($distributionProfile->getFromCustomData($customDataFileName, null, ''));
	$ppvModuleName = trim($distributionProfile->getFromCustomData($customDataPpvModule, null, ''));
	if ( $filename == '' && $ppvModuleName == '' )
	{
		return null;
	}
	
	$newTag = new TvinciDistributionTag();
	$newTag->setTagname($tagname);
	$newTag->setFilename($filename);
	$newTag->setPpvmodule($ppvModuleName);

	switch ($tagname)
	{
		case 'ism':
			$newTag->setFormat('sl');
			$newTag->setProtocol('http');
			$newTag->setExtension('ism');
			break;
		case 'ipadnew':
		case 'iphonenew':
			$newTag->setFormat('applehttp');
			$newTag->setProtocol('http');
			$newTag->setExtension('m3u8');
			break;
		case 'mbr':
			$newTag->setFormat('hdnetworkmanifest');
			$newTag->setProtocol('http');
			$newTag->setExtension('a4m');
			break;
		case 'dash':
			$newTag->setFormat('mpegdash');
			$newTag->setProtocol('http');
			$newTag->setExtension('mpd');
			break;
		case 'widevine':
		case 'widevine_mbr':
			$newTag->setFormat('url');
			$newTag->setProtocol('http');
			$newTag->setExtension('wvm');
			break;
	}
	return $newTag;
}

?>