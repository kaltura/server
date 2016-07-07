<?php
/**
 * @package plugins.pushToNewsDistribution
 * @subpackage api.objects
 */
class KalturaPushToNewsDistributionProfile extends KalturaConfigurableDistributionProfile
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
	public $certificateKey;
	
	/**
	 * @var string
	 */
	 public $idXslt;
	
	/**
	 * @var string
	 */
	public $publishdatXslt;
	
	/**
	 * @var string
	 */
	 public $creationatXslt;
	
	/**
	 * @var string
	 */
	 public $titlelanguagedatXslt;
	
	/**
	 * @var string
	 */
	 public $titleXslt;
	 
	/**
	 * @var string
	 */
	 public $mimetypeXslt; 
	 
	/**
	 * @var string
	 */
	 public $languageXslt;
	
	/**
	 * @var string
	 */
	 public $bodyXslt;
	 
	/**
	 * @var string
	 */
	 public $authorNameXslt;
	 
	/**
	 * @var string
	 */
	 public $authorEmailXslt;
	 
	/**
	 * @var string
	 */
	 public $rightsinfoCopyrightholderXslt;
	 
	/**
	 * @var string
	 */
	 public $rightsinfoNameXslt;
	 
	/**
	 * @var string
	 */
	 public $rightsinfoCopyrightnoticeXslt;

	 /**
	 * @var string
	 */
	 public $productcodeXslt;
	 
	 /**
	 * @var string
	 */
	 public $attributionXslt;
	 
	 /**
	 * @var string
	 */
	 public $metadataOrganizationsXslt;
	 
	 /**
	 * @var string
	 */
	 public $metadataSubjectsXslt;

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
		'certificateKey',
		'idXslt',
		'publishdatXslt',
		'creationatXslt',
		'titlelanguagedatXslt',
		'titleXslt',
		'mimetypeXslt',
		'languageXslt',
		'bodyXslt',
		'authorNameXslt',
		'authorEmailXslt',
		'rightsinfoCopyrightholderXslt',
		'rightsinfoNameXslt',
		'rightsinfoCopyrightnoticeXslt',
		'productcodeXslt',
		'attributionXslt',
		'metadataOrganizationsXslt',
		'metadataSubjectsXslt',		
	);
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			return null;
			
		return parent::toObject($dbObject, $skip);
	}
}
