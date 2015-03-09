<?php

/**
 * Retrieve information and invoke actions on Flavor Asset
 *
 * @service DrmLicenseAccess
 * @package api
 * @subpackage services
 */
class DrmLicenseAccessService extends KalturaBaseService
{
	protected function kalturaNetworkAllowed($actionName)
	{
		if(	$actionName == 'getAccess' )
		{
			$this->partnerGroup .= ',0';
			return true;
		}
			
		return parent::kalturaNetworkAllowed($actionName);
	}


    /**
     * getAccessAction
     * input: flavor ids, drmProvider
     * Flow:apply access control validations
    get policy name according to access control
    Calculate license expiration date based on license policy configuration
    Output: firstPlay, duration both set to duration in seconds, policy name
    QA recommendations

     * Get Access Action
     * @action getAccess
     * @param string $flavorIds
     * @param string $drmProvider
     * @return KalturaFlavorAsset
     **/

    function getAccessAction($flavorIds, $drmProvider)
    {
        KalturaLog::err("@@NA I defined a new API, good for me :)");
    }


}