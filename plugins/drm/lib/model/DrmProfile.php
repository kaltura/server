<?php


/**
 * Skeleton subclass for representing a row from the 'drm_profile' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.drm
 * @subpackage model
 */
class DrmProfile extends BaseDrmProfile implements IBaseObject {

    const CUSTOM_DATA_SIGNING_KEY = 'signing_key';

    const CONFIG_NAME_LICENSE_SERVER_URL = "license_server_url";

    public function getLicenseServerUrl()
    {
        if(!parent::getLicenseServerUrl())
        {
            return DrmPlugin::getConfigParam(DrmPlugin::getPluginName(), self::CONFIG_NAME_LICENSE_SERVER_URL);
        }
        return parent::getLicenseServerUrl();
    }

    public function getSigningKey()
    {
        $key = $this->getFromCustomData(self::CUSTOM_DATA_SIGNING_KEY);
        if(!$key)
        {
        	$signingKeys  = kConf::get('partner_signing_key', 'drm', array());
        	if(isset($signingKeys[$this->getPartnerId()]))
        		$key = $signingKeys[$this->getPartnerId()];
        	else
        		$key = kConf::get('signing_key', 'drm', null);
        }
        return $key;
    }
    
    public function setSigningKey($key)
    {
        $this->putInCustomData(self::CUSTOM_DATA_SIGNING_KEY, $key);
    }

} // DrmProfile
