<?php
/**
 * @package plugins.systemPartner
 * @subpackage api.objects
 */
class KalturaSystemPartnerLimit extends KalturaObject
{
	/**
	 * @var KalturaSystemPartnerLimitType
	 */
	public $type;
	
	/**
	 * @var float
	 */
	public $max;
	
	/**
	 * @param KalturaSystemPartnerLimitType $type
	 * @param Partner $partner
	 * @return KalturaSystemPartnerLimit
	 */
	public static function fromPartner($type, Partner $partner)
	{
		$limit = new KalturaSystemPartnerLimit();
		$limit->type = $type;
		
		switch($type)
		{
			case KalturaSystemPartnerLimitType::ACCESS_CONTROLS:
				$limit->max = $partner->getAccessControls();
				break;
		}
		
		return $limit;
	} 

	public function validate()
	{
		switch($this->type)
		{
			case KalturaSystemPartnerLimitType::ACCESS_CONTROLS:
				$this->validatePropertyMinValue('max', 1, true);
				break;
		}
	}
	
	/**
	 * @param Partner $partner
	 */
	public function apply(Partner $partner)
	{
		switch($this->type)
		{
			case KalturaSystemPartnerLimitType::ACCESS_CONTROLS:
				$partner->setAccessControls($this->max);
				break;
		}
	} 
}