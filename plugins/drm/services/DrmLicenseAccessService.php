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
        KalturaLog::debug("Starting");
        $response = new KalturaDrmLicenseAccessDetails();
        $response->policyName = "";
        $response->duration = 0;
        $response->absoluteExpiration = 0;
        $flavorIdsArr = explode(",",$flavorIds);

        $entry = entryPeer::retrieveByPK($entryId);
        if (isset($entry))
        {
            $drmLU = new DrmLicenseUtils($entry, $referrer);
            $policyId = $this->validateFlavorAssetssAllowed($drmLU, $flavorIdsArr);
            if (isset($policyId)) {
                KalturaLog::debug("policy_id is '$policyId'");

                $dbPolicy = DrmPolicyPeer::retrieveByPK($policyId);
                if (isset($dbPolicy)) {

                    $expirationDate = DrmLicenseUtils::calculateExpirationDate($dbPolicy, $entry);

                    $response->policyName = $dbPolicy->getName();
                    $response->duration = $expirationDate;
                    $response->absoluteExpiration = $expirationDate;
                    KalturaLog::debug("response is  '" . print_r($response, true) . "' ");
                } else {
                    KalturaLog::err("Could not get DRM policy from DB");
                }
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
        try {
            $policyId = $drmLU->getPolicyId();
            $secureEntryHelper = $drmLU->getSecureEntryHelper();
            foreach($flavorIdsArr as $flavorId)
            {
                $flavorAsset = assetPeer::retrieveById($flavorId);
                if (isset($flavorAsset))
                {
                    if (!$secureEntryHelper->isAssetAllowed($flavorAsset))
                    {
                        KalturaLog::err("Asset '$flavorId' is not allowed according to policy'");
                        $policyId = null;
                        break;
                    }
                }
            }
        } catch (Exception $e) {
            $policyId = null;
        }
        return $policyId;
    }

}