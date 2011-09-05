<?php

define('START_MARKER', '[KalturaFrontController->run] DEBUG: Params [');

function print_r_reverse($in) {
    $lines = explode("\n", trim($in));
    if (trim($lines[0]) != 'Array') {
        // bottomed out to something that isn't an array
        return $in;
    } else {
        // this is an array, lets parse it
        if (preg_match("/(\s{5,})\(/", $lines[1], $match)) {
            // this is a tested array/recursive call to this function
            // take a set of spaces off the beginning
            $spaces = $match[1];
            $spaces_length = strlen($spaces);
            $lines_total = count($lines);
            for ($i = 0; $i < $lines_total; $i++) {
                if (substr($lines[$i], 0, $spaces_length) == $spaces) {
                    $lines[$i] = substr($lines[$i], $spaces_length);
                }
            }
        }
        array_shift($lines); // Array
        array_shift($lines); // (
        array_pop($lines); // )
        $in = implode("\n", $lines);
        // make sure we only match stuff with 4 preceding spaces (stuff for this array and not a nested one)
        preg_match_all("/^\s{4}\[(.+?)\] \=\> /m", $in, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
        $pos = array();
        $previous_key = '';
        $in_length = strlen($in);
        // store the following in $pos:
        // array with key = key of the parsed array's item
        // value = array(start position in $in, $end position in $in)
        foreach ($matches as $match) {
            $key = $match[1][0];
            $start = $match[0][1] + strlen($match[0][0]);
            $pos[$key] = array($start, $in_length);
            if ($previous_key != '') $pos[$previous_key][1] = $match[0][1] - 1;
            $previous_key = $key;
        }
        $ret = array();
        foreach ($pos as $key => $where) {
            // recursively see if the parsed out value is an array too
            $ret[$key] = print_r_reverse(substr($in, $where[0], $where[1] - $where[0]));
        }
        return $ret;
    }
}

function doCurl($url, $params = array(), $files = array())
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	if (count($files) > 0)
	{
		foreach($files as &$file)
			$file = "@".$file; // let curl know its a file
		curl_setopt($ch, CURLOPT_POSTFIELDS, array_merge($params, $files));
	}
	else
	{
		$opt = http_build_query($params, null, "&");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $opt);
	}
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, '');
	curl_setopt($ch, CURLOPT_TIMEOUT, 0);
		
	$result = curl_exec($ch);
	$curlError = curl_error($ch);
	curl_close($ch);
	return array($result, $curlError);
}

function stripXMLInvalidChars($value) 
{
	return preg_replace ( '/[^\t\n\r\x{20}-\x{d7ff}\x{e000}-\x{fffd}\x{10000}-\x{10ffff}]/u', "", $value );
}	

function xmlToArray($xmlstring)
{
	$xmlstring = stripXMLInvalidChars($xmlstring);
	$xml = simplexml_load_string($xmlstring);
	$json = json_encode($xml);
	$array = json_decode($json,TRUE);
	return $array;
}

function compareArrays($resultNew, $resultOld, $path)
{
	$errors = array();
	foreach ($resultOld as $key => $oldValue)
	{
		if (!array_key_exists($key, $resultNew))
		{
			$errors[] = "missing field $key (path=$path)";
			continue;
		}
		
		$newValue = $resultNew[$key];
		if (is_array($oldValue))
		{
			$errors = array_merge($errors, compareArrays($newValue, $oldValue, "$path/$key"));
		}
		else
		{
			if ($newValue != $oldValue)
			{
				$errors[] = "field $key has different value (path=$path new=$newValue old=$oldValue)";
			}
		}
	}
	
	return $errors;
}

function compareResults($resultNew, $resultOld)
{
	$resultNew = xmlToArray($resultNew);
	unset($resultNew["executionTime"]);

	$resultOld = xmlToArray($resultOld);
	unset($resultOld["executionTime"]);
	
	return compareArrays($resultNew, $resultOld, "");
}

function beginsWith($str, $end) 
{
	return (substr($str, 0, strlen($end)) === $end);
}

