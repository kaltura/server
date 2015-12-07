<?php


/**
 * Skeleton subclass for representing a row from the 'metadata_profile_field' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.metadata
 * @subpackage model
 */
class MetadataProfileField extends BaseMetadataProfileField implements IBaseObject {

	const STATUS_ACTIVE = 1;
	const STATUS_DEPRECATED = 2;
	const STATUS_NONE_SEARCHABLE = 3;
	
	public function getCacheInvalidationKeys()
	{
		return array("metadataProfileField:metadataProfileId=".strtolower($this->getMetadataProfileId()));
	}
} // MetadataProfileField
