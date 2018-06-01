<?php
require_once realpath(__DIR__ . '/../') . '/KalturaMonitorResult.php';

class KalturaMonitorClientPs2
{
	const RESPONSE_TYPE_JSON = 1;
	const RESPONSE_TYPE_XML = 2;
	const RESPONSE_TYPE_PHP = 3;
	const RESPONSE_TYPE_PHP_ARRAY = 4;
	const RESPONSE_TYPE_PHP_OBJECT = 5;
	const RESPONSE_TYPE_RAW = 6;
	const RESPONSE_TYPE_HTML = 7;
	const RESPONSE_TYPE_MRSS = 8;
	
	protected $debug				= false;
	protected $curlTimeout			= 10;
	protected $userAgent			= '';
	protected $proxyHost			= null;
	protected $proxyPort			= null;
	protected $proxyUser			= null;
	protected $proxyPassword		= '';
	protected $proxyType			= 'HTTP';
	protected $verifySSL			= true;
	protected $sslCertificatePath	= null;
	protected $serviceUrl			= null;
	
	public function __construct(array $config, array $options)
	{
		$this->serviceUrl = $config['config']['protocol'] . '://' . $options['service-url'] . ':' . $config['config']['port'];
		
		if(isset($options['debug']))
			$this->debug = true;
		
		foreach($config['config'] as $attribute => $value)
			$this->$attribute = $value;
	}
	
	public function request($action, array $params = array())
	{
		$params['format'] = self::RESPONSE_TYPE_PHP;
		$params['nocache'] = true;
		
		$url = "$this->serviceUrl/index.php/partnerservices2/$action";
		if($this->debug)
		{
			echo "URL: $url\n";
			echo "POST Params: " . print_r($params, true) . "\n";
		}
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->curlTimeout);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params, null, '&'));
		
		if($this->userAgent)
			curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
	
		if (isset($this->proxyHost)) {
			curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true);
			curl_setopt($ch, CURLOPT_PROXY, $this->proxyHost);
			if (isset($this->proxyPort)) {
				curl_setopt($ch, CURLOPT_PROXYPORT, $this->proxyPort);
			}
			if (isset($this->proxyUser)) {
				curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->proxyUser.':'.$this->proxyPassword);
			}
			if (isset($this->proxyType) && $this->proxyType === 'SOCKS5') {
				curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
			}
		}
	
		// Set SSL verification
		if(!$this->verifySSL)
		{
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		}
		elseif(!$this->sslCertificatePath)
		{
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
			curl_setopt($ch, CURLOPT_CAINFO, $this->sslCertificatePath);
		}
		
		$response = curl_exec($ch);
		
		$errorMessage = null;
		try
		{
			$xml = @new SimpleXMLElement($response);
			if(isset($xml->error) && isset($xml->error->num_0) && isset($xml->error->num_0->desc))
				$errorMessage = strval($xml->error->num_0->desc);
		}
		catch(Exception $e)
		{
			$result = @unserialize($response);

			if ($result === false && serialize(false) !== $response)
			{
				$errorMessage = "failed to unserialize server result\n$response";
			}
			else
			{
				return $result;
			}
		}
		
		throw new Exception($errorMessage);
	}
}

$options = getopt('', array(
	'service-url:',
	'debug',
));

if(!isset($options['service-url']))
{
	echo "Argument service-url is required";
	exit(-1);
}

$config = parse_ini_file(__DIR__ . '/../config.ini', true);

$client = new KalturaMonitorClientPs2($config, $options);
