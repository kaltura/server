<?php


class ComcastMediaFileField extends SoapObject
{				
	const _ID = 'ID';
					
	const _URL = 'URL';
					
	const _ACTUALRETENTIONDATE = 'actualRetentionDate';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _ALLOWRELEASE = 'allowRelease';
					
	const _APPROVED = 'approved';
					
	const _ASSETTYPEIDS = 'assetTypeIDs';
					
	const _ASSETTYPES = 'assetTypes';
					
	const _AUDIOCHANNELS = 'audioChannels';
					
	const _AUDIOCODEC = 'audioCodec';
					
	const _AUDIOSAMPLERATE = 'audioSampleRate';
					
	const _AUDIOSAMPLESIZE = 'audioSampleSize';
					
	const _BACKUPSTREAMINGURL = 'backupStreamingURL';
					
	const _BITRATE = 'bitrate';
					
	const _CACHENEWFILE = 'cacheNewFile';
					
	const _CACHED = 'cached';
					
	const _CANDELETE = 'canDelete';
					
	const _CHECKSUM = 'checksum';
					
	const _CHECKSUMALGORITHM = 'checksumAlgorithm';
					
	const _CONTENT = 'content';
					
	const _CONTENTTYPE = 'contentType';
					
	const _CUSTOMFILEPATH = 'customFilePath';
					
	const _DELETEDDATE = 'deletedDate';
					
	const _DESCRIPTION = 'description';
					
	const _DRMKEYID = 'drmKeyID';
					
	const _DYNAMIC = 'dynamic';
					
	const _ENCODENEW = 'encodeNew';
					
	const _ENCODINGPROFILEID = 'encodingProfileID';
					
	const _ENCODINGPROFILETITLE = 'encodingProfileTitle';
					
	const _EXPRESSION = 'expression';
					
	const _FORMAT = 'format';
					
	const _FRAMERATE = 'frameRate';
					
	const _GUID = 'guid';
					
	const _HEIGHT = 'height';
					
	const _INCLUDEINFEEDS = 'includeInFeeds';
					
	const _ISDEFAULT = 'isDefault';
					
	const _ISTHUMBNAIL = 'isThumbnail';
					
	const _LANGUAGE = 'language';
					
	const _LASTCACHED = 'lastCached';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LENGTH = 'length';
					
	const _LOCATIONID = 'locationID';
					
	const _LOCKED = 'locked';
					
	const _MEDIAFILETYPE = 'mediaFileType';
					
	const _MEDIAID = 'mediaID';
					
	const _ORIGINALLOCATION = 'originalLocation';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _PARENTDRMKEYID = 'parentDRMKeyID';
					
	const _PROTECTEDWITHDRM = 'protectedWithDRM';
					
	const _PROTECTIONSCHEME = 'protectionScheme';
					
	const _REFRESHSTATUS = 'refreshStatus';
					
	const _REQUIREDFILENAME = 'requiredFileName';
					
	const _SIZE = 'size';
					
	const _SOURCEMEDIAFILEID = 'sourceMediaFileID';
					
	const _SOURCETIME = 'sourceTime';
					
	const _STATUS = 'status';
					
	const _STATUSDESCRIPTION = 'statusDescription';
					
	const _STATUSDETAIL = 'statusDetail';
					
	const _STATUSMESSAGE = 'statusMessage';
					
	const _STORAGE = 'storage';
					
	const _STORAGESERVERID = 'storageServerID';
					
	const _STORAGESERVERICON = 'storageServerIcon';
					
	const _STOREDFILENAME = 'storedFileName';
					
	const _STOREDFILEPATH = 'storedFilePath';
					
	const _STREAMINGURL = 'streamingURL';
					
	const _SYSTEMTASKID = 'systemTaskID';
					
	const _THUMBNAILURL = 'thumbnailURL';
					
	const _TRUEFORMAT = 'trueFormat';
					
	const _UNDELETE = 'undelete';
					
	const _USEDASMEDIATHUMBNAIL = 'usedAsMediaThumbnail';
					
	const _VERIFY = 'verify';
					
	const _VERSION = 'version';
					
	const _VIDEOCODEC = 'videoCodec';
					
	const _WIDTH = 'width';
					
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


