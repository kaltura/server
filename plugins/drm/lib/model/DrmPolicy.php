<?php


/**
 * Skeleton subclass for representing a row from the 'drm_policy' table.
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
class DrmPolicy extends BaseDrmPolicy implements IBaseObject
{

	const CUSTOM_DATA_LICENSE_PARAMS = 'license_params';

	public function setLicenseParams($v) {return $this->putInCustomData(self::CUSTOM_DATA_LICENSE_PARAMS, $v);}

	public function getLicenseParams() {return $this->getFromCustomData(self::CUSTOM_DATA_LICENSE_PARAMS);}

} // DrmPolicy
