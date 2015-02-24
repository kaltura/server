<?php
/**
 * @package api
 * @subpackage objects
 */
abstract class KalturaResponseProfileBase extends KalturaObject
{
	/**
	 * @return array<KalturaResponseProfileBase>
	 */
	abstract public function getRelatedProfiles();
	
	protected function validateNestedObjects($maxPageSize, $maxNestingLevel)
	{
		$relatedProfiles = $this->getRelatedProfiles();
		if(!$relatedProfiles)
		{
			return;
		}
		
		if($maxNestingLevel > 0)
		{
			foreach($relatedProfiles as $relatedProfile)
			{
				/* @var $relatedProfile KalturaResponseProfileBase */
				$relatedProfile->validateNestedObjects($maxPageSize, $maxNestingLevel - 1);
				
				if($relatedProfile->pager)
				{
					$relatedProfile->pager->validatePropertyMaxValue('pageSize', $maxPageSize, true);
				}
			}
		}
		else
		{
			if(count($relatedProfiles))
			{
				throw new KalturaAPIException(KalturaErrors::RESPONSE_PROFILE_MAX_NESTING_LEVEL);
			}
		}
	}
}