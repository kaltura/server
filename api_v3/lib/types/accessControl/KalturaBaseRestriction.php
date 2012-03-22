<?php
/**
 * @package api
 * @subpackage objects
 * @deprecated use KalturaRule instead
 * @abstract
 */
class KalturaBaseRestriction extends KalturaObject
{
	/**
	 * @param KalturaRestrictionArray $restrictions enable one restriction to be affected by other restrictions
	 * @return kAccessControlRestriction
	 * @abstract must be implemented
	 */
	public function toRule(KalturaRestrictionArray $restrictions)
	{
		throw new KalturaAPIException(KalturaErrors::OBJECT_TYPE_ABSTRACT, get_class($this));
	}
}