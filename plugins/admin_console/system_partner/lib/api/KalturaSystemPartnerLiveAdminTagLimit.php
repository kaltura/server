<?php
/**
 * @package plugins.systemPartner
 * @subpackage api.objects
 */
class KalturaSystemPartnerLiveAdminTagLimit extends KalturaSystemPartnerLimit
{
	/**
	 * @var string
	 */
	public $adminTag;

	protected static function createLiveAdminTagLimit($adminTag, $value)
	{
		$res = new KalturaSystemPartnerLiveAdminTagLimit();
		$res->type = KalturaSystemPartnerLimitType::LIVE_CONCURRENT_BY_ADMIN_TAG;
		$res->adminTag = $adminTag;
		$res->max = $value;
		return $res;
	}

	/**
	 * @param KalturaSystemPartnerLimitType $type
	 * @param Partner $partner
	 * @return KalturaSystemPartnerLimitArray
	 */
	public static function getArrayFromPartner($type, $partner)
	{
		$res = new KalturaSystemPartnerLimitArray();
		$limits = $partner->getMaxConcurrentLiveByAdminTag();
		foreach ($limits as $adminTag => $value)
		{
			$res[] = self::createLiveAdminTagLimit($adminTag, $value);
		}
		return $res;
	}
	
	public function validate()
	{
		$this->validatePropertyMinValue('max', 0, true);
	}

	/**
	 * @param Partner $partner
	 */
	public function apply(Partner $partner)
	{
		if($this->isNull('max'))
		{
			$this->max = null;
		}
		$limits = $partner->getMaxConcurrentLiveByAdminTag();
		$limits[$this->adminTag] = $this->max;
		$partner->setMaxConcurrentLiveByAdminTag($limits);
	}
}
