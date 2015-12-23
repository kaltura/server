<?php

require_once(KALTURA_ROOT_PATH.'/alpha/apps/kaltura/lib/cache/kCacheManager.php');
require_once(KALTURA_ROOT_PATH.'/vendor/facebook-sdk-php-v5-customized/autoload.php');

/**
 * Saves key/value in the custom data of the provider given
 *  @package infra
 *  @subpackage general
 */
class KalturaFacebookPersistentDataHandler implements \Facebook\PersistentData\PersistentDataInterface{

    private $facebookDistributionProfile;

	/**
	 * @param $profile FacebookDistributionProfile
	 * @throws Exception
	 */
    function __construct($profile){
        if (!is_a($profile,'FacebookDistributionProfile' )){
            throw new Exception("Invalid distribution type given ");
        }
        $this->facebookDistributionProfile = $profile;
    }

    /**
     * @inheritdoc
     */
    public function get($key)
    {
        $returnValue = $this->facebookDistributionProfile->getFromCustomData($key);
        // since this token is just for the csrf validation we delete it after first use
        $this->set($key, null);
        return $returnValue;
    }

    /**
     * @inheritdoc
     */
    public function set($key, $value)
    {
        if (!$this->facebookDistributionProfile->putInCustomData($key, $value)){
            throw new Exception("Failed to set value {$value} for key {$key} in custom data for provider {$this->facebookDistributionProfile->getId()}");
        }
        $this->facebookDistributionProfile->save();
    }


}
