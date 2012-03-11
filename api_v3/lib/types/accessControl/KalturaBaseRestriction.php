<?php
/**
 * @package api
 * @subpackage objects
 * @deprecated use KalturaRule instead
 */
class KalturaBaseRestriction extends KalturaObject
{
	/**
	 * @return kAccessControlRestriction
	 * @abstract must be implemented
	 */
	public function toRule()
	{
		throw new KalturaAPIException(KalturaErrors::OBJECT_TYPE_ABSTRACT, get_class($this));
	}
}