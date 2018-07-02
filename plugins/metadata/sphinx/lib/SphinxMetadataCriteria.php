<?php

/**
 * @package plugins.metadataSphinx
 */
class SphinxMetadataCriteria extends SphinxCriteria
{
	public function getIndexObjectName() {
		return "MetadataIndex";
	}
	
	public function hasPeerFieldName($fieldName)
	{
		if(strpos($fieldName, '.') === false)
		{
			$fieldName = strtoupper($fieldName);
			$fieldName = "metadata.$fieldName";
		}
		
		$metadataFields = MetadataPeer::getFieldNames(BasePeer::TYPE_COLNAME);
		
		return in_array($fieldName, $metadataFields);
	}
	
	/* (non-PHPdoc)
	 * @see SphinxCriteria::applyFilterFields()
	 */
	protected function applyFilterFields(baseObjectFilter $filter)
	{
		/* @var $filter EntryVendorTaskFilter */
		
		if ($filter->get('_eq_object_type') == MetadataObjectType::DYNAMIC_OBJECT)
		{
			$this->sphinxSkipped = false;
			$objectTypeStr = MetadataPeer::getSearchIndexFieldValue(MetadataPeer::OBJECT_TYPE, $filter->get('_eq_object_type'), kCurrentContext::getCurrentPartnerId());
			$this->addMatch("@object_type_str " . $objectTypeStr);
			$filter->unsetByName('_eq_object_type');
		}
		
		return parent::applyFilterFields($filter);
	}
}