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
    }

    /**
     * @return mixed $policyId
     */
    public function getPolicyId()
    {
        KalturaLog::debug("Validating access control");

        $this->secureEntryHelper->validateForPlay();
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

    public function calculateLicenseDates(PlayReadyPolicy $policy, entry $entry)
    {
        $expirationDate = null;
        $removalDate = null;

        $expirationDate = $this->calculateExpirationDate($policy, $entry);

        switch($policy->getLicenseRemovalPolicy())
        {
            case PlayReadyLicenseRemovalPolicy::FIXED_FROM_EXPIRATION:
                $removalDate = $expirationDate + dateUtils::DAY*$policy->getLicenseRemovalDuration();
                break;
            case PlayReadyLicenseRemovalPolicy::ENTRY_SCHEDULING_END:
                $removalDate = $entry->getEndDate();
                break;
        }

        //override begin and expiration dates from ks if passed
        if(kCurrentContext::$ks_object)
        {
            $privileges = kCurrentContext::$ks_object->getPrivileges();
            $allParams = explode(',', $privileges);
            foreach($allParams as $param)
            {
                $exParam = explode(':', $param);
                if ($exParam[0] == self::PLAY_READY_BEGIN_DATE_PARAM)
                    $beginDate = $exParam[1];
                if ($exParam[0] == self::PLAY_READY_EXPIRATION_DATE_PARAM)
                    $expirationDate = $exParam[1];
            }
        }

        return array($beginDate, $expirationDate, $removalDate);
    }

    public function calculateExpirationDate($policy, $entry)
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