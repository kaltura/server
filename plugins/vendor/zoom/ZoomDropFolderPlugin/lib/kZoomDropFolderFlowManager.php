<?php
/**
 * @package plugins.Vendor
 * @subpackage zoom.zoomDropFolderPlugin
 */
class kZoomDropFolderFlowManager implements kObjectChangedEventConsumer
{
	const MAX_ZOOM_DROP_FOLDERS = 4; //Temporary
	/**
	 * @inheritDoc
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns)
	{
		if ( self::wasStatusChanged($object, $modifiedColumns))
		{
			//Update the status of the Drop Folder
			$criteria = new Criteria();
			$criteria->add(DropFolderPeer::PARTNER_ID, $object->getPartnerId());
			$criteria->add(DropFolderPeer::TYPE, ZoomDropFolderPlugin::getCoreValue('DropFolderType',
			                                                                        ZoomDropFolderType::ZOOM));
			$allPartnerZoomDropFolders = DropFolderPeer::doSelect($criteria);
			$partnerZoomDropFoldersCount = count($allPartnerZoomDropFolders);
			$currentVendorId = $object->getId();
			$foundZoomDropFolder = false;
			foreach ($allPartnerZoomDropFolders as $partnerZoomDropFolder)
			{
				/* @var $partnerZoomDropFolder ZoomDropFolder */
				if ($partnerZoomDropFolder->getFromCustomData(ZoomDropFolder::ZOOM_VENDOR_INTEGRATION_ID) == $currentVendorId)
				{
					$foundZoomDropFolder = true;
					$partnerZoomDropFolder -> setStatus(self::getDropFolderStatus($object -> getStatus()));
					$partnerZoomDropFolder -> save();
					KalturaLog ::debug('Updated ZoomDropFolder status to ' . $partnerZoomDropFolder -> getStatus());
					break;
				}
			}
			if (!$foundZoomDropFolder && $partnerZoomDropFoldersCount < self::MAX_ZOOM_DROP_FOLDERS)
			{
				/* @var $object ZoomVendorIntegration */
				KalturaLog::debug('Creating new ZoomDropFolder');
				// Create new Zoom Drop Folder
				$newZoomDropFolder = new ZoomDropFolder();
				$newZoomDropFolder->setZoomVendorIntegrationId($object->getId());
				$newZoomDropFolder->setPartnerId($object->getPartnerId());
				$newZoomDropFolder->setStatus(self::getDropFolderStatus($object -> getStatus()));
				$newZoomDropFolder->setType(ZoomDropFolderPlugin::getCoreValue('DropFolderType',
				                                                               ZoomDropFolderType::ZOOM));
				$newZoomDropFolder->save();
			}
			else
			{
				if (!$foundZoomDropFolder)
				{
					throw new KalturaAPIException(KalturaZoomDropFolderErrors::EXCEEDED_MAX_ZOOM_DROP_FOLDERS);
				}
			}
			
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns)
	{
		if ( self::wasStatusChanged($object, $modifiedColumns))
		{
			return true;
		}
		if ( self::hasRefreshTokenChanged($object, $modifiedColumns)){
			return true;
		}
		return false;
	}
	
	public static function wasStatusChanged(BaseObject $object, array $modifiedColumns)
	{
		if ( ($object instanceof ZoomVendorIntegration)  //
			//&& in_array(entryPeer::CUSTOM_DATA, $modifiedColumns)
			&& in_array('vendor_integration.STATUS', $modifiedColumns) )
		{
			return true;
		}
		return false;
	}
	
	public static function hasRefreshTokenChanged(BaseObject $object, array $modifiedColumns)
	{
		if ( ($object instanceof ZoomVendorIntegration)
			&& in_array(entryPeer::CUSTOM_DATA, $modifiedColumns)
			&& $object->isColumnModified('refreshToken'))
		{
			return true;
		}
		return false;
	}
	
	private static function getDropFolderStatus($v)
	{
		switch ($v)
		{
			case 1:
			{
				return DropFolderStatus::DISABLED;
			}
			case 2:
			{
				return DropFolderStatus::ENABLED;
			}
			case 3:
			{
				return DropFolderStatus::DELETED;
			}
		}
	}

}