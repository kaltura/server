<?php
class TestingHelpers
{
	public static function getPartnerForTest()
	{
		$partnerName = "Unit Test Partner";
		
		$c = new Criteria();
		$c->add(PartnerPeer::PARTNER_NAME, $partnerName);
		
		$partner = PartnerPeer::doSelectOne($c);
		
		if (!$partner)
		{
			$partnerRegistration = new myPartnerRegistration();
			$partner = $partnerRegistration->initNewPartner($partnerName, $partnerName, "unittest@kaltura.com", "commercial_use", "yes", $partnerName, $partnerName);
		}
		
		return $partner;
	}
	
	public static function getNormalKs($partnerId, $userId)
	{
		$partner = PartnerPeer::retrieveByPK($partnerId);
		$ks = "";
		$privileges = "";
		kSessionUtils::startKSession($partnerId, $partner->getSecret(), $userId, $ks, 86400, 0, "", $privileges);
		return $ks;
	}
	
	public static function getAdminKs($partnerId, $userId)
	{
		$partner = PartnerPeer::retreiveByPK($partnerId);
		$ks = "";
		$privileges = "";
		kSessionUtils::startKSession($partnerId, $partner->getAdminSecret(), $userId, $ks, 86400, 2, "", $privileges);
		return $ks;
	}
}
?>