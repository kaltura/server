<?php

/**
 * @package plugins.OneDrive
 * @subpackage api.objects
 */
class KalturaOneDriveDropFolder extends KalturaRemoteDropFolder
{
	/**
	 * ID of the integration being fulfilled by the drop folder
	 *
	 * @var int
	 */
	public $integrationId;
	
	/**
	 * @var string
	 */
	public $defaultCategoryIds;
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)
	 */
	private static $map_between_objects = array
	(
		'integrationId',
		'defaultCategoryIds',
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
		{
			$dbObject = new OneDriveDropFolder();
		}

		if ($this->integrationId)
		{
			$dbVendorIntegrationItem = VendorIntegrationPeer::retrieveByPK($this->integrationId);
			if (!$dbVendorIntegrationItem)
			{
				throw new KalturaAPIException(APIErrors::INVALID_OBJECT_ID, $this->integrationId);
			}

			if ($dbVendorIntegrationItem->getVendorType() != OneDrivePlugin::getVendorTypeCoreValue(OneDriveVendorType::ONE_DRIVE))
			{
				throw new KalturaAPIException(APIErrors::INVALID_OBJECT_ID, $this->integrationId);
			}
		}

		if($this->defaultCategoryIds)
		{
			$defaultCategoryIds = explode(',', $this->defaultCategoryIds);
			foreach ($defaultCategoryIds as $categoryId)
			{
				$category = categoryPeer::retrieveByPK($categoryId);
				if (!$category)
				{
					throw new KalturaAPIException(APIErrors::INVALID_OBJECT_ID, $categoryId);
				}
			}
		}
		
		if (!$dbObject->getType())
		{
			$dbObject->setType(OneDrivePlugin::getDropFolderTypeCoreValue(OneDriveDropFolderType::ONE_DRIVE));
		}

		return parent::toObject($dbObject, $skip);
	}

	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		if (!OneDrivePlugin::isAllowedPartner(kCurrentContext::getCurrentPartnerId()) || !OneDrivePlugin::isAllowedPartner($sourceObject->getPartnerId()))
		{
			throw new KalturaAPIException (KalturaErrors::PERMISSION_NOT_FOUND, 'Permission not found to use the Microsoft Teams Drop Folder feature.');
		}

		parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}

	public function validateForInsert($propertiesToSkip = array())
	{
		if (!OneDrivePlugin::isAllowedPartner(kCurrentContext::getCurrentPartnerId()) || !OneDrivePlugin::isAllowedPartner($this->partnerId))
		{
			throw new KalturaAPIException (KalturaErrors::PERMISSION_NOT_FOUND, 'Permission not found to use the Microsoft Teams Drop Folder feature.');
		}

		parent::validateForInsert($propertiesToSkip);
	}
}