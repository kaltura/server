<?php


/* --------  Validates arguments -------- */

$exampleMessage = "\tphp " . basename(__FILE__) . " input [output]\n";
$exampleMessage .= 	"For example\n";
$exampleMessage .= 	"\tphp " . basename(__FILE__) . " in.xml out.xml\n";

if($argc < 2)
{
	echo "Missing attributes, see usage:\n";
	echo $exampleMessage;
	exit(-1);
}

$inFile = $argv[1];
$outFile = null;
if(isset($argv[2]))
	$outFile = $argv[2];

if(!file_exists($inFile))
{
	echo "Input file is missing [$inFile]\n";
	exit(-1);
}

/* --------  Parse input XML -------- */

$xmlFormat = '<xml>
	<request service="serviceName" action="actionName" plugin="pluginName">
		<itemName1 objectType="KalturaObjectClass1">
			<attr1>value1</attr1>
			<attr2>value2</attr2>
		</itemName1>
		<itemName2 objectType="KalturaObjectClass2">
			<attr1>value1</attr1>
			<attr2>value2</attr2>
		</itemName2>
	</request>
	<multirequest>
		<request service="serviceName" action="actionName" plugin="pluginName">
			<itemName1 objectType="KalturaObjectClass1">
				<attr1>value1</attr1>
				<attr2>value2</attr2>
			</itemName1>
			<itemName2 objectType="KalturaObjectClass2">
				<attr1>value1</attr1>
				<attr2>value2</attr2>
			</itemName2>
		</request>
	</multirequest>
</xml>';

$inXml = new SimpleXMLElement(file_get_contents($inFile));
if($inXml->getName() != 'xml' || (!isset($inXml->request) && !isset($inXml->multirequest)))
{
	echo "Input file must match xml format [$xmlFormat]\n";
	exit(-1);
}

function parseInputArray(SimpleXMLElement $items)
{
	$array = array();
	foreach($items as $item)
		$array[] = parseInputObject($item);

	return $array;
}

function parseInputObject(SimpleXMLElement $input = null)
{
	global $inFile;

	if(is_null($input))
		return null;
		
	if(isset($input['null']) && $input['null'])
		return KalturaClient::getKalturaNullValue();
	
	$type = 'string';
	if(isset($input['objectType']))
		$type = strval($input['objectType']);

	$value = strval($input);
	
	$matches = null;
	if(preg_match('/\{prompt:([^}]+)\}/', $value, $matches))
	{
		$userInput = askForUserParameter($matches[1]);
		$value = preg_replace('/\{prompt:[^}]+\}/', $userInput, $value);
	}
	
	if(isset($input['path']))
	{
		$path = $input['path'];
		if(!file_exists($path))
		{
			$path = dirname($inFile) . '/' . $path;
			if(!file_exists($path))
			{
				echo "File [$path] could not be found\n";
				exit(-1);
			}
		}
		$value = file_get_contents($path);
	}

	switch($type)
	{
		case 'string':
			return $value;

		case 'int':
			return intval($value);

		case 'bool':
			return (bool)($value);

		case 'file':
			if(file_exists($value))
				return realpath($value);

			$value = dirname($inFile) . '/' . $value;
			if(file_exists($value))
				return realpath($value);

			echo "File [$value] could not be found\n";
			exit(-1);

		case 'array':
			return parseInputArray($input->item);
	}

	if(!class_exists($type))
	{
		echo "Type [$type] could not be found\n";
		exit(-1);
	}

	$object = new $type();
	$properties = $input->children();
	foreach($properties as $property)
	{
		/* @var $property SimpleXMLElement */
		$propertyName = $property->getName();
		$object->$propertyName = parseInputObject($property);
	}
	return $object;
}


function generateSession($adminSecretForSigning, $userId, $type, $partnerId, $expiry, $privileges)
{
	$rand = rand(0, 32000);
	$expiry = time()+$expiry;
	$fields = array (
		$partnerId ,
		$partnerId ,
		$expiry ,
		$type,
		$rand ,
		$userId ,
		$privileges
	);
	$info = implode ( ";" , $fields );

	$signature = sha1($adminSecretForSigning . $info);
	$strToHash =  $signature . "|" . $info ;
	$encoded_str = base64_encode( $strToHash );

	return $encoded_str;
}

