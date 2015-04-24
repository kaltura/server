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
        KalturaLog::debug("Validating access control");
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

	public static function createCustomData($entryId, KalturaFlavorAssetArray $flavorAssets, $signingKey)
	{
		$flavorParamIds = "";
		foreach ($flavorAssets as $flavor)
		{
			$flavorParamIds .= $flavor->flavorParamsId.",";
		}

		$innerData = new stdClass();
		$innerData->ca_system = self::SYSTEM_NAME;
		$innerData->user_token = kCurrentContext::$ks;
		$innerData->acount_id = kCurrentContext::$partner_id;
		$innerData->content_id = $entryId;
		$innerData->files = $flavorParamIds;
		$innerDataJson = json_encode($innerData);
		$innerDataSignature = self::signDataWithKey($innerDataJson, $signingKey);
		$innerDataJsonEncoded = rawurlencode(base64_encode($innerDataJson));

		$customData = array();
		foreach ($flavorAssets as $flavor)
		{
            $customData[$flavor->id] = new stdClass();
			$customData[$flavor->id]->custom_data = $innerDataJsonEncoded;
			$customData[$flavor->id]->signature = $innerDataSignature;
		}
		return $customData;
	}
}