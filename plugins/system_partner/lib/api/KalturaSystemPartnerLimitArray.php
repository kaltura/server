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
		foreach($types as $type)
			$arr[] = KalturaSystemPartnerLimit::fromPartner($type, $partner);;
			
		return $arr;
	} 
	
	public function __construct()
	{
		return parent::__construct("KalturaSystemPartnerLimit");
	}
}