<?php
/**
 * @abstract
 * @plugin drm
 * @package plugins.drm
 * @subpackage api.services
 */

class DrmLicenseUtils {


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
}