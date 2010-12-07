<?php


/**
 * Skeleton subclass for representing a row from the 'entry_distribution' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.model
 */
class EntryDistribution extends BaseEntryDistribution implements IIndexable 
{

	public function setFlavorAssetIds($v)
	{
		if(is_array($v))
			$v = implode(',', $v);
			
		return parent::setFlavorAssetIds($v);
	}
	
	public function setThumbAssetIds($v)
	{
		if(is_array($v))
			$v = implode(',', $v);
			
		return parent::setThumbAssetIds($v);
	}

	public function getValidationErrors()
	{
		$validationErrors = parent::getValidationErrors();
		if(!$validationErrors)
			return array();
			
		return unserialize($validationErrors);
	}

	public function setValidationErrors(array $v)
	{
		return parent::setValidationErrors(serialize($v));
	}

	public function getSunStatus()
	{
		$now = time();
		if($now < $this->getSunrise(null))
			return EntryDistributionSunStatus::BEFORE_SUNRISE;
			
		if($now > $this->getSunset(null))
			return EntryDistributionSunStatus::AFTER_SUNSET;
			
		return EntryDistributionSunStatus::AFTER_SUNRISE;
	}

	/* (non-PHPdoc)
	 * @see IIndexable::getIntId()
	 */
	public function getIntId()
	{
		return $this->getId();
	}

	/* (non-PHPdoc)
	 * @see IIndexable::getObjectIndexName()
	 */
	public function getObjectIndexName()
	{
		return EntryDistributionPeer::OM_CLASS;
	}

	/* (non-PHPdoc)
	 * @see IIndexable::getIndexFieldsMap()
	 */
	public function getIndexFieldsMap()
	{
		return array(
			'entry_distribution_id' => 'id',
			'created_at' => 'createdAt',
			'updated_at' => 'updatedAt',
			'submitted_at' => 'submittedAt',
			'entry_id' => 'entryId',
			'partner_id' => 'partnerId',
			'distribution_profile_id' => 'distributionProfileId',
			'status' => 'status',
			'dirty_status' => 'dirtyStatus',
			'thumb_asset_ids' => 'thumbAssetIds',
			'flavor_asset_ids' => 'flavorAssetIds',
			'sunrise' => 'sunrise',
			'sunset' => 'sunset',
			'remote_id' => 'remoteId',
			'plays' => 'plays',
			'views' => 'views',
			'error_type' => 'errorType',
			'error_number' => 'errorNumber',
		);
	}

	private static $indexFieldTypes = array(
		'entry_distribution_id' => IIndexable::FIELD_TYPE_INTEGER,
		'created_at' => IIndexable::FIELD_TYPE_DATETIME,
		'updated_at' => IIndexable::FIELD_TYPE_DATETIME,
		'submitted_at' => IIndexable::FIELD_TYPE_DATETIME,
		'entry_id' => IIndexable::FIELD_TYPE_STRING,
		'partner_id' => IIndexable::FIELD_TYPE_INTEGER,
		'distribution_profile_id' => IIndexable::FIELD_TYPE_INTEGER,
		'status' => IIndexable::FIELD_TYPE_INTEGER,
		'dirty_status' => IIndexable::FIELD_TYPE_INTEGER,
		'thumb_asset_ids' => IIndexable::FIELD_TYPE_STRING,
		'flavor_asset_ids' => IIndexable::FIELD_TYPE_STRING,
		'sunrise' => IIndexable::FIELD_TYPE_DATETIME,
		'sunset' => IIndexable::FIELD_TYPE_DATETIME,
		'remote_id' => IIndexable::FIELD_TYPE_STRING,
		'plays' => IIndexable::FIELD_TYPE_INTEGER,
		'views' => IIndexable::FIELD_TYPE_INTEGER,
		'error_type' => IIndexable::FIELD_TYPE_INTEGER,
		'error_number' => IIndexable::FIELD_TYPE_INTEGER,
	);
	
	/* (non-PHPdoc)
	 * @see IIndexable::getIndexFieldType()
	 */
	public function getIndexFieldType($field)
	{
		if(isset(self::$indexFieldTypes[$field]))
			return self::$indexFieldTypes[$field];
			
		return null;
	}
	
} // EntryDistribution
