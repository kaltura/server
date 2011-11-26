<?php

define('PS2_START_MARKER', 'symfony [info] {sfRequest} request parameters ');
define('APIV3_START_MARKER', '[KalturaFrontController->run] DEBUG: Params [');

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
	if ($params)
	{
		curl_setopt($ch, CURLOPT_POST, 1);
	}
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

	$beforeTime = microtime(true);	
	$result = curl_exec($ch);
	$endTime = microtime(true);
	
	$curlError = curl_error($ch);
	curl_close($ch);
	return array($result, $curlError, $endTime - $beforeTime);
}

function stripXMLInvalidChars($value) 
{
	preg_match_all('/[^\t\n\r\x{20}-\x{d7ff}\x{e000}-\x{fffd}\x{10000}-\x{10ffff}]/u', $value, $invalidChars);
	$invalidChars = reset($invalidChars);
	if (count($invalidChars))
	{
		$value = str_replace($invalidChars, "", $value);
	}
	return $value;
}

function printStringDiff($string1, $string2)
{
	for ($i = 0; $i < strlen($string1); $i++)
	{
		if ($string1[$i] == $string2[$i])
			continue;
			
		print "Byte offset: $i\n";
		print "Char1: " . ord($string1[$i]) . "\n";
		print "Char2: " . ord($string2[$i]) . "\n";
		$start = 0;
		if ($i > 100)
			$start = $i - 100;
		print "String1: " . substr($string1, $start, 200) . "\n";
		print "String2: " . substr($string2, $start, 200) . "\n";
		break;
	}
}

function xmlToArray($xmlstring)
{
	// fix the xml if it's invalid
	$origstring = $xmlstring;
	$xmlstring = @iconv('utf-8', 'utf-8', $xmlstring);
	$xmlstring = stripXMLInvalidChars($xmlstring);
	$xmlstring = str_replace('&', '&amp;', $xmlstring);
	$xmlstring = str_replace(array('&amp;#', '&amp;lt;', '&amp;gt;', '&amp;quot;', '&amp;amp;', '&amp;apos;'), array('&#', '&lt;', '&gt;', '&quot;', '&amp;', '&apos;'), $xmlstring);
	if ($xmlstring != $origstring)
	{
		printStringDiff($xmlstring, $origstring);
		return null;
	}

	// parse the xml
	$xml = @simplexml_load_string($xmlstring);
	$json = json_encode($xml);
	$array = json_decode($json,TRUE);
	return $array;
}

$IGNORED_FIELDS = array(
	'/executionTime', 
	'/result/serverTime',
	'/debug/execute_impl_time', 
	'/debug/execute_time', 
	'/debug/total_time', 
	'/entry/@attributes/server_time', 
	);
	
function normalizeKS($value)
{
	$ksPos1 = strpos($value, '/ks/');
	$ksPos2 = strpos($value, '&ks=');
	if ($ksPos1 !== false)
	{
		$ksStartPos = $ksPos1;
		$endDelim = '/';
	}
	else if ($ksPos2 !== false)
	{
		$ksStartPos = $ksPos2;
		$endDelim = '&';
	}
	else
		return $value;
		
	$ksStartPos += 4;
	$ksEndPos = strpos($value, $endDelim, $ksStartPos);
	if ($ksEndPos == false)
	{
		$ksEndPos = strlen($value);
	}
	
	$ks = substr($value, $ksStartPos, $ksEndPos - $ksStartPos);
	$decodedKs = base64_decode($ks);
	list($sig, $ksFields) = explode('|', $decodedKs);
	$ksFields = explode(';', $ksFields);
	unset($ksFields[2]);		// valid until
	unset($ksFields[4]);		// rand
	$ksFields = implode(';', $ksFields);
	return str_replace($ks, $ksFields, $value);
}

function compareValues($newValue, $oldValue)
{
	global $serviceUrlNew, $serviceUrlOld;
	
	$newValue = str_replace($serviceUrlNew, $serviceUrlOld, $newValue);
	
	$newValue = normalizeKS($newValue);
	$oldValue = normalizeKS($oldValue);

	return $newValue == $oldValue;
}	
	
