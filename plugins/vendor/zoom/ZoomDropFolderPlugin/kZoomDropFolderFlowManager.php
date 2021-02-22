<?php


class kZoomDropFolderFlowManager implements kObjectChangedEventConsumer
{
	const MAX_ZOOM_DROP_FOLDERS = 4; //Temporary
	/**
	 * @inheritDoc
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns)
	{
		// TODO: Implement objectChanged() method.
		if ( self::wasStatusChanged($object, $modifiedColumns))
		{
			//Update the status of the Drop Folder
			$criteria = new Criteria();
			$criteria->add(DropFolderPeer::PARTNER_ID, $object->getPartnerId());
			$criteria->add(DropFolderPeer::TYPE, $object->getVendorType());
			$allPartnerZoomDropFolders = DropFolderPeer::doSelect($criteria);
			$partnerZoomDropFoldersCount = count($allPartnerZoomDropFolders);
			$currentVendorId = $object->getId(); //vendorId
			$foundZoomDropFolder = false;
			foreach ($allPartnerZoomDropFolders as $partnerZoomDropFolder)
			{
				/* @var $partnerZoomDropFolder ZoomDropFolder */
				if ($partnerZoomDropFolder->getZoomVendorIntegrationId() == $currentVendorId)
				{
					$foundZoomDropFolder = true;
					$partnerZoomDropFolder->setStatus($object->getStatus()); //update the new status
					$partnerZoomDropFolder->save();
					break;
				}
			}
			if (!$foundZoomDropFolder && $partnerZoomDropFoldersCount < self::MAX_ZOOM_DROP_FOLDERS)
			{
				// Create new Zoom Drop Folder
				$newZoomDropFolder = new ZoomDropFolder();
				$newZoomDropFolder->setZoomVendorIntegrationId($object->getId());
				$newZoomDropFolder->save();
			}
			else
			{
				throw new kCoreException("Amount of maximum zoom drop folders per partner exceeded",
				                         kCoreException::EXCEEDED_MAX_CUSTOM_DATA_SIZE);
			}
			
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns)
	{
		// TODO: Implement shouldConsumeChangedEvent() method.
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
		if ( ($object instanceof ZoomVendorIntegration)
			&& in_array(entryPeer::CUSTOM_DATA, $modifiedColumns)
			&& $object->isColumnModified('status'))
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

}