<?php
/**
 * @package plugins.ftpDistribution
 * @subpackage api.objects
 */
class KalturaFtpDistributionProfile extends KalturaConfigurableDistributionProfile
{
	/**
	 * @var KalturaDistributionProtocol
	 * @insertonly 
	 */
	 public $protocol;

	/**
	 * @var string
	 */
	 public $host;

	/**
	 * @var int
	 */
	 public $port;

	/**
	 * @var string
	 */
	 public $basePath;

	/**
	 * @var string
	 */
	 public $username;

	/**
	 * @var string
	 */
	 public $password;

    /**
     * @var string
     */
    public $passphrase;

	/**
	 * @var string
	 */
	 public $sftpPublicKey;

	/**
	 * @var string
	 */
	 public $sftpPrivateKey;

	/**
	 * @var bool
	 */
	 public $disableMetadata;

	/**
	 * @var string
	 */
	 public $metadataXslt;

	/**
	 * @var string
	 */
	 public $metadataFilenameXslt;

	/**
	 * @var string
	 */
	 public $flavorAssetFilenameXslt;

	/**
	 * @var string
	 */
	 public $thumbnailAssetFilenameXslt;
	 
	 /**
	  * @var string
	  */
	 public $assetFilenameXslt;
	 
	 /**
	 * @var string
	 */
	 public $asperaPublicKey;

	/**
	 * @var string
	 */
	 public $asperaPrivateKey;
	 
	 /**
	 * @var bool
	 */
	 public $sendMetadataAfterAssets;
	 
	
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)  
	 */
	private static $map_between_objects = array 
	(
		'protocol',
		'host',
		'port',
		'basePath',
		'username',
		'password',
        'passphrase',
		'sftpPublicKey',
		'sftpPrivateKey',
		'disableMetadata',
		'metadataXslt',
		'metadataFilenameXslt',
		'flavorAssetFilenameXslt',
		'thumbnailAssetFilenameXslt',
		'assetFilenameXslt',
		'asperaPublicKey',
		'asperaPrivateKey',
		'sendMetadataAfterAssets',
	);
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			return null;
			
		parent::toObject($dbObject, $skip);
					
		return $dbObject;
	}
	
	public function fromObject($source_object, IResponseProfile $responseProfile = null)
	{
		parent::fromObject($source_object, $responseProfile);
	}
}