<?php

$realrun = false;

if($argc != 2)
{
	echo "Arguments missing.\n\n";
	echo "Usage: php 2016_04_05_migrate_tvinci_distribution_tags_to_be_dynamic.php {partner id}\n";
	exit;
}
require_once (__DIR__ . '/../../bootstrap.php');
$partnerId = $argv[1];


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
	$ismFileName = $distributionProfile->getFromCustomData('ismFileName', null, '');
	$ismPpvmodule = $distributionProfile->getFromCustomData('ismPpvModule', null, '');
	if ( $ismFileName != '' || $ismPpvmodule != '' )
	{
		$tag = createTvinciTag("ism", $ismFileName, $ismPpvmodule);
		$tags[] = $tag;
	}

	$ipadnewFileName = $distributionProfile->getFromCustomData('ipadnewFileName', null, '');
	$ipadnewPpvmodule = $distributionProfile->getFromCustomData('ipadnewPpvModule', null, '');
	if ( $ipadnewFileName != '' || $ipadnewPpvmodule != '' )
	{
		$tag = createTvinciTag("ipadnew", $ipadnewFileName, $ipadnewPpvmodule);
		$tags[] = $tag;
	}

	$iphonenewFileName = $distributionProfile->getFromCustomData('iphonenewFileName', null, '');
	$iphonenewPpvmodule = $distributionProfile->getFromCustomData('iphonenewPpvModule', null, '');
	if ( $iphonenewFileName != '' || $iphonenewPpvmodule != '' )
	{
		$tag = createTvinciTag("iphonenew", $iphonenewFileName, $iphonenewPpvmodule);
		$tags[] = $tag;
	}

	$mbrFileName = $distributionProfile->getFromCustomData('mbrFileName', null, '');
	$mbrPpvmodule = $distributionProfile->getFromCustomData('mbrPpvModule', null, '');
	if ( $mbrFileName != '' || $mbrPpvmodule != '' )
	{
		$tag = createTvinciTag("mbr", $mbrFileName, $mbrPpvmodule);
		$tags[] = $tag;
	}

	$dashFileName = $distributionProfile->getFromCustomData('dashFileName', null, '');
	$dashPpvmodule = $distributionProfile->getFromCustomData('dashPpvModule', null, '');
	if ( $dashFileName != '' || $dashPpvmodule != '' )
	{
		$tag = createTvinciTag("dash", $dashFileName, $dashPpvmodule);
		$tags[] = $tag;
	}

	$widevineFileName = $distributionProfile->getFromCustomData('widevineFileName', null, '');
	$widevinePpvmodule = $distributionProfile->getFromCustomData('widevinePpvModule', null, '');
	if ( $widevineFileName != '' || $widevinePpvmodule != '' )
	{
		$tag = createTvinciTag("widevine", $widevineFileName, $widevinePpvmodule);
		$tags[] = $tag;
	}

	$widevine_mbrFileName = $distributionProfile->getFromCustomData('widevineMbrFileName', null, '');
	$widevine_mbrPpvmodule = $distributionProfile->getFromCustomData('widevineMbrPpvModule', null, '');
	if ( $widevine_mbrFileName != '' || $widevine_mbrPpvmodule != '' )
	{
		$tag = createTvinciTag("widevine_mbr", $widevine_mbrFileName, $widevine_mbrPpvmodule);
		$tags[] = $tag;
	}
	
	$distributionProfile->putInCustomData('tags',$tags);
	KalturaLog::debug("noam [".print_r($distributionProfile,true)."]");
	if ($realrun)
	{
		$distributionProfile->save();
	}
}


function createTvinciTag($tagname, $filename, $ppvModuleName)
{
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
			$newTag->setFormat('applehttp');
			$newTag->setProtocol('http');
			$newTag->setExtension('m3u8');
			break;
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
			$newTag->setFormat('url');
			$newTag->setProtocol('http');
			$newTag->setExtension('wvm');
			break;
		case 'widevine_mbr':
			$newTag->setFormat('url');
			$newTag->setProtocol('http');
			$newTag->setExtension('wvm');
			break;
	}
	return $newTag;
}

?>