function processRequest($parsedParams)
{
	global $testedActions, $testedRequests;
	global $serviceUrlOld, $serviceUrlNew, $maxTestsPerActionType;

	
	if (!array_key_exists('service', $parsedParams))
	{
		print "Error: service not specified " . print_r($parsedParams, true) . "\n";
		return;
	}

	$service = $parsedParams['service'];
	if ($service == "multirequest")
	{
		return;
	}
	
	if (!array_key_exists('action', $parsedParams))
	{
		print "Error: action not specified " . print_r($parsedParams, true) . "\n";
		return;
	}
		
	$action = $parsedParams['action'];
	$fullActionName = strtolower("$service.$action");
	if (!beginsWith($fullActionName, 'playlist.execute') && 
		!in_array($action, array('get', 'list', 'count')))
	{
		return;
	}
	
	if (!array_key_exists($fullActionName, $testedActions))
	{
		$testedActions[$fullActionName] = 0;
	}
	
	if ($testedActions[$fullActionName] > $maxTestsPerActionType)
	{
		return;
	}

	unset($parsedParams['service']);
	unset($parsedParams['action']);
	$parsedParams['format'] = '2';		# XML
	
	$paramsForHash = $parsedParams;
	unset($paramsForHash['ks']);
	unset($paramsForHash['kalsig']);
	unset($paramsForHash['clientTag']);
	$requestHash = md5($fullActionName . serialize($paramsForHash));
	if (in_array($requestHash, $testedRequests))
	{
		return;
	}
	
	$testedRequests[] = $requestHash;
	$testedActions[$fullActionName]++;
	
	$uri = "/api_v3/index.php?service=$service&action=$action";
	
	print "Testing $service.$action...";
	
	usleep(200000);         // sleep for 0.2 sec to avoid hogging the server
	$beforeTime = microtime(true);
	list($resultNew, $curlErrorNew) = doCurl($serviceUrlNew . $uri, $parsedParams);
	$newTime = microtime(true) - $beforeTime;
	
	$beforeTime = microtime(true);
	list($resultOld, $curlErrorOld) = doCurl($serviceUrlOld . $uri, $parsedParams);
	$oldTime = microtime(true) - $beforeTime;
	
	if ($curlErrorNew || $curlErrorOld)
	{
		print "Curl error [$curlErrorNew] [$curlErrorOld]\n";
		return;
	}
	
	$errors = compareResults($resultNew, $resultOld);
	if (!count($errors))
	{
		print sprintf("Ok (new=%.3f old=%.3f)\n", $newTime, $oldTime);
		return;
	}
	
	print "\n-------------------------------------------------------------------------------\n";
	print "\tService = $service\n";
	print "\tAction = $action\n";
	print "\tParams = ".print_r($parsedParams, true)."\n";
	foreach ($errors as $error)
	{
		print "\tError: $error\n";
	}
}

// parse the command line
if ($argc < 4)
	die("Usage:\n\tphp compatCheck <old service url> <new service url> <api_v3 log> [<start position> [<end position> [<max tests per action>]]]\n");

$serviceUrlOld = $argv[1];
$serviceUrlNew = $argv[2];
$apiV3LogPath = $argv[3];

if (!beginsWith(strtolower($serviceUrlOld), 'http://'))
	$serviceUrlOld = 'http://' . $serviceUrlOld;
if (!beginsWith(strtolower($serviceUrlNew), 'http://'))
	$serviceUrlNew = 'http://' . $serviceUrlNew;

$startPosition = 0;
$endPosition = 0;
$maxTestsPerActionType = 10;

if ($argc > 4)
	$startPosition = intval($argv[4]);
if ($argc > 5)
	$endPosition = intval($argv[5]);
if ($argc > 6)
	$maxTestsPerActionType = intval($argv[6]);
	
// process the log file
$handle = @fopen($apiV3LogPath, "r");
if (!$handle)
	die('Error: failed to open log file');

$logStats = fstat($handle);
$origSize = $logStats['size'];

$testedActions = array();
$testedRequests = array();
$requestNumber = 0;
$inParams = false;
while (ftell($handle) < $origSize && ($buffer = fgets($handle)) !== false) {
	if (!$inParams)
	{
		$markerPos = strpos($buffer, START_MARKER);
		if ($markerPos === false)
			continue;
		$params = substr($buffer, $markerPos + strlen(START_MARKER));
		$inParams = true;
	}
	else
	{
		if ($buffer[0] == ']')
		{
			$parsedParams = print_r_reverse($params);
			if (print_r($parsedParams, true) != $params)
			{
				print "print_r_reverse failed\n";
				continue;
			}

			$requestNumber++;
			if ($endPosition != 0 && $requestNumber > $endPosition)
			{
				break;
			}
			
			if ($requestNumber > $startPosition)
			{
				processRequest($parsedParams);
			}
						
			$inParams = false;
		}
		else
		{
			$params .= $buffer;
		}
	}
}
fclose($handle);
