<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaDeleteJobData extends KalturaJobData
{
	/**
	 * The filter should return the list of objects that need to be deleted.
	 * @var KalturaFilter
	 */
	public $filter;
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbData = null, $props_to_skip = array()) 
	{
		if(is_null($dbData))
			$dbData = new kDeleteJobData();
			
		return parent::toObject($dbData, $props_to_skip);
	}
	
	public function fromObject($dbData) 
	{
		/* @var $dbData kDeleteJobData */
		$filter = $dbData->getFilter();
		$filterType = get_class($filter);
		switch($filterType)
		{
			case 'categoryEntryFilter':
				$this->filter = new KalturaCategoryEntryFilter();
				break;
				
			case 'categoryKuserFilter':
				$this->filter = new KalturaCategoryUserFilter();
				break;

			case 'KuserKgroupFilter':
				$this->filter = new KalturaGroupUserFilter();
				break;
				
			default:
				$this->filter = KalturaPluginManager::loadObject('KalturaFilter', $filterType);
		}
		if($this->filter)
			$this->filter->fromObject($filter);
		
		return parent::fromObject($dbData);
	}
}
