<?php
/**
 * @package plugins.httpNotification
 * @subpackage model.data
 */
class kHttpNotificationDispatchJobData extends kEventNotificationDispatchJobData
{
	/**
	 * Remote server URL
	 * @var string
	 */
	protected $url;
	
	/**
	 * Request method.
	 * @var int
	 */
	protected $method;
	
	/**
	 * Data to send.
	 * 
	 * @var kHttpNotificationData
	 */
	protected $dataObject;
	
	/**
	 * Data to send.
	 * 
	 * @var string
	 */
	protected $data;
	
	/**
	 * The maximum number of seconds to allow cURL functions to execute.
	 * 
	 * @var int
	 */
	protected $timeout;
	
	/**
	 * The number of seconds to wait while trying to connect.
	 * Must be larger than zero.
	 * 
	 * @var int
	 */
	protected $connectTimeout;
	
	/**
	 * A username to use for the connection.
	 * 
	 * @var string
	 */
	protected $username;
	
	/**
	 * A password to use for the connection.
	 * 
	 * @var string
	 */
	protected $password;
	
	/**
	 * The HTTP authentication method to use.
	 * 
	 * @var int
	 */
	protected $authenticationMethod;
	
	/**
	 * The SSL version (2 or 3) to use.
	 * By default PHP will try to determine this itself, although in some cases this must be set manually.
	 * 
	 * @var int
	 */
	protected $sslVersion;
	
	/**
	 * SSL certificate to verify the peer with.
	 * 
	 * @var string
	 */
	protected $sslCertificate;
	
	/**
	 * The format of the certificate.
	 * 
	 * @var string
	 */
	protected $sslCertificateType;
	
	/**
	 * The password required to use the certificate.
	 * 
	 * @var string
	 */
	protected $sslCertificatePassword;
	
	/**
	 * The identifier for the crypto engine of the private SSL key specified in ssl key.
	 * 
	 * @var string
	 */
	protected $sslEngine;
	
	/**
	 * The identifier for the crypto engine used for asymmetric crypto operations.
	 * 
	 * @var string
	 */
	protected $sslEngineDefault;
	
	/**
	 * The key type of the private SSL key specified in ssl key - PEM / DER / ENG.
	 * 
	 * @var string
	 */
	protected $sslKeyType;
	
	/**
	 * Private SSL key.
	 * 
	 * @var string
	 */
	protected $sslKey;
	
	/**
	 * The secret password needed to use the private SSL key specified in ssl key.
	 * 
	 * @var string
	 */
	protected $sslKeyPassword;
	
	/**
	 * Adds a e-mail custom header
	 * 
	 * @var array
	 */
	protected $customHeaders;
	
	/**
	 * Define the content dynamic parameters
	 * @var array
	 */
	protected $contentParameters;
	
	/**
	 * @return string $url
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * @return int $method
	 */
	public function getMethod()
	{
		return $this->method;
	}

	/**
	 * @return kHttpNotificationData $dataObject
	 */
	public function getDataObject()
	{
		return $this->dataObject;
	}
	
	/**
	 * @return string $data
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * @return int $timeout
	 */
	public function getTimeout()
	{
		return $this->timeout;
	}

	/**
	 * @return int $connectTimeout
	 */
	public function getConnectTimeout()
	{
		return $this->connectTimeout;
	}

	/**
	 * @return string $username
	 */
	public function getUsername()
	{
		return $this->username;
	}

	/**
	 * @return string $password
	 */
	public function getPassword()
	{
		return $this->password;
	}

	/**
	 * @return int $authenticationMethod
	 */
	public function getAuthenticationMethod()
	{
		return $this->authenticationMethod;
	}

	/**
	 * @return int $sslVersion
	 */
	public function getSslVersion()
	{
		return $this->sslVersion;
	}

	/**
	 * @return string $sslCertificate
	 */
	public function getSslCertificate()
	{
		return $this->sslCertificate;
	}

	/**
	 * @return string $sslCertificateType
	 */
	public function getSslCertificateType()
	{
		return $this->sslCertificateType;
	}

	/**
	 * @return string $sslCertificatePassword
	 */
	public function getSslCertificatePassword()
	{
		return $this->sslCertificatePassword;
	}