function executeRequest(KalturaClient $client, SimpleXMLElement $request)
{
	$arguments = array();
	$inputs = $request->children();
	foreach($inputs as $input)
		$arguments[$input->getName()] = parseInputObject($input);

	$serviceName = parseInputObject($request['service']);
	$actionName = parseInputObject($request['action']);
	$impersonatePartner = isset($request['partnerId']) ? parseInputObject($request['partnerId']) : null;
	$pluginName = ucfirst(parseInputObject($request['plugin']));

	if(isset($pluginName) && $pluginName != '') //get plugin service
	{
		$pluginClass = "Kaltura{$pluginName}ClientPlugin";
		require_once realpath(__DIR__ . '/../') . "/lib/KalturaPlugins/$pluginClass.php";

		$plugin = call_user_func(array($pluginClass, 'get'), $client);
		$service = $plugin->$serviceName;
	}
	else //get core service
	{
		$services = get_object_vars($client);
		if(!isset($services[$serviceName]))
		{
			echo "Service [$serviceName] not found\n";
			exit(-1);
		}

		$service = $services[$serviceName];
	}

	if(!method_exists($service, $actionName))
	{
		echo "Action [$actionName] not found on service [$serviceName]\n";
		exit(-1);
	}

	$result = null;
	try
	{
		if (!is_null($impersonatePartner))
		{
			$config = $client->getConfig();
			$configPartnerId = $config->partnerId;
			$config->partnerId = $impersonatePartner;
		}
		if ($client->isMultiRequest())
			$doing = 'Queuing';
		else
			$doing = 'Executing';
		echo "$doing action [$serviceName.$actionName]\n";
		$result = call_user_func_array(array($service, $actionName), $arguments);
		if (!is_null($impersonatePartner))
		{
			$config = $client->getConfig();
			$config->partnerId = $configPartnerId;
		}
	}
	catch(Exception $e)
	{
		echo "Executing failed [" . $e->getMessage() . "]\n";
		$result = $e;
	}
	return $result;
}

function askForUserParameter($message)
{
	fwrite(STDERR, "$message ");
	return trim(fgets(STDIN));
}

require_once realpath(__DIR__ . '/../') . '/lib/KalturaClient.php';

class KalturaStandAloneTestLogger implements IKalturaLogger
{
	function log($msg)
	{
		echo "$msg\n";
	}
}

$config = new KalturaConfiguration();
$config->setLogger(new KalturaStandAloneTestLogger());
if(isset($inXml->config))
{
	$configs = $inXml->config->children();
	foreach($configs as $configItem)
	{
		$configItemName = $configItem->getName();
		$config->$configItemName = parseInputObject($configItem);
	}
}

$client = new KalturaClient($config);
if(isset($inXml->session))
{
	$partnerId = parseInputObject($inXml->session->partnerId);
	$secret = parseInputObject($inXml->session->secret);
	$sessionType = parseInputObject($inXml->session->sessionType);
	$userId = isset($inXml->session->userId) ? parseInputObject($inXml->session->userId) : '';
	$expiry = isset($inXml->session->expiry) ? parseInputObject($inXml->session->expiry) : 86400;
	$privileges = isset($inXml->session->privileges) ? parseInputObject($inXml->session->privileges) : '';
	$email = isset($inXml->session->email) ? $inXml->session->email : null;
	$password = isset($inXml->session->password) ? $inXml->session->password : null;

	if ($secret)
	{
		$ks = generateSession($secret, $userId, $sessionType, $partnerId, $expiry, $privileges);
	}
	else
	{
		if (!$email)
		{
			$email = askForUserParameter('Partner email address:');
		}
		if (!$password)
		{
			$password = askForUserParameter('Partner password:');
		}
		$ks = $client->user->loginByLoginId($email, $password);
	}
	$client->setKs($ks);
}

$results = array();
foreach($inXml->children() as $element)
{
	/** @var $element SimpleXMLElement */
	if ($element->getName() == 'multirequest')
	{
		$client->startMultiRequest();
		foreach($element->request as $request)
		{
			executeRequest($client, $request);
		}
		echo "Executing multirequest\n";
		$multiResponse = $client->doMultiRequest();
		foreach($multiResponse as $index => $response)
		{
			if ($client->isError($response))
			{
				echo "Executing failed for request #".($index+1)." with error [" . $response['message'] . "]\n";
				throw new KalturaException($response["message"], $response["code"]);
			}

			$results[] = $response;
		}
	}
	elseif ($element->getName() == 'request')
	{
		$results[] = executeRequest($client, $element);
	}
}


/* --------  Generate output XML -------- */

function appandObject(SimpleXMLElement $outXml, $object, $name)
{
	$objectXml = null;

	$type = gettype($object);
	if($type == 'array')
	{
		$objectXml = $outXml->addChild($name);
		$objectXml->addAttribute('objectType', 'array');
		foreach($object as $arrayItem)
			appandObject($objectXml, $arrayItem, 'item');
	}
	elseif($type == 'object')
	{
		$objectXml = $outXml->addChild($name);
		$objectXml->addAttribute('objectType', get_class($object));
		$attributes = get_object_vars($object);
		foreach($attributes as $attributeName => $attributes)
			appandObject($objectXml, $attributes, $attributeName);
	}
	else
	{
		$objectXml = $outXml->addChild($name, htmlentities($object));
	}
}

if(!$outFile)
	exit(0);

$outXml = new SimpleXMLElement('<xml/>');
$outXmlResult = $outXml->addChild('output');

foreach($results as $result)
	appandObject($outXmlResult, $result, 'response');

$outXml->saveXML($outFile);

exit(0);
