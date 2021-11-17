<?php
/**
 * @package plugins.Vendor
 * @subpackage zoom.zoomDropFolderPlugin
 */
class kMicrosoftTeamsDropFolderFlowManager implements kObjectChangedEventConsumer
{
	const MAX_TEAMS_DROP_FOLDERS = 4; //Temporary
	
	/**
	 * @param BaseObject $object
	 * @param array $modifiedColumns
	 * @return bool
	 */
	public function objectChanged (BaseObject $object, array $modifiedColumns)
	{
		$partnerTeamsDropFolderModified = false;
		
		if (self::wasStatusChanged($object, $modifiedColumns))
		{
			list($partnerTeamsDropFolder, $partnerTeamsDropFoldersCount) = self::getTeamsDropFolderRelatedInfo($object);
			if ($partnerTeamsDropFolder)
			{
				if ($partnerTeamsDropFolder->getStatus() != DropFolderStatus::ENABLED && $object->getStatus() == VendorIntegrationStatus::ACTIVE)
				{
					self::verifyAndSetDropFolderConfig($partnerTeamsDropFolder);
				}
				
				$partnerTeamsDropFolder->setStatus(self::getDropFolderStatus($object->getStatus()));
				$partnerTeamsDropFolderModified = true;
				KalturaLog ::debug('MicrosoftTeamsDropFolder with vendorId ' . $object->getId() . ' updated status to ' .
				                   $partnerTeamsDropFolder->getStatus());
			}
			if (!$partnerTeamsDropFolder && $partnerTeamsDropFoldersCount < self::MAX_TEAMS_DROP_FOLDERS)
			{
				self::createNewTeamsDropFolder($object);
			}
			else
			{
				if (!$partnerTeamsDropFolder)
				{
					throw new KalturaAPIException(KalturaMicrosoftTeamsDropFolderErrors::EXCEEDED_MAX_TEAMS_DROP_FOLDERS);
				}
			}
		}
		
		if ($partnerTeamsDropFolderModified)
		{
			$partnerTeamsDropFolder->save();
		}
		
		return true;
	}
	
	/**
	 * @param BaseObject $object
	 * @param array $modifiedColumns
	 * @return bool
	 */
	public function shouldConsumeChangedEvent (BaseObject $object, array $modifiedColumns)
	{
		if (self::wasStatusChanged($object, $modifiedColumns))
		{
			return true;
		}
		return false;
	}
	
	public static function wasStatusChanged(BaseObject $object, array $modifiedColumns)
	{
		return ($object instanceof MicrosoftTeamsIntegration) && in_array('vendor_integration.STATUS', $modifiedColumns);
	}
	
	public static function getTeamsDropFolderRelatedInfo($object)
	{
		$criteria = new Criteria();
		$criteria->add(DropFolderPeer::PARTNER_ID, $object->getPartnerId());
		$criteria->add(DropFolderPeer::TYPE, MicrosoftTeamsDropFolderPlugin::getDropFolderTypeCoreValue( MicrosoftTeamsDropFolderType::MS_TEAMS));
		DropFolderPeer::setUseCriteriaFilter(false);
		$allPartnerTeamsDropFolders = DropFolderPeer::doSelect($criteria);
		DropFolderPeer::setUseCriteriaFilter(true);
		$partnerTeamsDropFoldersCount = 0;
		$enabledTeamsDropFolder = null;
		$noneEnabledPartnerTeamsDropFolder = null;
		foreach ($allPartnerTeamsDropFolders as $partnerTeamsDropFolder)
		{
			if ($partnerTeamsDropFolder->getStatus() != DropFolderStatus::DELETED)
			{
				$partnerTeamsDropFoldersCount++;
			}
			if ($enabledTeamsDropFolder || $partnerTeamsDropFolder->getIntegrationId() != $object->getId())
			{
				continue;
			}
			if ($partnerTeamsDropFolder->getStatus() == DropFolderStatus::ENABLED)
			{
				$enabledTeamsDropFolder = $partnerTeamsDropFolder;
				continue;
			}
			if (!$noneEnabledPartnerTeamsDropFolder || $partnerTeamsDropFolder->getUpdatedAt() > $noneEnabledPartnerTeamsDropFolder->getUpdatedAt())
			{
				$noneEnabledPartnerTeamsDropFolder = $partnerTeamsDropFolder;
			}
		}
		return array($enabledTeamsDropFolder ?  $enabledTeamsDropFolder : $noneEnabledPartnerTeamsDropFolder, $partnerTeamsDropFoldersCount);
	}
	
