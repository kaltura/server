<?php
/**
 * @abstract
 * @plugin drm
 * @package plugins.drm
 * @subpackage api.services
 */

class DrmLicenseUtils {

    const SYSTEM_NAME = 'OVP';
    
    private $secureEntryHelper;

    public function __construct($entry, $referrer)
    {
        $this->secureEntryHelper = new KSecureEntryHelper($entry, kCurrentContext::$ks, $referrer, ContextType::PLAY);
        $this->secureEntryHelper->validateForPlay();
    }

    /**
     * @return mixed $policyId
     */
    public function getPolicyId()
    {
        $actions = $this->secureEntryHelper->getContextResult()->getActions();
        $policyId = null;
        foreach($actions as $action)
        {
            if($action instanceof kAccessControlDrmPolicyAction && $action->getPolicyId())
            {
                $policyId = $action->getPolicyId();
                break;
            }
        }
        return $policyId;
    }

    public static function calculateExpirationDate(DrmPolicy $policy, entry $entry)
    {
        $beginDate = time();
        switch($policy->getLicenseExpirationPolicy())
        {
            case DrmLicenseExpirationPolicy::FIXED_DURATION:
                $expirationDate = $beginDate + dateUtils::DAY*$policy->getDuration();
                break;
            case DrmLicenseExpirationPolicy::ENTRY_SCHEDULING_END:
                $expirationDate = $entry->getEndDate();
                break;
        }
        return $expirationDate;
    }

    public function getSecureEntryHelper()
    {
        return $this->secureEntryHelper;
    }

    public static function signDataWithKey($dataToSign, $signingKey)
    {
        return rawurlencode(base64_encode(sha1($signingKey.$dataToSign,TRUE)));
    }

    public static function createCustomDataForEntry($entryId, $flavors, $signingKey){
        return self::createCustomData($entryId, $flavors, $signingKey);
    }

	public static function createCustomData($entryId, $flavorAssets, $signingKey)
	{
		$flavorIds = "";
        $first = true;
		foreach ($flavorAssets as $flavor)
		{
            /**
             * @var asset $flavor
             */
			if ($first)
			{
				$first = false;
			}
			else
			{
				$flavorIds .=",";
			}
			$flavorIds .= $flavor->getId();
		}

        $innerData = array();
        $innerData["ca_system"] = self::SYSTEM_NAME;
        $innerData["user_token"] = kCurrentContext::$ks;
        $innerData["account_id"] = kCurrentContext::getCurrentPartnerId();
        $innerData["content_id"] = $entryId;
        $innerData["files"] = $flavorIds;

		$customData = array();
		foreach ($flavorAssets as $flavor)
		{
			/*
			* we sign for each flavor asset in case that in the future someone will want to add data per flavor asset
			*/
			$innerDataJson = json_encode($innerData);
			$innerDataSignature = self::signDataWithKey($innerDataJson, $signingKey);
			$innerDataJsonEncoded = rawurlencode(base64_encode($innerDataJson));

			$customData[$flavor->getId()] = array();
			$customData[$flavor->getId()]["custom_data"] = $innerDataJsonEncoded;
			$customData[$flavor->getId()]["signature"] = $innerDataSignature;
		}

		kApiCache::limitConditionalCacheTimeToKs();

		return $customData;
	}

	public static function purifyUrl($url)
	{
		$url = preg_replace('/\s+/', '', $url);
		return $url;
	}
}