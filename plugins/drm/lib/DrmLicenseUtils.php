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
        return urlencode(base64_encode(sha1($signingKey.$dataToSign,TRUE)));
    }

    public static function createCustomData($entryId, KalturaFlavorAssetArray $flavorAssets)
    {
        $customData = new stdClass();
        $customData->ca_system = self::SYSTEM_NAME;
        $customData->user_token = kCurrentContext::$ks;
        $customData->acount_id = kCurrentContext::$partner_id;
        $customData->content_id = $entryId;
        $customData->files = "";
        if (isset($flavorAssets))
        {
            $flavorIds = array();
            $flavorAssets = $flavorAssets->toArray();
            foreach ($flavorAssets as $flavor)
            {
                $flavorIds[] = $flavor->flavorParamsId;
            }
            $customData->files = $flavorIds;
        }
        $customDataJson = json_encode($customData);
        return $customDataJson;
    }

}