	protected static function createNewTeamsDropFolder($teamsVendorIntegrationObject)
	{
		/* @var $teamsVendorIntegrationObject MicrosoftTeamsIntegration */
		KalturaLog::debug('Creating new MicrosoftTeamsDropFolder');
		// Create new Teams Drop Folder
		$newTeamsDropFolder = new MicrosoftTeamsDropFolder();
		$newTeamsDropFolder->setIntegrationId($teamsVendorIntegrationObject->getId());
		$newTeamsDropFolder->setPartnerId($teamsVendorIntegrationObject->getPartnerId());
		$newTeamsDropFolder->setStatus(self::getDropFolderStatus($teamsVendorIntegrationObject -> getStatus()));
		$newTeamsDropFolder->setType(MicrosoftTeamsDropFolderPlugin::getDropFolderTypeCoreValue(MicrosoftTeamsDropFolderType::MS_TEAMS));
		$newTeamsDropFolder->setName('teams_' . $teamsVendorIntegrationObject->getPartnerId() . '_' . $teamsVendorIntegrationObject->getAccountId());
		$newTeamsDropFolder->setTags('teams');
		$conversionProfileId = $teamsVendorIntegrationObject->getConversionProfileId();
		if (!$conversionProfileId)
		{
			$partner = PartnerPeer::retrieveByPK($newTeamsDropFolder->getPartnerId());
			$conversionProfileId = $partner->getDefaultConversionProfileId();
		}
		$newTeamsDropFolder->setConversionProfileId($conversionProfileId);
		$fileHandler = new DropFolderContentFileHandlerConfig();
		$fileHandler->setSlugRegex('/(?P<referenceId>.+)[.]\w{2,}/');
		$fileHandler->setHandlerType(DropFolderFileHandlerType::CONTENT);
		$fileHandler->setContentMatchPolicy(DropFolderContentFileHandlerMatchPolicy::ADD_AS_NEW);
		$newTeamsDropFolder->setFileHandlerType(DropFolderFileHandlerType::CONTENT);
		$newTeamsDropFolder->setFileHandlerConfig($fileHandler);
		$newTeamsDropFolder->setDc(kDataCenterMgr::getCurrentDcId());
		$newTeamsDropFolder->setPath(0);
		$newTeamsDropFolder->setFileSizeCheckInterval(0);
		$newTeamsDropFolder->setLastFileTimestamp(0);
		$newTeamsDropFolder->setMetadataProfileId(0);
		$newTeamsDropFolder->setFileNamePatterns('*');
		$newTeamsDropFolder->save();
	}
	
	protected static function verifyAndSetDropFolderConfig(MicrosoftTeamsDropFolder $teamsDropFolder)
	{
		KalturaLog::debug('Verify and set config before reactivating Drop Folder Id: ' . $teamsDropFolder->getId());
		$conversionProfile = conversionProfile2Peer::retrieveByPK($teamsDropFolder->getConversionProfileId());
		if (!$conversionProfile)
		{
			$partner = PartnerPeer::retrieveByPK($teamsDropFolder->getPartnerId());
			$teamsDropFolder->setConversionProfileId($partner->getDefaultConversionProfileId());
		}
	}
	
	protected static function getDropFolderStatus($v)
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
}