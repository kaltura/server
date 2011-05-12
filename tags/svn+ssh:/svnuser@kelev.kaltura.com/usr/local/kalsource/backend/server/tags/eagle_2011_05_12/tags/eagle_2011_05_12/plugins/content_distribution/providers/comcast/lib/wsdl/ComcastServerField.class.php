<?php


class ComcastServerField extends SoapObject
{				
	const _ID = 'ID';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _AVAILABLEFORSTORAGE = 'availableForStorage';
					
	const _AVAILABLETOCHILDACCOUNTSBYDEFAULT = 'availableToChildAccountsByDefault';
					
	const _BACKUPSTREAMINGURL = 'backupStreamingURL';
					
	const _CUSTOM = 'custom';
					
	const _DELETEURL = 'deleteURL';
					
	const _DELIVERFROMSTORAGEFORHTTP = 'deliverFromStorageForHTTP';
					
	const _DELIVERSMETAFILES = 'deliversMetafiles';
					
	const _DELIVERY = 'delivery';
					
	const _DELIVERYPERCENTAGE = 'deliveryPercentage';
					
	const _DESCRIPTION = 'description';
					
	const _DISABLED = 'disabled';
					
	const _DISPLAYTITLE = 'displayTitle';
					
	const _DOWNLOADURL = 'downloadURL';
					
	const _DROPFOLDERURLS = 'dropFolderURLs';
					
	const _ENABLEFILELISTURL = 'enableFileListURL';
					
	const _FILELISTOPTIONS = 'fileListOptions';
					
	const _FILELISTPASSWORD = 'fileListPassword';
					
	const _FILELISTURL = 'fileListURL';
					
	const _FILELISTUSERNAME = 'fileListUserName';
					
	const _FORMAT = 'format';
					
	const _GUID = 'guid';
					
	const _ICON = 'icon';
					
	const _INUSE = 'inUse';
					
	const _ISPUBLIC = 'isPublic';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LOCKED = 'locked';
					
	const _MAXIMUMFOLDERCOUNT = 'maximumFolderCount';
					
	const _MEDIAFILEIDS = 'mediaFileIDs';
					
	const _OPTIMIZEFORMANYFILES = 'optimizeForManyFiles';
					
	const _ORGANIZEFILESBYOWNER = 'organizeFilesByOwner';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _PASSWORD = 'password';
					
	const _PID = 'pid';
					
	const _PRIVATEKEY = 'privateKey';
					
	const _PROMPTSTODOWNLOAD = 'promptsToDownload';
					
	const _PUBLISHINGPASSWORD = 'publishingPassword';
					
	const _PUBLISHINGURL = 'publishingURL';
					
	const _PUBLISHINGUSERNAME = 'publishingUserName';
					
	const _PULLURL = 'pullURL';
					
	const _REFRESHSTATUS = 'refreshStatus';
					
	const _RELEASEIDS = 'releaseIDs';
					
	const _REQUIREACTIVEFTP = 'requireActiveFTP';
					
	const _STATUS = 'status';
					
	const _STATUSDESCRIPTION = 'statusDescription';
					
	const _STATUSDETAIL = 'statusDetail';
					
	const _STATUSMESSAGE = 'statusMessage';
					
	const _STORAGENETWORKS = 'storageNetworks';
					
	const _STORAGEQUOTA = 'storageQuota';
					
	const _STORAGEURL = 'storageURL';
					
	const _STORAGEUSED = 'storageUsed';
					
	const _STREAMINGURL = 'streamingURL';
					
	const _SUPPORTSPUSH = 'supportsPush';
					
	const _TITLE = 'title';
					
	const _UPDATEFILELAYOUT = 'updateFileLayout';
					
	const _UPLOADBASEURLS = 'uploadBaseURLs';
					
	const _USERNAME = 'userName';
					
	const _VERSION = 'version';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


