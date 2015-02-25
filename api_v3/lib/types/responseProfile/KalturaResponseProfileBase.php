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
	
	/**
	 * @return KalturaFilterPager
	 */
	abstract public function getPager();

	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUsage($sourceObject, $propertiesToSkip)
	 */
	public function validateForUsage($sourceObject, $propertiesToSkip = array())
	{
		$this->validateNestedObjects();
	
		parent::validateForUsage($sourceObject, $propertiesToSkip);
	}
	
	protected function validateNestedObjects($maxPageSize = null, $maxNestingLevel = null)
	{	
		$relatedProfiles = $this->getRelatedProfiles();
		if(!$relatedProfiles)
		{
			return;
		}
	
		if(is_null($maxPageSize))
		{
			$maxPageSize = kConf::get('response_profile_max_page_size', 'local', 100);
		}
		
		if(is_null($maxNestingLevel))
		{
			$maxNestingLevel = kConf::get('response_profile_max_nesting_level', 'local', 2);
		}
		
		if($maxNestingLevel > 0)
		{
			foreach($relatedProfiles as $relatedProfile)
			{
				/* @var $relatedProfile KalturaResponseProfileBase */
				$relatedProfile->validateNestedObjects($maxPageSize, $maxNestingLevel - 1);
				
				$pager = $relatedProfile->getPager();
				if($pager)
				{
					$pager->validatePropertyMaxValue('pageSize', $maxPageSize, true);
				}
			}
		}
		elseif(count($relatedProfiles))
		{
			throw new KalturaAPIException(KalturaErrors::RESPONSE_PROFILE_MAX_NESTING_LEVEL);
		}
	}
}