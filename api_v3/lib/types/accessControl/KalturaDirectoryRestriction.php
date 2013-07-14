<?php
/**
 * @package api
 * @subpackage objects
 * @deprecated
 */
class KalturaDirectoryRestriction extends KalturaBaseRestriction 
{
	/**
	 * Kaltura directory restriction type
	 * 
	 * @var KalturaDirectoryRestrictionType
	 */
	public $directoryRestrictionType;
	
	/* (non-PHPdoc)
	 * @see KalturaBaseRestriction::toRule()
	 */
	public function toRule(KalturaRestrictionArray $restrictions)
	{
		return null;
	}
}