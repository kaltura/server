<?php
/**
 * @package plugins.Vendor
 * @subpackage zoom.zoomDropFolderPlugin
 */
class kZoomDropFolderFlowManager implements kObjectChangedEventConsumer
{
	const MAX_ZOOM_DROP_FOLDERS = 4; //Temporary
	const DELETION_POLICY = 'deletionPolicy';
	/**
	 * @inheritDoc
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns)
	{
		if(kConf::get('DisableZoomDropFolder','vendor',true))
		{
			return true;
		}

		$partnerZoomDropFolderModified = false;

		if (self::wasDeletionPolicyChanged($object, $modifiedColumns))
		{
			/* @var $object ZoomVendorIntegration */
			list($partnerZoomDropFolder, $partnerZoomDropFoldersCount) = self::getZoomDropFolderRelatedInfo($object);
			if ($partnerZoomDropFolder)
			{
				if ($object->getDeletionPolicy())
				{
					$partnerZoomDropFolder->setFileDeletePolicy(DropFolderFileDeletePolicy::AUTO_DELETE);
					$daysToDelete = kConf::getArrayValue('dayToDelete', 'ZoomAccount', 'vendor', 1);
					$partnerZoomDropFolder->setAutoFileDeleteDays($daysToDelete);
				}
				else
				{
					$partnerZoomDropFolder->setFileDeletePolicy(DropFolderFileDeletePolicy::MANUAL_DELETE);
				}

				$partnerZoomDropFolderModified = true;
				KalturaLog::debug('ZoomDropFolder with vendorId ' . $object->getId() . ' updated deletion policy to ' .
					$partnerZoomDropFolder->getFileDeletePolicy());
			}
		}
		if ( self::wasStatusChanged($object, $modifiedColumns))
		{
			self::setDefaultValuesIntegration($object);
			list($partnerZoomDropFolder, $partnerZoomDropFoldersCount) = self::getZoomDropFolderRelatedInfo($object);
			if ($partnerZoomDropFolder)
			{
				$partnerZoomDropFolder->setStatus(self::getDropFolderStatus($object->getStatus()));
				if ($partnerZoomDropFolder->getStatus() == DropFolderStatus::ENABLED)
				{
					self::verifyAndSetDropFolderConfig($partnerZoomDropFolder);
				}

				$partnerZoomDropFolderModified = true;
				KalturaLog ::debug('ZoomDropFolder with vendorId ' . $object->getId() . ' updated status to ' .
				                   $partnerZoomDropFolder->getStatus());
			}
			if (!$partnerZoomDropFolder && $partnerZoomDropFoldersCount < self::MAX_ZOOM_DROP_FOLDERS)
			{
				self::createNewZoomDropFolder($object);
			}
			else
			{
				if (!$partnerZoomDropFolder)
				{
					throw new KalturaAPIException(KalturaZoomDropFolderErrors::EXCEEDED_MAX_ZOOM_DROP_FOLDERS);
				}
			}
		}

		if ($partnerZoomDropFolderModified)
		{
			$partnerZoomDropFolder->save();
		}

		return true;
	}

	public static function getZoomDropFolderRelatedInfo($object)
	{
		$criteria = new Criteria();
		$criteria->add(DropFolderPeer::PARTNER_ID, $object->getPartnerId());
		$criteria->add(DropFolderPeer::TYPE, ZoomDropFolderPlugin::getCoreValue('DropFolderType', ZoomDropFolderType::ZOOM));
		DropFolderPeer::setUseCriteriaFilter(false);
		$allPartnerZoomDropFolders = DropFolderPeer::doSelect($criteria);
		DropFolderPeer::setUseCriteriaFilter(true);
		$partnerZoomDropFoldersCount = 0;
		$enabledZoomDropFolder = null;
		$noneEnabledPartnerZoomDropFolder = null;
		foreach ($allPartnerZoomDropFolders as $partnerZoomDropFolder)
		{
			if ($partnerZoomDropFolder->getStatus() != DropFolderStatus::DELETED)
			{
				$partnerZoomDropFoldersCount++;
			}
			if ($enabledZoomDropFolder || $partnerZoomDropFolder->getZoomVendorIntegrationId() != $object->getId())
			{
				continue;
			}
			if ($partnerZoomDropFolder->getStatus() == DropFolderStatus::ENABLED)
			{
				$enabledZoomDropFolder = $partnerZoomDropFolder;
				continue;
			}
			if (!$noneEnabledPartnerZoomDropFolder || $partnerZoomDropFolder->getUpdatedAt() > $noneEnabledPartnerZoomDropFolder->getUpdatedAt())
			{
				$noneEnabledPartnerZoomDropFolder = $partnerZoomDropFolder;
			}
		}
		return array($enabledZoomDropFolder ?  $enabledZoomDropFolder : $noneEnabledPartnerZoomDropFolder, $partnerZoomDropFoldersCount);
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
		return ($object instanceof ZoomVendorIntegration) && in_array('vendor_integration.STATUS', $modifiedColumns);
	}
	
	public static function wasDeletionPolicyChanged(BaseObject $object, array $modifiedColumns)
	{
		if ($object instanceof ZoomVendorIntegration && in_array('vendor_integration.CUSTOM_DATA', $modifiedColumns))
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
		return ($object instanceof ZoomVendorIntegration)
			&& in_array(entryPeer::CUSTOM_DATA, $modifiedColumns)
			&& $object->isColumnModified('refreshToken');
	}
	
	private static function getDropFolderStatus($v)
	{
		switch ($v)
		{
			case VendorIntegrationStatus::DISABLED:
			{
				return DropFolderStatus::DISABLED;
			}
			case VendorIntegrationStatus::ACTIVE:
			{
				return DropFolderStatus::ENABLED;
			}
			case VendorIntegrationStatus::DELETED:
			{
				return DropFolderStatus::DELETED;
			}
			default:
			{
				return DropFolderStatus::ERROR;
			}
		}
	}
	
	protected static function setDefaultValuesIntegration(ZoomVendorIntegration $zoomVendorIntegrationObject)
	{
		if ($zoomVendorIntegrationObject->getEnableMeetingUpload() === null)
		{
			$zoomVendorIntegrationObject->setEnableMeetingUpload(true);
		}
		if ($zoomVendorIntegrationObject->getDeletionPolicy() === null)
		{
			$zoomVendorIntegrationObject->setDeletionPolicy(false);
		}
		if ($zoomVendorIntegrationObject->getEnableZoomTranscription() === null)
		{
			$zoomVendorIntegrationObject->setEnableZoomTranscription(true);
		}
		$zoomVendorIntegrationObject->save();
	}
	
	protected static function createNewZoomDropFolder($zoomVendorIntegrationObject)
	{
		/* @var $zoomVendorIntegrationObject ZoomVendorIntegration */
		KalturaLog::debug('Creating new ZoomDropFolder');
		// Create new Zoom Drop Folder
		$newZoomDropFolder = new ZoomDropFolder();
		$newZoomDropFolder->setZoomVendorIntegrationId($zoomVendorIntegrationObject->getId());
		$newZoomDropFolder->setPartnerId($zoomVendorIntegrationObject->getPartnerId());
		$newZoomDropFolder->setStatus(self::getDropFolderStatus($zoomVendorIntegrationObject -> getStatus()));
		$newZoomDropFolder->setType(ZoomDropFolderPlugin::getCoreValue('DropFolderType',
		                                                               ZoomDropFolderType::ZOOM));
		$newZoomDropFolder->setName('zoom_' . $zoomVendorIntegrationObject->getPartnerId() . '_' . $zoomVendorIntegrationObject->getAccountId());
		$newZoomDropFolder->setTags('zoom');
		$conversionProfileId = $zoomVendorIntegrationObject->getConversionProfileId();
		if (!$conversionProfileId)
		{
			$partner = PartnerPeer::retrieveByPK($newZoomDropFolder->getPartnerId());
			$conversionProfileId = $partner->getDefaultConversionProfileId();
		}
		$newZoomDropFolder->setConversionProfileId($conversionProfileId);
		$fileHandler = new DropFolderContentFileHandlerConfig();
		$fileHandler->setSlugRegex('/(?P<referenceId>.+)[.]\w{2,}/');
		$fileHandler->setHandlerType(DropFolderFileHandlerType::CONTENT);
		$fileHandler->setContentMatchPolicy(DropFolderContentFileHandlerMatchPolicy::ADD_AS_NEW);
		$newZoomDropFolder->setFileHandlerType(DropFolderFileHandlerType::CONTENT);
		$newZoomDropFolder->setFileHandlerConfig($fileHandler);
		$newZoomDropFolder->setDc(kDataCenterMgr::getCurrentDcId());
		$newZoomDropFolder->setPath(0);
		$newZoomDropFolder->setFileSizeCheckInterval(0);
		if ($zoomVendorIntegrationObject->getDeletionPolicy())
		{
			$newZoomDropFolder->setFileDeletePolicy(DropFolderFileDeletePolicy::AUTO_DELETE);
			$daysToDelete = kConf::getArrayValue('dayToDelete', 'ZoomAccount', 'vendor', 1);
			$newZoomDropFolder->setAutoFileDeleteDays($daysToDelete);
		}
		else
		{
			$newZoomDropFolder->setFileDeletePolicy(DropFolderFileDeletePolicy::MANUAL_DELETE);
		}
		$newZoomDropFolder->setLastFileTimestamp(0);
		$newZoomDropFolder->setMetadataProfileId(0);
		$newZoomDropFolder->setLastHandledMeetingTime(time());
		$newZoomDropFolder->setFileNamePatterns('*');
		$newZoomDropFolder->save();
	}

	protected static function verifyAndSetDropFolderConfig(ZoomDropFolder $zoomDropFolder)
	{
		KalturaLog::debug('Verify and set config before reactivating Drop Folder Id: ' . $zoomDropFolder->getId());
		$zoomDropFolder->setLastHandledMeetingTime(time());
		$conversionProfile = conversionProfile2Peer::retrieveByPK($zoomDropFolder->getConversionProfileId());
		if (!$conversionProfile)
		{
			$partner = PartnerPeer::retrieveByPK($zoomDropFolder->getPartnerId());
			$zoomDropFolder->setConversionProfileId($partner->getDefaultConversionProfileId());
		}
	}
	
}
