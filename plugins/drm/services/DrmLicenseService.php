<?php
/**
 * @abstract
 * @plugin drm
 * @package plugins.drm
 * @subpackage api.services
 */

abstract class DrmLicenseService extends KalturaBaseService {

    /**
     * @param entry $entry
     * @param null $referrer64base
     * @return $policyId
     * @throws KalturaAPIException
     */
    protected function validateAccessControl(entry $entry, $referrer64base = null)
    {
        KalturaLog::debug("Validating access control");

        if (isset($referrer64base))
        {
            $referrer = base64_decode(str_replace(" ", "+", $referrer64base));
            if (!is_string($referrer))
                $referrer = ""; // base64_decode can return binary data
        }
        else
        {
            $referrer = null;
        }

        $secureEntryHelper = new KSecureEntryHelper($entry, kCurrentContext::$ks, $referrer, ContextType::PLAY);
        $secureEntryHelper->validateForPlay();
        $actions = $secureEntryHelper->getContextResult()->getActions();
        foreach($actions as $action)
        {
            if($action instanceof kAccessControlPlayReadyPolicyAction && $action->getPolicyId())
                return $action->getPolicyId();
        }

        throw new KalturaAPIException(KalturaPlayReadyErrors::PLAYREADY_POLICY_NOT_FOUND, $entry->getId());
    }

    protected function calculateLicenseDates(PlayReadyPolicy $policy, entry $entry)
    {
        $beginDate = time();
        $expirationDate = null;
        $removalDate = null;

        switch($policy->getLicenseExpirationPolicy())
        {
            case DrmLicenseExpirationPolicy::FIXED_DURATION:
                $expirationDate = $beginDate + dateUtils::DAY*$policy->getDuration();
                break;
            case DrmLicenseExpirationPolicy::ENTRY_SCHEDULING_END:
                $expirationDate = $entry->getEndDate();
                break;
        }

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
                if ($exParam[0] == PlayReadyDrmService::PLAY_READY_BEGIN_DATE_PARAM)
                    $beginDate = $exParam[1];
                if ($exParam[0] == PlayReadyDrmService::PLAY_READY_EXPIRATION_DATE_PARAM)
                    $expirationDate = $exParam[1];
            }
        }

        return array($beginDate, $expirationDate, $removalDate);
    }


}