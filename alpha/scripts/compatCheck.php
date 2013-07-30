<?php

require_once(dirname(__FILE__) . '/../apps/kaltura/lib/request/kSessionBase.class.php');

ini_set("memory_limit", "2048M");

define('PS2_START_MARKER', '[sfWebRequest->loadParameters] INFO: {sfRequest} request parameters ');
define('APIV3_START_MARKER', '[KalturaFrontController->run] DEBUG: Params [');
define('APIV3_GETFEED_MARKER', '[syndicationFeedRenderer] [global] DEBUG: getFeed Params [');

define('DB_HOST_NAME', 'dbgoeshere');
define('DB_USER_NAME', 'root');
define('DB_PASSWORD', 'root');

define('IP_ADDRESS_SALT', '');

$PS2_TESTED_XML_ACTIONS = array(
		'extwidget.playmanifest', 
		'keditorservices.getmetadata', 
		'keditorservices.getentryinfo', 
		'partnerservices2.executeplaylist',
		'partnerservices2.getentries',
		'partnerservices2.getallentries',
		'partnerservices2.getentry',
		'partnerservices2.getentryroughcuts',
		'partnerservices2.getkshow',
		'partnerservices2.getuiconf',
		'partnerservices2.getwidget',
		'partnerservices2.listentries',
		'partnerservices2.listkshows',
		'partnerservices2.listplaylists',
		'extwidget.embedIframeJs',
		);

$PS2_TESTED_BIN_ACTIONS = array(
		'extwidget.serveFlavor',
		'extwidget.kwidget',
		'extwidget.thumbnail',
		'extwidget.download',
		'keditorservices.flvclipper',
		'extwidget.raw',	
		);

$APIV3_TESTED_SERVICES = array(
		'*',

		// Entries services:
//		'baseEntry',
//		'data',
//		'document',
//		'liveStream',
//		'media',
//		'mixing',
//		'playlist',
//		'document_documents',
//		'externalMedia_externalMedia',
);

$APIV3_TESTED_ACTIONS = array(
		'syndicationFeed.execute',			// api_v3/getFeed.php
		'playlist.execute',
		'*.get',
		'*.list',
		'*.count',
		'*.serve',
		'*.goto',
		'*.search',
		);

$APIV3_BLOCKED_ACTIONS = array(
		'*.getexclusive',
		);

$KS_PATTERNS = array('/\/ks\/([a-zA-Z0-9+_\-]+=*)/', '/&ks=([a-zA-Z0-9+\/_\-]+=*)/', '/\?ks=([a-zA-Z0-9+\/_\-]+=*)/');

// compare modes
define('CM_XML', 0);
define('CM_BINARY', 1);
define('CM_WIDGET', 2);

define('MAX_BINARY_DIFFS', 5);

$ID_FIELDS = array('id', 'guid', 'loc', 'title', 'link');

class PartnerSecretPool
{
	protected $secrets = array();
	
	/**
	 * @var resource
	 */
	protected $link;

	public function __construct()
	{
		$this->link = mysql_connect(DB_HOST_NAME, DB_USER_NAME, DB_PASSWORD)
			or die('Error: Could not connect: ' . mysql_error() . "\n");

		mysql_select_db('kaltura', $this->link) or die("Error: Could not select 'kaltura' database\n");
	}

	public function __destruct()
	{
		mysql_close($this->link);
	}

	public function getPartnerSecret($partnerId)
	{
		if (isset($this->secrets[$partnerId]))
			return $this->secrets[$partnerId];
		if (!is_numeric($partnerId))
			return null;
			
		$query = "SELECT admin_secret FROM partner WHERE id='{$partnerId}'";
		$result = mysql_query($query, $this->link) or die('Error: Select from func table query failed: ' . mysql_error() . "\n");
		$line = mysql_fetch_array($result, MYSQL_NUM);
		if (!$line)
			return null;
		$this->secrets[$partnerId] = $line[0];
		return $line[0];
	}
}

class ks extends kSessionBase
{
	protected function getKSVersionAndSecret($partnerId)
	{
		global $partnerSecretPool;
		/* @var $partnerSecretPool PartnerSecretPool */

		$adminSecret = $partnerSecretPool->getPartnerSecret($partnerId);
		if (!$adminSecret)
			return null;
		return array(1, $adminSecret);
	}
}

