<?php
/**
 * @package plugins.systemPartner
 * @subpackage api.objects
 */
class KalturaSystemPartnerLimitArray extends KalturaTypedArray
{
	/**
	 * @param Partner $partner
	 * @return KalturaSystemPartnerLimitArray
	 */
	public static function fromPartner(Partner $partner)
	{
		$arr = new KalturaSystemPartnerLimitArray();
		$reflector = KalturaTypeReflectorCacher::get('KalturaSystemPartnerLimitType');
		$types = $reflector->getConstants();
		foreach($types as $typeInfo) {
			$typeValue = $typeInfo->getDefaultValue();
			if ($typeValue == KalturaSystemPartnerLimitType::LIVE_CONCURRENT_BY_ADMIN_TAG)
			{
				$adminTagLimits =  KalturaSystemPartnerLiveAdminTagLimit::getArrayFromPartner($partner);
				foreach ($adminTagLimits as $adminTagLimit)
				{
					$arr[] = $adminTagLimit;
				}
			} 
			else
			{
				$arr[] = KalturaSystemPartnerOveragedLimit::fromPartner($typeValue, $partner);
			}
		}
		return $arr;
	} 
	
	public function __construct()
	{
		return parent::__construct("KalturaSystemPartnerLimit");
	}
}