<?php
/**
 * @package plugins.httpNotification
 * @subpackage api.objects
 */
class KalturaHttpNotificationDispatchJobData extends KalturaEventNotificationDispatchJobData
{
	/**
	 * Remote server URL
	 * @var string
	 */
	public $url;
	
	/**
	 * Request method.
	 * @var KalturaHttpNotificationMethod
	 */
	public $method;
	
	/**
	 * Data to send.
	 * 
	 * @var string
	 */
	public $data;
	
	/**
	 * The maximum number of seconds to allow cURL functions to execute.
	 * 
	 * @see CURLOPT_TIMEOUT
	 * @var int
	 */
	public $timeout;
	
	/**
	 * The number of seconds to wait while trying to connect.
	 * Must be larger than zero.
	 * 
	 * @see CURLOPT_CONNECTTIMEOUT
	 * @var int
	 */
	public $connectTimeout;
	
	/**
	 * A username to use for the connection.
	 * 
	 * @see CURLOPT_USERPWD
	 * @var string
	 */
	public $username;
	
	/**
	 * A password to use for the connection.
	 * 
	 * @see CURLOPT_USERPWD
	 * @var string
	 */
	public $password;
	
	/**
	 * The HTTP authentication method to use.
	 * 
	 * @see CURLOPT_HTTPAUTH
	 * @var KalturaHttpNotificationAuthenticationMethod
	 */
	public $authenticationMethod;
	
	/**
	 * The SSL version (2 or 3) to use.
	 * By default PHP will try to determine this itself, although in some cases this must be set manually.
	 * 
	 * @see CURLOPT_SSLVERSION
	 * @var KalturaHttpNotificationSslVersion
	 */
	public $sslVersion;
	
	/**
	 * SSL certificate to verify the peer with.
	 * 
	 * @see CURLOPT_CAINFO / CURLOPT_SSLCERT
	 * @var string
	 */
	public $sslCertificate;
	
	/**
	 * The format of the certificate.
	 * 
	 * @see CURLOPT_SSLCERTTYPE
	 * @var KalturaHttpNotificationCertificateType
	 */
	public $sslCertificateType;
	
	/**
	 * The password required to use the certificate.
	 * 
	 * @see CURLOPT_SSLCERTPASSWD
	 * @var string
	 */
	public $sslCertificatePassword;
	
	/**
	 * The identifier for the crypto engine of the private SSL key specified in ssl key.
	 * 
	 * @see CURLOPT_SSLENGINE
	 * @var string
	 */
	public $sslEngine;
	
	/**
	 * The identifier for the crypto engine used for asymmetric crypto operations.
	 * 
	 * @see CURLOPT_SSLENGINE_DEFAULT
	 * @var string
	 */
	public $sslEngineDefault;
	
	/**
	 * The key type of the private SSL key specified in ssl key - PEM / DER / ENG.
	 * 
	 * @see CURLOPT_SSLKEYTYPE
	 * @var KalturaHttpNotificationSslKeyType
	 */
	public $sslKeyType;
	
	/**
	 * Private SSL key.
	 * 
	 * @see CURLOPT_SSLKEY
	 * @var string
	 */
	public $sslKey;
	
	/**
	 * The secret password needed to use the private SSL key specified in ssl key.
	 * 
	 * @see CURLOPT_SSLKEYPASSWD
	 * @var string
	 */
	public $sslKeyPassword;
	
	/**
	 * Adds a e-mail custom header
	 * 
	 * @var KalturaKeyValueArray
	 */
	public $customHeaders;
	
	/**
	 * Define the content dynamic parameters
	 * @var KalturaKeyValueArray
	 */
	public $contentParameters;
	
	private static $map_between_objects = array
	(
		'url',
		'method',
		'data',
		'timeout',
		'connectTimeout',
		'username',
		'password',
		'authenticationMethod',
		'sslVersion',
		'sslCertificate',
		'sslCertificateType',
		'sslCertificatePassword',
		'sslEngine',
		'sslEngineDefault',
		'sslKeyType',
		'sslKey',
		'sslKeyPassword',
		'customHeaders',
		'contentParameters',
	);

	/* (non-PHPdoc)
	 * @see KalturaEventNotificationDispatchJobData::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject($srcObj)
	 */
	public function fromObject($srcObj)
	{
		/* @var $srcObj kHttpNotificationDispatchJobData */
		parent::fromObject($srcObj);
		
		if(is_null($this->data) && $srcObj->getDataObject())
		{
			$dataObject = KalturaHttpNotificationData::getInstance($srcObj->getDataObject());
			if($dataObject)
				$this->data = $dataObject->getData($srcObj);
		}
	}
}
