<?php


class ComcastMediaFile extends ComcastStatusObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfMediaFileField';
			case 'approved':
				return 'Comcastboolean';
			case 'assetTypeIDs':
				return 'ComcastIDSet';
			case 'assetTypes':
				return 'ComcastArrayOfstring';
			case 'contentType':
				return 'ComcastContentType';
			case 'expression':
				return 'ComcastExpression';
			case 'format':
				return 'ComcastFormat';
			case 'language':
				return 'ComcastLanguage';
			case 'mediaFileType':
				return 'ComcastMediaFileType';
			case 'storageServerIcon':
				return 'ComcastServerIcon';
			case 'trueFormat':
				return 'ComcastFormat';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var ComcastArrayOfMediaFileField
	 **/
	public $template;
				
	/**
	 * @var string
	 **/
	public $URL;
				
	/**
	 * @var dateTime
	 **/
	public $actualRetentionDate;
				
	/**
	 * @var boolean
	 **/
	public $allowRelease;
				
	/**
	 * @var Comcastboolean
	 **/
	public $approved;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $assetTypeIDs;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $assetTypes;
				
	/**
	 * @var int
	 **/
	public $audioChannels;
				
	/**
	 * @var string
	 **/
	public $audioCodec;
				
	/**
	 * @var int
	 **/
	public $audioSampleRate;
				
	/**
	 * @var int
	 **/
	public $audioSampleSize;
				
	/**
	 * @var string
	 **/
	public $backupStreamingURL;
				
	/**
	 * @var long
	 **/
	public $bitrate;
				
	/**
	 * @var boolean
	 **/
	public $cacheNewFile;
				
	/**
	 * @var boolean
	 **/
	public $cached;
				
	/**
	 * @var boolean
	 **/
	public $canDelete;
				
	/**
	 * @var string
	 **/
	public $checksum;
				
	/**
	 * @var string
	 **/
	public $checksumAlgorithm;
				
	/**
	 * @var base64Binary
	 **/
	public $content;
				
	/**
	 * @var ComcastContentType
	 **/
	public $contentType;
				
	/**
	 * @var string
	 **/
	public $customFilePath;
				
	/**
	 * @var dateTime
	 **/
	public $deletedDate;
				
	/**
	 * @var string
	 **/
	public $drmKeyID;
				
	/**
	 * @var boolean
	 **/
	public $dynamic;
				
	/**
	 * @var boolean
	 **/
	public $encodeNew;
				
	/**
	 * @var long
	 **/
	public $encodingProfileID;
				
	/**
	 * @var string
	 **/
	public $encodingProfileTitle;
				
	/**
	 * @var ComcastExpression
	 **/
	public $expression;
				
	/**
	 * @var ComcastFormat
	 **/
	public $format;
				
	/**
	 * @var float
	 **/
	public $frameRate;
				
	/**
	 * @var string
	 **/
	public $guid;
				
	/**
	 * @var int
	 **/
	public $height;
				
	/**
	 * @var boolean
	 **/
	public $includeInFeeds;
				
	/**
	 * @var boolean
	 **/
	public $isDefault;
				
	/**
	 * @var boolean
	 **/
	public $isThumbnail;
				
	/**
	 * @var ComcastLanguage
	 **/
	public $language;
				
	/**
	 * @var dateTime
	 **/
	public $lastCached;
				
	/**
	 * @var long
	 **/
	public $length;
				
	/**
	 * @var long
	 **/
	public $locationID;
				
	/**
	 * @var ComcastMediaFileType
	 **/
	public $mediaFileType;
				
	/**
	 * @var long
	 **/
	public $mediaID;
				
	/**
	 * @var string
	 **/
	public $originalLocation;
				
	/**
	 * @var string
	 **/
	public $parentDRMKeyID;
				
	/**
	 * @var boolean
	 **/
	public $protectedWithDRM;
				
	/**
	 * @var string
	 **/
	public $protectionScheme;
				
	/**
	 * @var string
	 **/
	public $requiredFileName;
				
	/**
	 * @var long
	 **/
	public $size;
				
	/**
	 * @var long
	 **/
	public $sourceMediaFileID;
				
	/**
	 * @var long
	 **/
	public $sourceTime;
				
	/**
	 * @var string
	 **/
	public $storage;
				
	/**
	 * @var long
	 **/
	public $storageServerID;
				
	/**
	 * @var ComcastServerIcon
	 **/
	public $storageServerIcon;
				
	/**
	 * @var string
	 **/
	public $storedFileName;
				
	/**
	 * @var string
	 **/
	public $storedFilePath;
				
	/**
	 * @var string
	 **/
	public $streamingURL;
				
	/**
	 * @var long
	 **/
	public $systemTaskID;
				
	/**
	 * @var string
	 **/
	public $thumbnailURL;
				
	/**
	 * @var ComcastFormat
	 **/
	public $trueFormat;
				
	/**
	 * @var boolean
	 **/
	public $undelete;
				
	/**
	 * @var boolean
	 **/
	public $usedAsMediaThumbnail;
				
	/**
	 * @var boolean
	 **/
	public $verify;
				
	/**
	 * @var string
	 **/
	public $videoCodec;
				
	/**
	 * @var int
	 **/
	public $width;
				
}


