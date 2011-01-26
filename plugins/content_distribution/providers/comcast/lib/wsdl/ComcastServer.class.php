<?php


class ComcastServer extends ComcastStatusObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfServerField';
			case 'delivery':
				return 'ComcastDelivery';
			case 'dropFolderURLs':
				return 'ComcastArrayOfstring';
			case 'format':
				return 'ComcastFormat';
			case 'icon':
				return 'ComcastServerIcon';
			case 'mediaFileIDs':
				return 'ComcastIDSet';
			case 'releaseIDs':
				return 'ComcastIDSet';
			case 'storageNetworks':
				return 'ComcastArrayOfstring';
			case 'uploadBaseURLs':
				return 'ComcastArrayOfstring';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var ComcastArrayOfServerField
	 **/
	public $template;
				
	/**
	 * @var boolean
	 **/
	public $availableForStorage;
				
	/**
	 * @var boolean
	 **/
	public $availableToChildAccountsByDefault;
				
	/**
	 * @var string
	 **/
	public $backupStreamingURL;
				
	/**
	 * @var boolean
	 **/
	public $custom;
				
	/**
	 * @var string
	 **/
	public $deleteURL;
				
	/**
	 * @var boolean
	 **/
	public $deliverFromStorageForHTTP;
				
	/**
	 * @var boolean
	 **/
	public $deliversMetafiles;
				
	/**
	 * @var ComcastDelivery
	 **/
	public $delivery;
				
	/**
	 * @var float
	 **/
	public $deliveryPercentage;
				
	/**
	 * @var boolean
	 **/
	public $disabled;
				
	/**
	 * @var string
	 **/
	public $displayTitle;
				
	/**
	 * @var string
	 **/
	public $downloadURL;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $dropFolderURLs;
				
	/**
	 * @var boolean
	 **/
	public $enableFileListURL;
				
	/**
	 * @var string
	 **/
	public $fileListOptions;
				
	/**
	 * @var string
	 **/
	public $fileListPassword;
				
	/**
	 * @var string
	 **/
	public $fileListURL;
				
	/**
	 * @var string
	 **/
	public $fileListUserName;
				
	/**
	 * @var ComcastFormat
	 **/
	public $format;
				
	/**
	 * @var string
	 **/
	public $guid;
				
	/**
	 * @var ComcastServerIcon
	 **/
	public $icon;
				
	/**
	 * @var boolean
	 **/
	public $inUse;
				
	/**
	 * @var boolean
	 **/
	public $isPublic;
				
	/**
	 * @var long
	 **/
	public $maximumFolderCount;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $mediaFileIDs;
				
	/**
	 * @var boolean
	 **/
	public $optimizeForManyFiles;
				
	/**
	 * @var boolean
	 **/
	public $organizeFilesByOwner;
				
	/**
	 * @var string
	 **/
	public $password;
				
	/**
	 * @var string
	 **/
	public $pid;
				
	/**
	 * @var string
	 **/
	public $privateKey;
				
	/**
	 * @var boolean
	 **/
	public $promptsToDownload;
				
	/**
	 * @var string
	 **/
	public $publishingPassword;
				
	/**
	 * @var string
	 **/
	public $publishingURL;
				
	/**
	 * @var string
	 **/
	public $publishingUserName;
				
	/**
	 * @var string
	 **/
	public $pullURL;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $releaseIDs;
				
	/**
	 * @var boolean
	 **/
	public $requireActiveFTP;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $storageNetworks;
				
	/**
	 * @var long
	 **/
	public $storageQuota;
				
	/**
	 * @var string
	 **/
	public $storageURL;
				
	/**
	 * @var long
	 **/
	public $storageUsed;
				
	/**
	 * @var string
	 **/
	public $streamingURL;
				
	/**
	 * @var boolean
	 **/
	public $supportsPush;
				
	/**
	 * @var string
	 **/
	public $title;
				
	/**
	 * @var boolean
	 **/
	public $updateFileLayout;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $uploadBaseURLs;
				
	/**
	 * @var string
	 **/
	public $userName;
				
}


