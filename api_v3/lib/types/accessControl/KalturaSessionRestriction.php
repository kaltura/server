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
	public function toRule(KalturaRestrictionArray $restrictions)
	{	
		$rule = null;
		
		foreach($restrictions as $restriction)
		{
			if($restriction instanceof KalturaPreviewRestriction)
			{
				$rule = $restriction->toObject(new kAccessControlPreviewRestriction());
			}
		}
	
		if(!$rule)
			$rule = $this->toObject(new kAccessControlSessionRestriction());
		
		return $rule;
	}
}