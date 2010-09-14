<?php
class BatchTestsHelpers
{
	private static $_defaultUserId = 'BatchUnitTestUser';
	private static $email = 'batch@kaltura.com';
	private static $admin_name = 'batch admin';
	private static $partner_name = 'batch partner';
	private static $description = 'Build-in partner - used for batch operations';
		
	public static function getBatchPartner()
	{
		return PartnerPeer::retrieveByPK(Partner::BATCH_PARTNER_ID);
	}
	
	public static function getBatchAdmin()
	{
		$c = new Criteria();
		$c->add(adminKuserPeer::PARTNER_ID, Partner::BATCH_PARTNER_ID);
		
		return adminKuserPeer::doSelectOne($c);
	}
	
	public static function getBatchAdminKs()
	{
        $partner = self::getBatchPartner();
		$privileges = "";
		
	    $ks = "";
		kSessionUtils::startKSession(Partner::BATCH_PARTNER_ID, $partner->getAdminSecret(), self::$_defaultUserId, $ks, 86400, 2, "", $privileges);
		return $ks;
	}
}