<?php
/**
 * @package api
 * @subpackage objects
 * @deprecated use KalturaRule instead
 */
class KalturaSessionRestriction extends KalturaBaseRestriction 
{
	/* (non-PHPdoc)
	 * @see KalturaBaseRestriction::toRule()
	 */
	public function toRule()
	{
		return $this->toObject(new kAccessControlSessionRestriction());
	}
}