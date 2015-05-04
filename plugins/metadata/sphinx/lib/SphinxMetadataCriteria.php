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
}