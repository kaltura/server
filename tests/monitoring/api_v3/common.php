<?php
require_once realpath(__DIR__ . '/../../') . '/lib/KalturaClient.php';
require_once realpath(__DIR__ . '/../') . '/KalturaMonitorResult.php';

$options = getopt('', array(
	'service-url:',
	'debug',
));

if(!isset($options['service-url']))
{
	echo "Argument service-url is required";
	exit(-1);
}

class KalturaMonitorClientLogger implements IKalturaLogger
{
	function log($msg)
	{
		echo "Client: $msg\n";
	}
}

class KalturaMonitorClient extends KalturaClient
{
	protected function doHttpRequest($url, $params = array(), $files = array())
	{
		$this->addParam($params, 'nocache', true);
		return parent::doHttpRequest($url, $params, $files);
	}
}

$config = parse_ini_file(__DIR__ . '/../config.ini', true);

$serviceUrl = $config['client-config']['protocol'] . '://' . $options['service-url'];
$clientConfig = new KalturaConfiguration();
$clientConfig->partnerId = null;
$clientConfig->serviceUrl = $serviceUrl;

foreach($config['client-config'] as $attribute => $value)
	$clientConfig->$attribute = $value;

if(isset($options['debug']))
	$clientConfig->setLogger(new KalturaMonitorClientLogger());

$client = new KalturaMonitorClient($clientConfig);
