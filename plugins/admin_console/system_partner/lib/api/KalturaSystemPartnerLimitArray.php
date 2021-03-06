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
			$limits = array();
			if ($typeValue == KalturaSystemPartnerLimitType::LIVE_CONCURRENT_BY_ADMIN_TAG)
			{
				$limits =  KalturaSystemPartnerLiveAdminTagLimit::getArrayFromPartner($typeValue, $partner);
			} 
			else
			{
				$limits = KalturaSystemPartnerOveragedLimit::getArrayFromPartner($typeValue, $partner);
			}
			foreach ($limits as $limit)
			{
				$arr[] = $limit;
			}
		}
		return $arr;
	} 
	
	public function __construct()
	{
		return parent::__construct("KalturaSystemPartnerLimit");
	}
}