function compareArrays($resultNew, $resultOld, $path)
{
	global $IGNORED_FIELDS;
	
	$errors = array();
	foreach ($resultOld as $key => $oldValue)
	{
		if (!array_key_exists($key, $resultNew))
		{
			$errors[] = "missing field $key (path=$path)";
			continue;
		}
		
		$newValue = $resultNew[$key];
		if (is_array($oldValue) && is_array($newValue))
		{
			$errors = array_merge($errors, compareArrays($newValue, $oldValue, "$path/$key"));
		}
		else if (is_string($oldValue) && is_string($newValue))
		{
			if (in_array("$path/$key", $IGNORED_FIELDS))
			{
				continue;
			}
			
			if (!compareValues($newValue, $oldValue))
			{
				$errors[] = "field $key has different value (path=$path new=$newValue old=$oldValue)";
			}
		}
		else
		{
			$errors[] = "field $key has different type (path=$path new=$newValue old=$oldValue)";
		}
	}
	
	return $errors;
}

function compareResults($resultNew, $resultOld)
{
	$resultNew = xmlToArray($resultNew);
	$resultOld = xmlToArray($resultOld);

	if (!$resultNew || !$resultOld)
	{
		return array('failed to parse XMLs');
	}
	
	return compareArrays($resultNew, $resultOld, "");
}

function beginsWith($str, $end) 
{
	return (substr($str, 0, strlen($end)) === $end);
}

function getRequestHash($fullActionName, $paramsForHash)
{
	unset($paramsForHash['ks']);
	unset($paramsForHash['kalsig']);
	unset($paramsForHash['clientTag']);
	return md5($fullActionName . serialize($paramsForHash));
}

function shouldProcessRequest($fullActionName, $parsedParams)
{
	global $testedActions, $testedRequests, $maxTestsPerActionType;
	global $requestNumber, $startPosition, $endPosition;
	
	// test action type count
	if (!array_key_exists($fullActionName, $testedActions))
	{
		$testedActions[$fullActionName] = 0;
	}
	
	if ($maxTestsPerActionType && $testedActions[$fullActionName] > $maxTestsPerActionType)
	{
		return 'no';
	}
	
	// test whether this action was already tested
	$requestHash = getRequestHash($fullActionName, $parsedParams);
	if (in_array($requestHash, $testedRequests))
	{
		return 'no';
	}
	
	// apply start/end positions
	$requestNumber++;
	if ($endPosition != 0 && $requestNumber > $endPosition)
	{
		return 'quit';
	}
			
	if ($requestNumber <= $startPosition)
	{
		return 'no';
	}
	
	$testedRequests[] = $requestHash;
	$testedActions[$fullActionName]++;
	
	return 'yes';
}

function testAction($fullActionName, $parsedParams, $uri, $postParams = array())
{
	global $serviceUrlOld, $serviceUrlNew;
	
	print "Testing $fullActionName...";
	
	usleep(200000);         // sleep for 0.2 sec to avoid hogging the server
	
	for ($retries = 0; $retries < 3; $retries++)
	{
		list($resultNew, $curlErrorNew, $newTime) = doCurl($serviceUrlNew . $uri, $postParams);
		list($resultOld, $curlErrorOld, $oldTime) = doCurl($serviceUrlOld . $uri, $postParams);
		
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
		
		print "\nRetrying $fullActionName...";
		usleep(1000000);
	}
		
	print "\n-------------------------------------------------------------------------------\n";
	print "\tUrl = $serviceUrlNew$uri\n";
	print "\tPostParams = ".var_export($postParams, true)."\n";
	foreach ($errors as $error)
	{
		print "\tError: $error\n";
	}
}

function isRequestExpired($parsedParams)
{
	if (!array_key_exists('ks', $parsedParams))
	{
		return false;
	}
	
	$ks = $parsedParams['ks'];
	$ks = base64_decode($ks, true);
	@list($hash, $ks) = @explode ("|", $ks, 2);
	$ksParts = explode(";", $ks);
	if (count($ksParts) < 5)
	{
		return true;
	}
	
	list(
		$partnerId, 
		$partnerPattern, 
		$validUntil, 
		$type, 
		$rand, 
	) = $ksParts;
	
	return (time() >= $validUntil);
}

function processRequest($parsedParams)
{
	if (!array_key_exists('service', $parsedParams))
	{
		print "Error: service not specified " . print_r($parsedParams, true) . "\n";
		return;
	}

	$service = $parsedParams['service'];
	if (beginsWith(strtolower($service), "multirequest"))
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
	unset($parsedParams['service']);
	unset($parsedParams['action']);
	$parsedParams['format'] = '2';		# XML
	
	if (!beginsWith($fullActionName, 'playlist.execute') &&
		!beginsWith($action, 'get') &&
		!beginsWith($action, 'list') &&
		!beginsWith($action, 'count'))
	{
		return;
	}
	
	if (isRequestExpired($parsedParams))
	{
		return;
	}
	
	switch (shouldProcessRequest($fullActionName, $parsedParams))
	{
	case 'quit':
		return true;
		
	case 'no':
		return;
	}
	
	$uri = "/api_v3/index.php?service=$service&action=$action";
	
	testAction($fullActionName, $parsedParams, $uri, $parsedParams);
}

