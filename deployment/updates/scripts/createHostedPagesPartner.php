<?php
chdir(__DIR__ . '/../../');
require_once 'bootstrap.php';

function generateSecret ()
{
	$minlength = 5;
	$maxlength = 10;
	$useupper = true;
	$usespecial = false;
	$usenumbers = true;
	
	$charset = "abcdefghijklmnopqrstuvwxyz";
	if($useupper)
		$charset .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	
	if($usenumbers)
		$charset .= "0123456789";
	
	if($usespecial)
		$charset .= "~@#$%^*()_+-={}|]["; // Note: using all special characters this reads: "~!@#$%^&*()_+`-={}|\\]?[\":;'><,./";
	

	if($minlength > $maxlength)
		$length = mt_rand($maxlength, $minlength);
	else
		$length = mt_rand($minlength, $maxlength);
	
	$key = "";
	for($i = 0; $i < $length; $i++)
		$key .= $charset[(mt_rand(0, (strlen($charset) - 1)))];
		
	return md5($key);
}


$partner = new Partner();
$partner->setId(-3);
$partner->setPartnerName('Hosted Pages');
$partner->setPartnerAlias('Hosted Pages');
$partner->setDescription('Build-in partner - used for hosted pages');
$partner->setSecret(generateSecret());
$partner->setAdminSecret(generateSecret());
$partner->setMaxNumberOfHitsPerDay(-1);
$partner->setAppearInSearch(mySearchUtils::DISPLAY_IN_SEARCH_NONE);
$partner->setInvalidLoginCount(0);
$partner->setKsMaxExpiryInSeconds(86400);
$partner->setCreateUserOnDemand(false);
$partner->setCommercialUse(false);
$partner->setModerateContent(false);
$partner->setNotify(false);
$partner->setIsFirstLogin(true);
$partner->setAdminLoginUsersQuota(0);
$partner->setStatus(Partner::PARTNER_STATUS_ACTIVE);
$partner->setType(PartnerGroupType::PUBLISHER);

$criteria = $partner->buildCriteria(); 
$criteria->setDbName(PartnerPeer::DATABASE_NAME);

$con = Propel::getConnection(PartnerPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
$id = BasePeer::doInsert($criteria, $con);

echo "Created partner [$id]\n";
