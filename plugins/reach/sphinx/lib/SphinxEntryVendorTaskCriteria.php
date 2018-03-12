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
		
		if($filter->get('_eq_user_id'))
		{
			$this->sphinxSkipped = false;
			$kuserId = $filter->get('_eq_user_id');
			
			$this->addAnd(CuePointPeer::IS_PUBLIC, CuePoint::getIndexPrefix(kCurrentContext::getCurrentPartnerId()).$isPublic, Criteria::EQUAL);
		}
		$filter->unsetByName('_eq_is_public');
		
		return parent::applyFilterFields($filter);
	}

	public function getTranslateIndexId($id)
	{
		return $id;
	}

	private function buildMd5String($str)
	{
		return mySearchUtils::getMd5EncodedString($str);
	}
}