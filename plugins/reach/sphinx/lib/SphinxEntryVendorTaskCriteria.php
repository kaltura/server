<?php

class SphinxEntryVendorTaskCriteria extends SphinxCriteria
{
	public function getIndexObjectName() {
		return "EntryVendorTaskIndex";
	}
	
	public function hasPeerFieldName($fieldName)
	{
		if(strpos($fieldName, '.') === false)
		{
			$fieldName = strtoupper($fieldName);
			$fieldName = "entry_vendor_task.$fieldName";
		}
		
		$entryVendorTaskFields = EntryVendorTaskPeer::getFieldNames(BasePeer::TYPE_COLNAME);
		
		return in_array($fieldName, $entryVendorTaskFields);
	}

	/* (non-PHPdoc)
	 * @see SphinxCriteria::applyFilterFields()
	 */
	protected function applyFilterFields(baseObjectFilter $filter)
	{
		/* @var $filter EntryVendorTaskFilter */
		
		if($filter->get('_free_text'))
		{
			$this->sphinxSkipped = false;
			$freeTexts = $filter->get('_free_text');
			$this->addFreeTextToMatchClauseByMatchFields($freeTexts, EntryVendorTaskFilter::FREE_TEXT_FIELDS);
		}
		$filter->unsetByName('_free_text');
		
		return parent::applyFilterFields($filter);
	}

	public function getTranslateIndexId($id)
	{
		return $id;
	}
}