function extendKsExpiry($ks)
{
	global $partnerSecretPool;
	/* @var $partnerSecretPool PartnerSecretPool */
	
	$ksObj = new ks();
	if (!$ksObj->parseKS($ks))
		return null;
	
	$adminSecret = $partnerSecretPool->getPartnerSecret($ksObj->partner_id);
	if (!$adminSecret)
		return null;
		
	return kSessionBase::generateKsV1($adminSecret, $ksObj->user, $ksObj->type, $ksObj->partner_id, 86400, $ksObj->privileges, $ksObj->master_partner_id, $ksObj->additional_data);
}

function print_r_reverse($in) {
    $lines = explode("\n", trim($in));
    if (trim($lines[0]) != 'Array') {
        // bottomed out to something that isn't an array
        return $in;
    } else {
        // this is an array, lets parse it
        if (preg_match('/(\s{5,})\(/', $lines[1], $match)) {
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
        preg_match_all('/^\s{4}\[(.+?)\] \=\> /m', $in, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
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

function getSignedIpHeader($ipAddress)
{
	if (!IP_ADDRESS_SALT)
		return array();
	
	$salt = IP_ADDRESS_SALT;
	$curTime = time();
	$uniqId = rand(1,32767);
	$baseHeader = array($ipAddress, $curTime, $uniqId);
	$headerHash = md5(implode(',', $baseHeader) . ',' . $salt);
	$ipHeader = implode(',', $baseHeader) . ',' . $headerHash;
	return array("X_KALTURA_REMOTE_ADDR: $ipHeader");
}

function doCurl($url, $params = array(), $files = array(), $range = null, $requestHeaders = array())
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
	else if ($params)
	{
		$opt = http_build_query($params, null, "&");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $opt);
	}
	if (!is_null($range))
	{
		curl_setopt($ch, CURLOPT_RANGE, $range);
	}
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, '');
	curl_setopt($ch, CURLOPT_TIMEOUT, 0);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders );
	
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

function stripInvalidUtf8Chars($value)
{
	$regex = <<<'END'
/
  (
    (?: [\x00-\x7F]                 # single-byte sequences   0xxxxxxx
    |   [\xC0-\xDF][\x80-\xBF]      # double-byte sequences   110xxxxx 10xxxxxx
    |   [\xE0-\xEF][\x80-\xBF]{2}   # triple-byte sequences   1110xxxx 10xxxxxx * 2
    |   [\xF0-\xF7][\x80-\xBF]{3}   # quadruple-byte sequence 11110xxx 10xxxxxx * 3
    ){1,100}                        # ...one or more times
  )
| .                                 # anything else
/x
END;
	return preg_replace($regex, '$1', $value);
	
}

function xmlToArray($xmlstring)
{
	// fix the xml if it's invalid
	$origstring = $xmlstring;
	$xmlstring = stripInvalidUtf8Chars($xmlstring);
	$xmlstring = stripXMLInvalidChars($xmlstring);
	$xmlstring = str_replace('&', '&amp;', $xmlstring);
	$xmlstring = str_replace(array('&amp;#', '&amp;lt;', '&amp;gt;', '&amp;quot;', '&amp;amp;', '&amp;apos;'), array('&#', '&lt;', '&gt;', '&quot;', '&amp;', '&apos;'), $xmlstring);
	if ($xmlstring != $origstring)
	{
		//printStringDiff($xmlstring, $origstring);
		//return null;
	}

	// parse the xml
	$xml = @simplexml_load_string($xmlstring);
	$json = json_encode($xml);
	$array = json_decode($json,TRUE);
	return $array;
}

function normalizeKS($value, $ks)
{
	$ksObj = new ks();
	if (!$ksObj->parseKS($ks))
		return $value;
		
	$ksFields = array(
		$ksObj->partner_id,
		$ksObj->partner_id,
		0,		// expiry
		$ksObj->type,
		0,		// rand
		$ksObj->user,
		$ksObj->privileges,
		$ksObj->master_partner_id,
		$ksObj->additional_data,
	);

	$ksFields = implode(';', $ksFields);
	return str_replace($ks, $ksFields, $value);
}

function compareValues($newValue, $oldValue)
{
	return $newValue == $oldValue;
}	
	
function compareArraysInternal($resultNew, $resultOld, $path)
{
	global $ID_FIELDS;

	$errors = array();
	foreach ($resultOld as $key => $oldValue)
	{
		$subPath = "$path/$key";
		
		if (!array_key_exists($key, $resultNew))
		{
			$errors[$subPath] = "missing field $key (path=$path)";
			continue;
		}
		
		$newValue = $resultNew[$key];
		if (is_array($oldValue) && is_array($newValue))
		{
			$errors = array_merge($errors, compareArrays($newValue, $oldValue, $subPath));
		}
		else if (is_string($oldValue) && is_string($newValue))
		{
			if (!compareValues($newValue, $oldValue))
			{
				$errors[$subPath] = "field $key has different value (path=$path new=$newValue old=$oldValue)";
				if (in_array($key, $ID_FIELDS))
					break;		// id is different, all other fields will be different as well
			}
		}
		else
		{
			$errors[$subPath] = "field $key has different type (path=$path new=$newValue old=$oldValue)";
		}
	}

	return $errors;
}

function compareArraysById($item1, $item2)
{
	global $ID_FIELDS;

	if (!is_array($item1) || !is_array($item2))
		return 0;
	
	foreach ($ID_FIELDS as $idField)
	{
		if (isset($item1[$idField]) && isset($item2[$idField]) && 
			$item1[$idField] != $item2[$idField])
			return strcmp($item1[$idField], $item2[$idField]);
	}
	
	return 0;
}
	
function getCommonPrefixBase($string1, $string2)
{
	$left = 0;
	$right = strlen($string1);
	while ($left < $right)
	{
		$mid = ceil(($right + $left) / 2);
		if (substr($string1, 0, $mid) == substr($string2, 0, $mid))
			$left = $mid;
		else
			$right = $mid - 1;
	}
	return substr($string1, 0, $left);
}

function getCommonPrefix(array $strings)
{
	$prefix = getCommonPrefixBase(reset($strings), next($strings));
	for (;;)
	{
		$curString = next($strings);
		if ($curString === false)
			return $prefix;
		$curString = substr($curString, 0, strlen($prefix));
		if ($curString == $prefix)
			continue;
		$prefix = getCommonPrefixBase($prefix, $curString);
	}
}

function compareArrays($resultNew, $resultOld, $path)
{
	global $ID_FIELDS;

	$errors = compareArraysInternal($resultNew, $resultOld, $path);
	if (count($errors) < 2)
		return $errors;
	
	$ids = array();
	$isOnlyIdErrors = true;
	$errorPaths = array();
	foreach ($errors as $curError)
	{
		$isCurIdError = false;
		foreach ($ID_FIELDS as $idField)
		{
			if (beginsWith($curError, "field {$idField} has different value"))
			{
				$isCurIdError = true;
				break;
			}
		}
		
		if (!$isCurIdError)
		{
			$isOnlyIdErrors = false;
			break;
		}
		$explodedError = explode('(path=', rtrim($curError, ')'));
		$explodedError = explode(' new=', $explodedError[1]);
		$errorPaths[] = $explodedError[0];
		
		$explodedError = explode(' old=', $explodedError[1]);
		$ids[] = "'".$explodedError[0]."'";
		$ids[] = "'".$explodedError[1]."'";
	}
	
	if (!$isOnlyIdErrors)
		return $errors;
	
	usort($resultNew, 'compareArraysById');
	usort($resultOld, 'compareArraysById');
	$newErrors = compareArraysInternal($resultNew, $resultOld, $path);
	if ($newErrors)				// sorting didn't help
		return $errors;
	
	$errorPath = getCommonPrefix($errorPaths);
		
	$ids = implode(',', array_unique($ids));
	return array($errorPath => ('Different order ' . $ids));
}

function normalizeResultBuffer($result)
{
	global $serviceUrlNew, $serviceUrlOld, $KS_PATTERNS;
	
	$result = preg_replace('/<executionTime>[0-9\.]+<\/executionTime>/', '', $result);
	$result = preg_replace('/<serverTime>[0-9\.]+<\/serverTime>/', '', $result);
	$result = preg_replace('/<execute_impl_time>[\-0-9\.]+<\/execute_impl_time>/', '', $result);
	$result = preg_replace('/<execute_time>[0-9\.]+<\/execute_time>/', '', $result);
	$result = preg_replace('/<total_time>[0-9\.]+<\/total_time>/', '', $result);
	$result = preg_replace('/<server_time>[0-9\.]+<\/server_time>/', '', $result);
	$result = preg_replace('/server_time="[0-9\.]+"/', '', $result);
	$result = preg_replace('/kaltura_player_\d+/', 'KP', $result);
	$result = preg_replace('/&ts=[0-9\.]+&/', '&ts=0&', $result);
	
	if (strlen($serviceUrlOld) < strlen($serviceUrlNew))		// this if is for case where one of the url is a prefix of the other
		$result = str_replace($serviceUrlNew, $serviceUrlOld, $result);
	else
		$result = str_replace($serviceUrlOld, $serviceUrlNew, $result);
	
	$patterns = $KS_PATTERNS;
	foreach ($patterns as $pattern)
	{
		preg_match_all($pattern, $result, $matches);
		foreach ($matches[1] as $match)
		{
			$result = normalizeKS($result, $match);
		}
	}
	return $result;
}

function countDifferences($buffer1, $buffer2)
{
	$bufLen = strlen($buffer1);
	$result = 0;
	for ($i = 0; $i < $bufLen; $i++)
	{
		if ($buffer1[$i] != $buffer2[$i])
			$result++;
	}
	return $result;
}


define('KWIDGET_API_START', '<xml><result>');
define('KWIDGET_API_END', '</result></xml>');
define('KWIDGET_PARAMS_START', 'widgetId=');

function parseWidget($buffer)
{
	$uncomp = gzuncompress(substr($buffer, 8));
	
	$apiResponseStart = strpos($uncomp, KWIDGET_API_START);
	$apiResponseEnd = strrpos($uncomp, KWIDGET_API_END);
	$apiResponse = null;
	if ($apiResponseStart !== false && $apiResponseEnd !== false)
	{
		$apiResponse = substr($uncomp, $apiResponseStart, $apiResponseEnd + strlen(KWIDGET_API_END) - $apiResponseStart);
		$uncomp = str_replace($apiResponse, '', $uncomp);
	}
		
	$paramsStart = strpos($uncomp, KWIDGET_PARAMS_START);
	$params = null;
	if ($paramsStart !== false)
	{ 
		$paramsLen = unpack('V', substr($uncomp, $paramsStart - 10, 4));
		$params = substr($uncomp, $paramsStart, $paramsLen[1]);
		$uncomp = str_replace($params, '', $uncomp);
		$params = normalizeResultBuffer($params);
	}
	
	return array($uncomp, $apiResponse, $params);
}

function compareResults($resultNew, $resultOld)
{
	$resultNew = normalizeResultBuffer($resultNew);
	$resultOld = normalizeResultBuffer($resultOld);
	if ($resultNew == $resultOld)
		return array();
		
	$resultNew = xmlToArray($resultNew);
	$resultOld = xmlToArray($resultOld);

	if (!$resultNew && !$resultOld)
	{
		return array('failed to parse both XMLs');
	}
	
	if (!$resultNew)
	{
		return array('failed to parse new XML');
	}
	
	if (!$resultOld)
	{
		return array('failed to parse old XML');
	}
	
	return compareArrays($resultNew, $resultOld, "");
}

function beginsWith($str, $prefix) 
{
	return (substr($str, 0, strlen($prefix)) === $prefix);
}

function endsWith($str, $postfix) 
{
	return (substr($str, -strlen($postfix)) === $postfix);
}

function getRequestHash($fullActionName, $paramsForHash)
{
	foreach ($paramsForHash as $paramName => $paramValue)
	{
		preg_match('/^\d+\:ks$/', $paramName, $matches);
		if ($matches)
		{
			unset($paramsForHash[$paramName]);
			continue;
		}
	}
	
	$paramsToUnset = array(
		"ks",
		"kalsig",
		"clientTag",
		"callback",
		"sig",
		"ts",
		"3:contextDataParams:uid",
		"contextDataParams:uid",
		"4:filter:uid",
		"filter:uid",
		"4:pager:uid",
		"pager:uid",
		);
	foreach ($paramsToUnset as $paramToUnset)
	{
		unset($paramsForHash[$paramToUnset]);
	}
	ksort($paramsForHash);
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

function testAction($ipAddress, $fullActionName, $parsedParams, $uri, $postParams = array(), $compareMode = CM_XML, $kalcliCmd = '')
{
	global $serviceUrlOld, $serviceUrlNew;
	
	print "Testing $fullActionName...";
	
	usleep(200000);         // sleep for 0.2 sec to avoid hogging the server
	
	$range = null;
	if ($compareMode == CM_BINARY)
		$range = '0-262144';		// 256K
		
	$requestHeaders = array();
	for ($retries = 0; $retries < 3; $retries++)
	{
		if ($ipAddress)
			$requestHeaders = getSignedIpHeader($ipAddress); 
		list($resultNew, $curlErrorNew, $newTime) = doCurl($serviceUrlNew . $uri, $postParams, array(), $range, $requestHeaders);
		
		if ($ipAddress)
			$requestHeaders = getSignedIpHeader($ipAddress); 
		list($resultOld, $curlErrorOld, $oldTime) = doCurl($serviceUrlOld . $uri, $postParams, array(), $range, $requestHeaders);
		
		if ($curlErrorNew || $curlErrorOld)
		{
			print "Curl error [$curlErrorNew] [$curlErrorOld]\n";
			return;
		}
		
		switch ($compareMode)
		{
		case CM_WIDGET:
			list($uncompOld, $apiResponseOld, $paramsOld) = parseWidget($resultOld);
			list($uncompNew, $apiResponseNew, $paramsNew) = parseWidget($resultNew);
			$errors = compareResults($apiResponseNew, $apiResponseOld);
			if (countDifferences($uncompNew, $uncompOld) > MAX_BINARY_DIFFS)
				$errors[] = 'Data does not match - size='.strlen($uncompNew);
			if ($paramsOld != $paramsNew)
				$errors[] = 'Params dont match - new='.$paramsNew.' old='.$paramsOld;
			break;
				
		case CM_BINARY:
			$resultOld = normalizeResultBuffer($resultOld);
			$resultNew = normalizeResultBuffer($resultNew);
			if (strlen($resultNew) != strlen($resultOld))
			{
				$errors = array('Data does not match - newSize='.strlen($resultNew).' oldSize='.strlen($resultOld));
				break;
			}
			if (countDifferences($resultNew, $resultOld) <= MAX_BINARY_DIFFS)
				$errors = array();
			else
				$errors = array('Data does not match - size='.strlen($resultNew));
			break;
					
		case CM_XML:
			$errors = compareResults($resultNew, $resultOld);
			break;
		}
		
		if (!count($errors))
		{
			print sprintf("Ok (new=%.3f old=%.3f)\n", $newTime, $oldTime);
			if ($newTime > $oldTime * 3 && $newTime > 0.5)
			{
				$sig = '';
				if (isset($parsedParams['kalsig']))
					$sig = $parsedParams['kalsig'];
				else if (isset($parsedParams['sig']))
					$sig = $parsedParams['sig'];
				else if (isset($parsedParams['ks']))
					$sig = substr($parsedParams['ks'], 0, 20);
				else if (isset($parsedParams['1:ks']))
					$sig = substr($parsedParams['1:ks'], 0, 20);
				else
					$sig = print_r($parsedParams, true);
					
				print "Warning: API became slow ({$sig})\n";
			}			
			return;
		}
		
		if (count($errors) == 1 && beginsWith(reset($errors), 'Different order '))
		{
			break;			// retry doesn't help with different order, we can save the time
		}
		
		print "\nRetrying $fullActionName...";
		usleep(1000000);
	}
	
	// check which requests failed with the multirequest
	$badRequests = null;
	if (beginsWith($fullActionName, 'multirequest'))
	{
		$badRequests = array();
		foreach ($errors as $path => $error)
		{
			if (beginsWith($path, '/result/item/'))
			{
				$explodedPath = explode('/', $path);
				$badRequests[] =  $explodedPath[3];
			}
			else
			{
				$badRequests = null;
				break;
			}
		}
	}
	
	if (is_array($badRequests))
	{
		$badRequests = array_unique($badRequests);
		sort($badRequests);
	}
	
	print "\n-------------------------------------------------------------------------------\n";
	print "\tUrl = $serviceUrlNew$uri\n";
	print "\tPostParams = ".var_export($postParams, true)."\n";
	print "\tTestUrl = $serviceUrlNew$uri&".http_build_query($postParams)."\n";
	
	if ($kalcliCmd)
	{
		if (is_array($kalcliCmd))
		{
			if (is_array($badRequests))
			{
				// leave only the bad requests
				$newCommands = array();
				foreach ($kalcliCmd as $index => $curCommand)
				{
					if (in_array($index, $badRequests))
						$newCommands[] = $curCommand;
				}
				$kalcliCmd = $newCommands;
			}
			$kalcliCmd = implode("\n\t", $kalcliCmd);
		}
		print "\tkalcli = {$kalcliCmd}\n";
	}
	
	foreach ($errors as $path => $error)
	{
		if (beginsWith($fullActionName, 'multirequest') && beginsWith($path, '/result/item/'))
		{
			$explodedPath = explode('/', $path);
			$requestIndex =  $explodedPath[3];
			$explodedActionName = explode('/', $fullActionName);
			$actionName = $explodedActionName[$requestIndex + 1];
		}
		else
		{
			$actionName = $fullActionName;
		}
		
		print "\tError: ($actionName) $error\n";
	}
	
	if ($compareMode == CM_XML && (count($errors) != 1 || !beginsWith(reset($errors), 'Different order ')))
	{
		print "Result - new\n";
		print $resultNew . "\n";
		print "Result - old\n";
		print $resultOld . "\n";
	}
}

function extendRequestKss(&$parsedParams)
{
	if (array_key_exists('ks', $parsedParams))
	{
		$ks = $parsedParams['ks'];
		$ks = extendKsExpiry($ks);
		if (is_null($ks))
			return false;
		$parsedParams['ks'] = $ks;
	}
	
	for ($i = 1; ; $i++)
	{
		$ksKey = "{$i}:ks";
		if (!array_key_exists($ksKey, $parsedParams))
			break;
		
		$ks = $parsedParams[$ksKey];
		$ks = extendKsExpiry($ks);
		if (is_null($ks))
			return false;
		$parsedParams[$ksKey] = $ks;
	}
	
	return true;
}

function isServiceApproved($service)
{
	global $APIV3_TESTED_SERVICES;
	
	if(in_array('*', $APIV3_TESTED_SERVICES))
		return true;
		
	foreach ($APIV3_TESTED_SERVICES as $approvedService)
	{
		if (strcmp(strtolower($approvedService), strtolower($service)) == 0)
		{
			return true;
		}
	}
	return false;
}

function isActionApproved($fullActionName, $action)
{
	global $APIV3_TESTED_ACTIONS, $APIV3_BLOCKED_ACTIONS;

	foreach ($APIV3_BLOCKED_ACTIONS as $blockedAction)
	{
		if (beginsWith($blockedAction, '*.'))
		{
			if (beginsWith(strtolower($action), substr($blockedAction, 2)))
				return false;
		}
		else
		{
			if (beginsWith($fullActionName, $blockedAction))
				return false;
		}
	}
	
	foreach ($APIV3_TESTED_ACTIONS as $approvedAction)
	{
		if (beginsWith($approvedAction, '*.'))
		{
			if (beginsWith(strtolower($action), substr($approvedAction, 2)))
				return true;
		}
		else
		{
			if (beginsWith($fullActionName, $approvedAction))
				return true;
		}
	}
	return false;
}

function generateKalcliCommand($service, $action, $parsedParams)
{
	$kalcliCmd = "kalcli -x {$service} {$action}";
	foreach ($parsedParams as $key => $value)
	{
		if (in_array($key, array('action', 'service')))
			continue;
		
		$curParam = "{$key}={$value}";
		if (!preg_match('/^[a-zA-Z0-9\:_\-,=\.\/]+$/', $curParam))
			$kalcliCmd .= " '{$curParam}'";
		else
			$kalcliCmd .= " {$curParam}";
	}
	return $kalcliCmd;
}

function processMultiRequest($ipAddress, $parsedParams)
{
	$commonParams = array();
	$paramsByRequest = array();
	foreach ($parsedParams as $paramName => $paramValue)
	{
		$explodedName = explode(':', $paramName);
		if (count($explodedName) <= 1 || !is_numeric($explodedName[0]))
		{
			$commonParams[$paramName] = $paramValue;
			continue;
		}
		
		$requestIndex = (int)$explodedName[0];
		$paramName = implode(':', array_slice($explodedName, 1));
		if (!array_key_exists($requestIndex, $paramsByRequest))
		{
			$paramsByRequest[$requestIndex] = array();
		}
		$paramsByRequest[$requestIndex][$paramName] = $paramValue;
	}
	unset($commonParams['action']);		// sometimes multirequests have action=null
	
	if (!$paramsByRequest)
	{
		return;
	}
	
	$fullActionName = 'multirequest';
	$maxIndex = max(array_keys($paramsByRequest));
	for ($reqIndex = 1; $reqIndex <= $maxIndex; $reqIndex++)
	{
		if (!array_key_exists($reqIndex, $paramsByRequest) ||
			!array_key_exists('service', $paramsByRequest[$reqIndex]) ||
			!array_key_exists('action', $paramsByRequest[$reqIndex]))
		{
			return;
		}
		
		$service = $paramsByRequest[$reqIndex]['service'];
		$action = $paramsByRequest[$reqIndex]['action'];
		
		$curFullActionName = strtolower("$service.$action");		
		if (!isServiceApproved($service) || !isActionApproved($curFullActionName, $action))
		{
			return;
		}
		
		$fullActionName .= '/'.$curFullActionName;
	}

	if (!extendRequestKss($parsedParams))
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
	
	$parsedParams['format'] = '2';		# XML
	ksort($parsedParams);
	
	$uri = "/api_v3/index.php?service=multirequest";

	$kalcliCmds = array();
	for ($reqIndex = 1; $reqIndex <= $maxIndex; $reqIndex++)
	{
		$curParams = $paramsByRequest[$reqIndex];
		$service = $curParams['service'];
		$action = $curParams['action'];
		$kalcliCmds[] = generateKalcliCommand($service, $action, array_merge($curParams, $commonParams));
	}
	
	testAction($ipAddress, $fullActionName, $parsedParams, $uri, $parsedParams, CM_XML, $kalcliCmds);
}

function processRequest($ipAddress, $parsedParams)
{
	if (!array_key_exists('service', $parsedParams))
	{
		//print "Error: service not specified " . print_r($parsedParams, true) . "\n";
		return;
	}

	$service = $parsedParams['service'];
	unset($parsedParams['service']);
	
	if (beginsWith(strtolower($service), "multirequest"))
	{
		if (strtolower($service) == "multirequest")
		{
			processMultiRequest($ipAddress, $parsedParams);
		}
		return;
	}
	
	if (!array_key_exists('action', $parsedParams))
	{
		//print "Error: action not specified " . print_r($parsedParams, true) . "\n";
		return;
	}
		
	$action = $parsedParams['action'];
	unset($parsedParams['action']);
	
	$fullActionName = strtolower("$service.$action");
	$parsedParams['format'] = '2';		# XML
	
	if (!isServiceApproved($service) || 
		!isActionApproved($fullActionName, $action) ||
		!extendRequestKss($parsedParams))
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
	
	ksort($parsedParams);
	
	$kalcliCmd = generateKalcliCommand($service, $action, $parsedParams);
	
	$uri = "/api_v3/index.php?service=$service&action=$action";
	$compareMode = (beginsWith($action, 'serve') ? CM_BINARY : CM_XML);
	testAction($ipAddress, $fullActionName, $parsedParams, $uri, $parsedParams, $compareMode, $kalcliCmd);
}

function processFeedRequest($ipAddress, $parsedParams)
{
	$fullActionName = "getfeed";

	if (!isServiceApproved('syndicationFeed') ||
		!isActionApproved('syndicationFeed.execute', 'execute') ||
		!extendRequestKss($parsedParams))
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
	
	$parsedParams['nocache'] = '1';
	ksort($parsedParams);
	
	$uri = "/api_v3/getFeed.php?" . http_build_query($parsedParams, null, "&");
	
	testAction($ipAddress, $fullActionName, $parsedParams, $uri);
}

interface LogProcessor
{
	function processLine($buffer);
}

class LogProcessorApiV3 implements LogProcessor
{
	protected $inParams = false;
	protected $isFeed = false;
	protected $params = '';
	protected $ipAddress = '';
	
	function processLine($buffer)
	{
		if (!$this->inParams)
		{
			$markerPos = strpos($buffer, APIV3_START_MARKER);
			if ($markerPos !== false)
			{
				$explodedBuffer = explode(' ', $buffer);
				$this->ipAddress = substr($explodedBuffer[3], 1, -1);
				$this->params = substr($buffer, $markerPos + strlen(APIV3_START_MARKER));
				$this->inParams = true;
				$this->isFeed = false;
				return false;
			}
			$markerPos = strpos($buffer, APIV3_GETFEED_MARKER);
			if ($markerPos !== false)
			{
				$explodedBuffer = explode(' ', $buffer);
				$this->ipAddress = substr($explodedBuffer[3], 1, -1);
				$this->params = substr($buffer, $markerPos + strlen(APIV3_GETFEED_MARKER));
				$this->inParams = true;
				$this->isFeed = true;
				return false;
			}
		}
		else
		{
			if ($buffer[0] == ']')
			{
				$this->inParams = false;
			
				$parsedParams = print_r_reverse($this->params);
				if (print_r($parsedParams, true) != $this->params)
				{
					print "print_r_reverse failed\n";
					return false;
				}

				if ($this->isFeed)
				{
					$shouldQuit = processFeedRequest($this->ipAddress, $parsedParams);
				}
				else
				{
					$shouldQuit = processRequest($this->ipAddress, $parsedParams);
				}
				
				if ($shouldQuit)
				{
					return true;
				}
			}
			else
			{
				$this->params .= $buffer;
			}
		}
	
		return false;
	}
}

function processPS2Request($ipAddress, $parsedParams)
{
	global $serviceUrlOld, $serviceUrlNew, $PS2_TESTED_XML_ACTIONS, $PS2_TESTED_BIN_ACTIONS;

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
	
	if (!in_array($fullActionName, $PS2_TESTED_XML_ACTIONS) && 
		!in_array($fullActionName, $PS2_TESTED_BIN_ACTIONS))
	{
		return;
	}
	
	if (!extendRequestKss($parsedParams))
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
	
	ksort($parsedParams);
	$uri = "/index.php/$module/$action?" . http_build_query($parsedParams, null, "&");

	if (in_array($fullActionName, $PS2_TESTED_XML_ACTIONS))
		$compareMode = CM_XML;
	else if ($fullActionName == 'extwidget.kwidget')
		$compareMode = CM_WIDGET;
	else
		$compareMode = CM_BINARY;
	testAction($ipAddress, $fullActionName, $parsedParams, $uri, array(), $compareMode);
}

class LogProcessorPS2 implements LogProcessor
{	
	function processLine($buffer)
	{
		$markerPos = strpos($buffer, PS2_START_MARKER);
		if ($markerPos === false)
			return false;
		$params = trim(substr($buffer, $markerPos + strlen(PS2_START_MARKER)));
		if (!beginsWith($params, 'array (') || !endsWith($params, ')'))
			return false;

		$explodedBuffer = explode(' ', $buffer);
		$ipAddress = substr($explodedBuffer[3], 1, -1);
		$parsedParams = eval('return ' . $params . ';');

		if (processPS2Request($ipAddress, $parsedParams))
		{
			return true;
		}

		return false;
	}
}

class LogProcessorFeedList implements LogProcessor
{
	function processLine($buffer)
	{
		$feedId = trim($buffer);
		$parsedParams = array('feedId' => $feedId);
		processFeedRequest($parsedParams);
	}
}

class LogProcessorUriList implements LogProcessor
{
	function processLine($buffer)
	{
		$uri = trim($buffer);
		
		// TODO: call extendRequestKss
		
		$service = '';
		if (preg_match('/service=([\w_]+)/', $uri, $matches))
			$service = $matches[1];

		$action = '';
		if (preg_match('/action=([\w_]+)/', $uri, $matches))
			$action = $matches[1];

		$fullActionName = "$service.$action";
		testAction(null, $fullActionName, array(), $uri);
	}
}

function processRegularFile($apiLogPath, LogProcessor $logProcessor)
{
	$handle = @fopen($apiLogPath, "r");
	if (!$handle)
		die('Error: failed to open log file');

	$logStats = fstat($handle);
	$origSize = $logStats['size'];

	while (ftell($handle) < $origSize && ($buffer = fgets($handle)) !== false) 
	{
		if ($logProcessor->processLine($buffer))
			break;
	}

	fclose($handle);
}

function processGZipFile($apiLogPath, LogProcessor $logProcessor)
{
	$handle = @gzopen($apiLogPath, "r");
	if (!$handle)
		die('Error: failed to open log file');

	while (!gzeof($handle)) 
	{
		$buffer = gzgets($handle, 16384);
		if ($logProcessor->processLine($buffer))
			break;
	}

	gzclose($handle);
}

// parse the command line
if ($argc < 5)
	die("Usage:\n\tphp compatCheck <old service url> <new service url> <api log> <api_v3/ps2/feedIds/uris> [<start position> [<end position> [<max tests per action>]]]\n");

$serviceUrlOld = $argv[1];
$serviceUrlNew = $argv[2];
$apiLogPath = $argv[3];
$logFormat = strtolower($argv[4]);

$partnerSecretPool = new PartnerSecretPool();

if (strpos($apiLogPath, ':') !== false)
{
	$localLogPath = tempnam("/tmp", "CompatCheck");
	print("Copying log file to $localLogPath...\n");
	passthru("rsync -zavx --progress $apiLogPath $localLogPath");
	$apiLogPath = $localLogPath;
}

if (!in_array($logFormat, array('api_v3', 'ps2', 'feedids', 'uris')))
	die("Log format should be one of: api_v3, ps2, feedids, uris");

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

switch ($logFormat)
{
case 'api_v3':
	$logProcessor = new LogProcessorApiV3();
	break;
case 'ps2':
	$logProcessor = new LogProcessorPS2();
	break;
case 'feedids':
	$logProcessor = new LogProcessorFeedList();
	break;
case 'uris':
	$logProcessor = new LogProcessorUriList();
	break;
}

$logFileInfo = pathinfo($apiLogPath);

if (array_key_exists('extension', $logFileInfo) && $logFileInfo['extension'] == 'gz')
	processGZipFile($apiLogPath, $logProcessor);
else
	processRegularFile($apiLogPath, $logProcessor);

$partnerSecretPool = null;
