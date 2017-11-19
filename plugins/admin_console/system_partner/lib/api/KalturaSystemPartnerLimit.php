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
				
			case KalturaSystemPartnerLimitType::LIVE_STREAM_INPUTS:
				$limit->max = $partner->getMaxLiveStreamInputs();
				break;
				
			case KalturaSystemPartnerLimitType::LIVE_STREAM_OUTPUTS:
				$limit->max = $partner->getMaxLiveStreamOutputs();
				break;

			case KalturaSystemPartnerLimitType::USER_LOGIN_ATTEMPTS:
				$limit->max = $partner->getMaxLoginAttempts();
				break;

			case KalturaSystemPartnerLimitType::REACH_CREDIT:
				$limit->max = $partner->getReachCredit();
				break;

			case KalturaSystemPartnerLimitType::REACH_OVERCHARGE:
				$limit->max = $partner->getReachAllowedOvercharged();
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
				
			case KalturaSystemPartnerLimitType::LIVE_STREAM_INPUTS:
				$this->validatePropertyMinValue('max', 1, true);
				break;
				
			case KalturaSystemPartnerLimitType::LIVE_STREAM_OUTPUTS:
				$this->validatePropertyMinValue('max', 1, true);
				break;
				
			case KalturaSystemPartnerLimitType::USER_LOGIN_ATTEMPTS:
				$this->validatePropertyMinValue('max', 0, true);
				break;
		}
	}
	
	/**
	 * @param Partner $partner
	 */
	public function apply(Partner $partner)
	{
		if($this->isNull('max'))
			$this->max = null;
			
		switch($this->type)
		{
			case KalturaSystemPartnerLimitType::ACCESS_CONTROLS:
				$partner->setAccessControls($this->max);
				break;
				
			case KalturaSystemPartnerLimitType::LIVE_STREAM_INPUTS:
				$partner->setMaxLiveStreamInputs($this->max);
				break;
				
			case KalturaSystemPartnerLimitType::LIVE_STREAM_OUTPUTS:
				$partner->setMaxLiveStreamOutputs($this->max);
				break;
				
			case KalturaSystemPartnerLimitType::USER_LOGIN_ATTEMPTS:
				$partner->setMaxLoginAttempts($this->max);
				break;

			case KalturaSystemPartnerLimitType::REACH_CREDIT:
				$partner->setReachCredit($this->max);
				break;

			case KalturaSystemPartnerLimitType::REACH_OVERCHARGE:
				$partner->setReachAllowedOvercharged($this->max);
				break;
		}
	} 
}