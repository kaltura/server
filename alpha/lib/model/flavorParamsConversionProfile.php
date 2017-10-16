<?php

/**
 * Subclass for representing a row from the 'flavor_params_conversion_profile' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class flavorParamsConversionProfile extends BaseflavorParamsConversionProfile
{
	/**
	 * When called outside conversion profile, doesn't affect the entry readiness even if all flavors are ready.
	 */
	const READY_BEHAVIOR_IGNORE = -1;
	
	/**
	 * Doesn't affect the entry readiness. 
	 * The entry will become ready only when all flavors are ready OR if another optional or required flavor affected the entry readiness.
	 * This flavor set the entry to ready only if it's the last converting flavor and the entry is not ready yet.
	 */
	const READY_BEHAVIOR_NO_IMPACT = 0;
	
	/**
	 * All required flavors must be ready in order to set the entry to ready.
	 * Required flavor won't be reduced by the decision layer, if it couldn't be converted as configured, it will fail.
	 */
	const READY_BEHAVIOR_REQUIRED = 1;
	
	/**
	 * When flavor is ready, the entry will become ready, unless, there are required flavors that are not ready yet.
	 */
	const READY_BEHAVIOR_OPTIONAL = 2;
	
	
	public function getCacheInvalidationKeys()
	{
		return array("flavorParamsConversionProfile:flavorParamsId=".strtolower($this->getFlavorParamsId()).",conversionProfileId=".strtolower($this->getConversionProfileId()), "flavorParamsConversionProfile:conversionProfileId=".strtolower($this->getConversionProfileId()));
	}
	
	public function postSave(PropelPDO $con = null) 
	{
		$this->updateConversionProfileLastModified();
		parent::postSave($con);
	}
	
	public function postDelete(PropelPDO $con = null)
	{
		$this->updateConversionProfileLastModified();
		parent::postDelete($con);
	}
	
	private function updateConversionProfileLastModified()
	{
		$conversionProfile = $this->getconversionProfile2();
		
		if($conversionProfile)
		{
			$conversionProfile->setUpdatedAt(time());
			$conversionProfile->save();
		}
	}
	
	public function setIsEncrypted($v)	{$this->putInCustomData('IsEncrypted', $v);}
	public function getIsEncrypted()	{return $this->getFromCustomData('IsEncrypted', null, null);}

	public function setContentAwareness($v) {$this->putInCustomData('ContentAwareness', $v);}
	public function getContentAwareness()	{return $this->getFromCustomData('ContentAwareness', null, null);}

	public function setTwoPass($v)		{$this->putInCustomData('TwoPass', $v);}
	public function getTwoPass()		{return $this->getFromCustomData('TwoPass', null, null);}

        public function setTags($v)		{$this->putInCustomData('Tags', $v);}
        public function getTags()           	{return $this->getFromCustomData('Tags', null, null);}

	public function setChunkedEncodeMode($v){ $this->putInCustomData('ChunkedEncodeMode', $v);}
	public function getChunkedEncodeMode()        {return $this->getFromCustomData('ChunkedEncodeMode', null, 0);}
}
