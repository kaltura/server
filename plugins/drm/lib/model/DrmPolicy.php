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

	const CUSTOM_DATA_EXTRA_SECURITY_PARAMS = 'extraSecurityParams';

	/**
	 * Extra security parameters like OPL
	 *
	 * @var array of KalturaString
	 */
	public $extraSecurityParams;

	public function setExtraSecurityParams($v) {return $this->putInCustomData(self::CUSTOM_DATA_EXTRA_SECURITY_PARAMS, $v);}

	public function getExtraSecurityParams() {return $this->getFromCustomData(self::CUSTOM_DATA_EXTRA_SECURITY_PARAMS);}

} // DrmPolicy
