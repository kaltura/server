<?php

/**
 * Retrieve information and invoke actions on Flavor Asset
 *
 * @service drmLicenseAccess
 * @package plugins.drm
 * @subpackage api.services
 */

class DrmLicenseAccessService extends KalturaBaseService
{

	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		if (!DrmPlugin::isAllowedPartner(kCurrentContext::$ks_partner_id))
			throw new KalturaAPIException(KalturaErrors::FEATURE_FORBIDDEN, DrmPlugin::PLUGIN_NAME);
	}

    /**
     * getAccessAction
     * input: flavor ids, drmProvider
     * Get Access Action
     * @action getAccess
     * @param string $entryId
     * @param string $flavorIds
     * @param string $referrer
* @return KalturaDrmLicenseAccessDetails
     **/
    public function getAccessAction($entryId, $flavorIds, $referrer)
    {
        $response = new KalturaDrmLicenseAccessDetails();
        $response->policy = "";
        $response->duration = 0;
        $response->absolute_duration = 0;
        $flavorIdsArr = explode(",",$flavorIds);

        $entry = entryPeer::retrieveByPK($entryId);
        if (isset($entry))
        {
            try {
                $drmLU = new DrmLicenseUtils($entry, $referrer);
                if ($this->validateFlavorAssetssAllowed($drmLU, $flavorIdsArr) == true)
                {
                    $policyId = $drmLU->getPolicyId();
                    KalturaLog::info("policy_id is '$policyId'");

                    $dbPolicy = DrmPolicyPeer::retrieveByPK($policyId);
                    if (isset($dbPolicy)) {

                        $expirationDate = DrmLicenseUtils::calculateExpirationDate($dbPolicy, $entry);

                        $response->policy = $dbPolicy->getName();
                        $response->licenseParams = $this->buildPolicy($dbPolicy);
                        $response->duration = $expirationDate;
                        $response->absolute_duration = $expirationDate;
                        KalturaLog::info("response is  '" . print_r($response, true) . "' ");
                    } else {
                        KalturaLog::err("Could not get DRM policy from DB");
                    }
                }
            } catch (Exception $e) {
                KalturaLog::err("Could not validate license access, returned with message '".$e->getMessage()."'");
            }
        }
        else
        {
            KalturaLog::err("Entry '$entryId' not found");
        }
        return $response;

    }

    protected function validateFlavorAssetssAllowed(DrmLicenseUtils $drmLU, $flavorIdsArr)
    {
        $secureEntryHelper = $drmLU->getSecureEntryHelper();
        foreach($flavorIdsArr as $flavorId)
        {
            $flavorAsset = assetPeer::retrieveById($flavorId);
            if (isset($flavorAsset))
            {
                if (!$secureEntryHelper->isAssetAllowed($flavorAsset))
                {
                    KalturaLog::err("Asset '$flavorId' is not allowed according to policy'");
                    return false;
                }
            }
        }
        return true;
    }

    protected function buildPolicy(DrmPolicy $dbDrmPolicy)
    {
        $licenseParams = $dbDrmPolicy->getLicenseParams();
        if (is_null($licenseParams))
            return null;
        return $licenseParams;
    }

}