function processApiV3Log($handle, $origSize)
{
	$inParams = false;
	while (ftell($handle) < $origSize && ($buffer = fgets($handle)) !== false) {
		if (!$inParams)
		{
			$markerPos = strpos($buffer, APIV3_START_MARKER);
			if ($markerPos === false)
				continue;
			$params = substr($buffer, $markerPos + strlen(APIV3_START_MARKER));
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

				if (processRequest($parsedParams))
				{
					break;
				}
						
				$inParams = false;
			}
			else
			{
				$params .= $buffer;
			}
		}
	}
}

function processPS2Request($parsedParams)
{
	global $serviceUrlOld, $serviceUrlNew;

	if (!array_key_exists('module', $parsedParams) ||
		!array_key_exists('action', $parsedParams))
	{
		print "Error: module/action not specified " . print_r($parsedParams, true) . "\n";
	}

	$module = $parsedParams['module'];
	$action = $parsedParams['action'];
	unset($parsedParams['module']);
	unset($parsedParams['action']);
	if (isset($parsedParams['format']) && is_numeric($parsedParams['format']))
	{
		$parsedParams['format'] = '2';          # XML
	}
	
	if (strtolower($module) == 'partnerservices2' &&
		strtolower($action) == 'defpartnerservices2base')
	{
		$action = $parsedParams['myaction'];
		unset($parsedParams['myaction']);
	}
	
	$fullActionName = strtolower("$module.$action");
	
	if (!in_array($fullActionName, array(
		'extwidget.playmanifest', 
		'keditorservices.getmetadata', 
		'keditorservices.getentryinfo', 
		'partnerservices2.executeplaylist',
		'partnerservices2.getentries',
		'partnerservices2.getentry',
		'partnerservices2.getentryroughcuts',
		'partnerservices2.getkshow',
		'partnerservices2.getuiconf',
		'partnerservices2.getwidget',
		'partnerservices2.listentries',
		'partnerservices2.listkshows',
		'partnerservices2.listplaylists',
		)))
	{
		return;
	}
	
	switch (shouldProcessRequest($fullActionName, $parsedParams))
	{
	case 'quit':
		return true;
		
	case 'no':
		return;
	}
	
	$uri = "/index.php/$module/$action?" . http_build_query($parsedParams, null, "&");

	testAction($fullActionName, $parsedParams, $uri);
}

function processPS2Log($handle, $origSize)
{
	while (ftell($handle) < $origSize && ($buffer = fgets($handle)) !== false) {
		$markerPos = strpos($buffer, PS2_START_MARKER);
		if ($markerPos === false)
			continue;
		$params = substr($buffer, $markerPos + strlen(PS2_START_MARKER));
		$parsedParams = eval('return ' . trim($params) . ';');

		if (processPS2Request($parsedParams))
		{
			break;
		}
	}
}

// parse the command line
if ($argc < 5)
	die("Usage:\n\tphp compatCheck <old service url> <new service url> <api log> <api_v3/ps2> [<start position> [<end position> [<max tests per action>]]]\n");

$serviceUrlOld = $argv[1];
$serviceUrlNew = $argv[2];
$apiV3LogPath = $argv[3];
$logFormat = strtolower($argv[4]);

if (!in_array($logFormat, array('api_v3', 'ps2')))
	die("Log format shoud be either api_v3 or ps2");

if (!beginsWith(strtolower($serviceUrlOld), 'http://'))
	$serviceUrlOld = 'http://' . $serviceUrlOld;
if (!beginsWith(strtolower($serviceUrlNew), 'http://'))
	$serviceUrlNew = 'http://' . $serviceUrlNew;

$startPosition = 0;
$endPosition = 0;
$maxTestsPerActionType = 10;

if ($argc > 5)
	$startPosition = intval($argv[5]);
if ($argc > 6)
	$endPosition = intval($argv[6]);
if ($argc > 7)
	$maxTestsPerActionType = intval($argv[7]);

// init globals
$testedActions = array();
$testedRequests = array();
$requestNumber = 0;
	
// process the log file
$handle = @fopen($apiV3LogPath, "r");
if (!$handle)
	die('Error: failed to open log file');

$logStats = fstat($handle);
$origSize = $logStats['size'];

if ($logFormat == 'api_v3')
	processApiV3Log($handle, $origSize);
else
	processPS2Log($handle, $origSize);

fclose($handle);