	/**
	 * @return string $sslEngine
	 */
	public function getSslEngine()
	{
		return $this->sslEngine;
	}

	/**
	 * @return string $sslEngineDefault
	 */
	public function getSslEngineDefault()
	{
		return $this->sslEngineDefault;
	}

	/**
	 * @return string $sslKeyType
	 */
	public function getSslKeyType()
	{
		return $this->sslKeyType;
	}

	/**
	 * @return string $sslKey
	 */
	public function getSslKey()
	{
		return $this->sslKey;
	}

	/**
	 * @return string $sslKeyPassword
	 */
	public function getSslKeyPassword()
	{
		return $this->sslKeyPassword;
	}

	/**
	 * @return array $customHeaders
	 */
	public function getCustomHeaders()
	{
		return $this->customHeaders;
	}

	/**
	 * @return array $contentParameters
	 */
	public function getContentParameters()
	{
		return $this->contentParameters;
	}

	/**
	 * @param string $url
	 */
	public function setUrl($url)
	{
		$this->url = $url;
	}

	/**
	 * @param int $method
	 */
	public function setMethod($method)
	{
		$this->method = $method;
	}

	/**
	 * @param kHttpNotificationData $dataObject
	 */
	public function setDataObject(kHttpNotificationData $dataObject = null)
	{
		$this->data = null;
		$this->dataObject = $dataObject;
	}
	
	/**
	 * @param string $data
	 */
	public function setData($data = null)
	{
		$this->data = $data;
	}

	/**
	 * @param int $timeout
	 */
	public function setTimeout($timeout)
	{
		$this->timeout = $timeout;
	}

	/**
	 * @param int $connectTimeout
	 */
	public function setConnectTimeout($connectTimeout)
	{
		$this->connectTimeout = $connectTimeout;
	}

	/**
	 * @param string $username
	 */
	public function setUsername($username)
	{
		$this->username = $username;
	}

	/**
	 * @param string $password
	 */
	public function setPassword($password)
	{
		$this->password = $password;
	}

	/**
	 * @param int $authenticationMethod
	 */
	public function setAuthenticationMethod($authenticationMethod)
	{
		$this->authenticationMethod = $authenticationMethod;
	}

	/**
	 * @param int $sslVersion
	 */
	public function setSslVersion($sslVersion)
	{
		$this->sslVersion = $sslVersion;
	}

	/**
	 * @param string $sslCertificate
	 */
	public function setSslCertificate($sslCertificate)
	{
		$this->sslCertificate = $sslCertificate;
	}

	/**
	 * @param string $sslCertificateType
	 */
	public function setSslCertificateType($sslCertificateType)
	{
		$this->sslCertificateType = $sslCertificateType;
	}

	/**
	 * @param string $sslCertificatePassword
	 */
	public function setSslCertificatePassword($sslCertificatePassword)
	{
		$this->sslCertificatePassword = $sslCertificatePassword;
	}

	/**
	 * @param string $sslEngine
	 */
	public function setSslEngine($sslEngine)
	{
		$this->sslEngine = $sslEngine;
	}

	/**
	 * @param string $sslEngineDefault
	 */
	public function setSslEngineDefault($sslEngineDefault)
	{
		$this->sslEngineDefault = $sslEngineDefault;
	}

	/**
	 * @param string $sslKeyType
	 */
	public function setSslKeyType($sslKeyType)
	{
		$this->sslKeyType = $sslKeyType;
	}

	/**
	 * @param string $sslKey
	 */
	public function setSslKey($sslKey)
	{
		$this->sslKey = $sslKey;
	}

	/**
	 * @param string $sslKeyPassword
	 */
	public function setSslKeyPassword($sslKeyPassword)
	{
		$this->sslKeyPassword = $sslKeyPassword;
	}

	/**
	 * @param array $customHeaders
	 */
	public function setCustomHeaders(array $customHeaders)
	{
		$this->customHeaders = $customHeaders;
	}

	/**
	 * @param array $contentParameters
	 */
	public function setContentParameters(array $contentParameters)
	{
		$this->contentParameters = $contentParameters;
	}
}
