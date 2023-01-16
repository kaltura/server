<?php
/**
 * @package plugins.WebexAPIDropFolder
 * @subpackage lib
 */
class kWebexAPIDropFolderFlowManager implements kObjectChangedEventConsumer
{
	const MAX_WEBEX_API_DROP_FOLDERS = 6;
	const DELETION_POLICY = 'deletionPolicy';
	
	/**
	 * @inheritDoc
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns)
	{
		if (kConf::getArrayValue(WebexAPIDropFolderPlugin::CONFIGURATION_DISABLE_WEBEX_DROP_FOLDER, WebexAPIDropFolderPlugin::CONFIGURATION_WEBEX_ACCOUNT_PARAM, kConfMapNames::VENDOR, true))
		{
			return true;
		}

		$partnerWebexDropFolderModified = false;

		if (self::wasDeletionPolicyChanged($object, $modifiedColumns))
		{
			/* @var $object WebexAPIVendorIntegration */
			list($partnerWebexDropFolder, $partnerWebexDropFoldersCount) = self::getWebexAPIDropFolderRelatedInfo($object);
			if ($partnerWebexDropFolder)
			{
				self::setDeletePolicy($object, $partnerWebexDropFolder);
				$partnerWebexDropFolderModified = true;
				KalturaLog::debug('WebexAPIDropFolder with vendorId ' . $object->getId() . ' updated deletion policy to ' . $partnerWebexDropFolder->getFileDeletePolicy());
			}
		}
		if (self::wasStatusChanged($object, $modifiedColumns))
		{
			self::setDefaultValuesIntegration($object);
			list($partnerWebexDropFolder, $partnerWebexDropFoldersCount) = self::getWebexAPIDropFolderRelatedInfo($object);
			if ($partnerWebexDropFolder)
			{
				if ($partnerWebexDropFolder->getStatus() != DropFolderStatus::ENABLED && $object->getStatus() == VendorIntegrationStatus::ACTIVE)
				{
					self::verifyAndSetDropFolderConfig($partnerWebexDropFolder);
				}

				$partnerWebexDropFolder->setStatus(VendorHelper::getDropFolderStatus($object->getStatus()));
				$partnerWebexDropFolderModified = true;
				KalturaLog::debug('WebexAPIDropFolder with vendorId ' . $object->getId() . ' updated status to ' . $partnerWebexDropFolder->getStatus());
			}
			else
			{
				if ($partnerWebexDropFoldersCount <= self::MAX_WEBEX_API_DROP_FOLDERS)
				{
					self::createNewWebexAPIDropFolder($object);
				}
				else
				{
					throw new KalturaAPIException(KalturaWebexAPIErrors::EXCEEDED_MAX_WEBEX_API_DROP_FOLDERS);
				}
			}
		}

		if ($partnerWebexDropFolderModified)
		{
			$partnerWebexDropFolder->save();
		}

		return true;
	}

	public static function getWebexAPIDropFolderRelatedInfo($object)
	{
		$criteria = new Criteria();
		$criteria->add(DropFolderPeer::PARTNER_ID, $object->getPartnerId());
		$criteria->add(DropFolderPeer::TYPE, WebexAPIDropFolderPlugin::getCoreValue('DropFolderType', WebexAPIDropFolderType::WEBEX_API));
		DropFolderPeer::setUseCriteriaFilter(false);
		$allPartnerWebexAPIDropFolders = DropFolderPeer::doSelect($criteria);
		DropFolderPeer::setUseCriteriaFilter(true);
		$partnerWebexAPIDropFoldersCount = 0;
		$enabledWebexAPIDropFolder = null;
		$noneEnabledPartnerWebexAPIDropFolder = null;
		foreach ($allPartnerWebexAPIDropFolders as $partnerWebexAPIDropFolder)
		{
			if ($partnerWebexAPIDropFolder->getStatus() != DropFolderStatus::DELETED)
			{
				$partnerWebexAPIDropFoldersCount++;
			}
			if ($enabledWebexAPIDropFolder || $partnerWebexAPIDropFolder->getWebexAPIVendorIntegrationId() != $object->getId())
			{
				continue;
			}
			if ($partnerWebexAPIDropFolder->getStatus() == DropFolderStatus::ENABLED)
			{
				$enabledWebexAPIDropFolder = $partnerWebexAPIDropFolder;
				continue;
			}
			if (!$noneEnabledPartnerWebexAPIDropFolder || $partnerWebexAPIDropFolder->getUpdatedAt() > $noneEnabledPartnerWebexAPIDropFolder->getUpdatedAt())
			{
				$noneEnabledPartnerWebexAPIDropFolder = $partnerWebexAPIDropFolder;
			}
		}
		return array($enabledWebexAPIDropFolder ?  $enabledWebexAPIDropFolder : $noneEnabledPartnerWebexAPIDropFolder, $partnerWebexAPIDropFoldersCount);
	}
	
	/**
	 * @inheritDoc
	 */
	public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns)
	{
		if ( self::wasStatusChanged($object, $modifiedColumns) || self::wasDeletionPolicyChanged($object, $modifiedColumns))
		{
			return true;
		}
		if ( self::hasRefreshTokenChanged($object, $modifiedColumns))
		{
			return true;
		}
		return false;
	}
	
	public static function wasStatusChanged(BaseObject $object, array $modifiedColumns)
	{
		return ($object instanceof WebexAPIVendorIntegration) && in_array(BaseVendorIntegrationPeer::STATUS, $modifiedColumns);
	}
	
	public static function wasDeletionPolicyChanged(BaseObject $object, array $modifiedColumns)
	{
		if ($object instanceof WebexAPIVendorIntegration && in_array(BaseVendorIntegrationPeer::CUSTOM_DATA, $modifiedColumns))
		{
			$oldCustomDataValues = $object->getCustomDataOldValues();
			if (!isset($oldCustomDataValues[''][self::DELETION_POLICY]))
			{
				return false;
			}
			if ($oldCustomDataValues[''][self::DELETION_POLICY] != $object->getDeletionPolicy())
			{
				return true;
			}
		}
		return false;
	}
	
	public static function hasRefreshTokenChanged(BaseObject $object, array $modifiedColumns)
	{
		return ($object instanceof WebexAPIVendorIntegration)
			&& in_array(entryPeer::CUSTOM_DATA, $modifiedColumns)
			&& $object->isColumnModified('refreshToken');
	}
	
	protected static function setDefaultValuesIntegration(WebexAPIVendorIntegration $vendorIntegrationObject)
	{
		if ($vendorIntegrationObject->getEnableMeetingUpload() === null)
		{
			$vendorIntegrationObject->setEnableMeetingUpload(true);
		}
		if ($vendorIntegrationObject->getDeletionPolicy() === null)
		{
			$vendorIntegrationObject->setDeletionPolicy(false);
		}
		if ($vendorIntegrationObject->getEnableTranscription() === null)
		{
			$vendorIntegrationObject->setEnableTranscription(true);
		}
		$vendorIntegrationObject->save();
	}
	
	protected static function createNewWebexAPIDropFolder(WebexAPIVendorIntegration $vendorIntegrationObject)
	{
		KalturaLog::debug('Creating new WebexAPIDropFolder');
		$newWebexAPIDropFolder = new WebexAPIDropFolder();
		$newWebexAPIDropFolder->setWebexAPIVendorIntegrationId($vendorIntegrationObject->getId());
		$newWebexAPIDropFolder->setPartnerId($vendorIntegrationObject->getPartnerId());
		$newWebexAPIDropFolder->setStatus(VendorHelper::getDropFolderStatus($vendorIntegrationObject->getStatus()));
		$newWebexAPIDropFolder->setType(WebexAPIDropFolderPlugin::getCoreValue('DropFolderType',WebexAPIDropFolderType::WEBEX_API));
		$newWebexAPIDropFolder->setName('webex_api_' . $vendorIntegrationObject->getPartnerId() . '_' . $vendorIntegrationObject->getAccountId());
		$newWebexAPIDropFolder->setTags('webex_api');
		$conversionProfileId = $vendorIntegrationObject->getConversionProfileId();
		if (!$conversionProfileId)
		{
			$partner = PartnerPeer::retrieveByPK($newWebexAPIDropFolder->getPartnerId());
			$conversionProfileId = $partner->getDefaultConversionProfileId();
		}
		$newWebexAPIDropFolder->setConversionProfileId($conversionProfileId);
		$fileHandler = new DropFolderContentFileHandlerConfig();
		$fileHandler->setSlugRegex('/(?P<referenceId>.+)[.]\w{2,}/');
		$fileHandler->setHandlerType(DropFolderFileHandlerType::CONTENT);
		$fileHandler->setContentMatchPolicy(DropFolderContentFileHandlerMatchPolicy::ADD_AS_NEW);
		$newWebexAPIDropFolder->setFileHandlerType(DropFolderFileHandlerType::CONTENT);
		$newWebexAPIDropFolder->setFileHandlerConfig($fileHandler);
		$newWebexAPIDropFolder->setDc(kDataCenterMgr::getCurrentDcId());
		$newWebexAPIDropFolder->setPath(0);
		$newWebexAPIDropFolder->setFileSizeCheckInterval(0);
		self::setDeletePolicy($vendorIntegrationObject, $newWebexAPIDropFolder);
		$newWebexAPIDropFolder->setLastFileTimestamp(time());
		$newWebexAPIDropFolder->setMetadataProfileId(0);
		$newWebexAPIDropFolder->setFileNamePatterns('*');
		$newWebexAPIDropFolder->save();
	}

	protected static function verifyAndSetDropFolderConfig(WebexAPIDropFolder $dropFolder)
	{
		KalturaLog::debug('Verify and set config before reactivating Drop Folder Id: ' . $dropFolder->getId());
		$conversionProfile = conversionProfile2Peer::retrieveByPK($dropFolder->getConversionProfileId());
		if (!$conversionProfile)
		{
			$partner = PartnerPeer::retrieveByPK($dropFolder->getPartnerId());
			$dropFolder->setConversionProfileId($partner->getDefaultConversionProfileId());
		}
		$dropFolder->save();
	}

	protected static function setDeletePolicy($vendorIntegrationObject, $dropFolder)
	{
		if ($vendorIntegrationObject->getDeletionPolicy())
		{
			$dropFolder->setFileDeletePolicy(DropFolderFileDeletePolicy::AUTO_DELETE);
			$daysToDelete = kConf::getArrayValue(WebexAPIDropFolderPlugin::CONFIGURATION_AUTO_DELETE_FILE_DAYS, WebexAPIDropFolderPlugin::CONFIGURATION_WEBEX_ACCOUNT_PARAM, WebexAPIDropFolderPlugin::CONFIGURATION_VENDOR_MAP, 1);
			$dropFolder->setAutoFileDeleteDays($daysToDelete);
		}
		else
		{
			$dropFolder->setFileDeletePolicy(DropFolderFileDeletePolicy::MANUAL_DELETE);
		}
		$dropFolder->save();
	}
}
