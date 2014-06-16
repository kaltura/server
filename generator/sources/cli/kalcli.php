#!/usr/bin/php
<?php
// ===================================================================================================
//                           _  __     _ _
//                          | |/ /__ _| | |_ _  _ _ _ __ _
//                          | ' </ _` | |  _| || | '_/ _` |
//                          |_|\_\__,_|_|\__|\_,_|_| \__,_|
//
// This file is part of the Kaltura Collaborative Media Suite which allows users
// to do with audio, video, and animation what Wiki platfroms allow them to do with
// text.
//
// Copyright (C) 2006-2011  Kaltura Inc.
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Affero General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Affero General Public License for more details.
//
// You should have received a copy of the GNU Affero General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// @ignore
// ===================================================================================================

require_once(dirname(__file__) . '/lib/KalturaSession.php');
require_once(dirname(__file__) . '/lib/KalturaCommandLineParser.php');
require_once(dirname(__file__) . '/lib/KalturaCurlWrapper.php');
require_once(dirname(__file__) . '/kalcliSwitches.php');

$DATE_FIELD_SUFFIXES = array('At', 'Date', 'On');
define('MIN_TIME_STAMP', 946677600);		// 2000
define('MAX_TIME_STAMP', 2147483647);		// 2038
define('API_LOG_FILENAME', '/var/log/kaltura_api_v3.log');

function formatResponse($resp, $indent = '', $varName = null)
{
	global $DATE_FIELD_SUFFIXES;
	
	switch (gettype($resp))
	{
	case 'integer':
		if ($resp > MIN_TIME_STAMP && $resp < MAX_TIME_STAMP)
		{
			foreach ($DATE_FIELD_SUFFIXES as $dateSuffix)
			{
				if (substr($varName, -strlen($dateSuffix)) === $dateSuffix) 
					return "{$resp}\t(" . date('Y-m-d H:i:s', $resp) . ")";						
			}
		}
		
	case 'boolean':
	case 'double':
	case 'string':
	case 'NULL':
		return (string)$resp;
		
	case 'array':
		$result = "array";
		foreach ($resp as $index => $elem)
		{
			$value = formatResponse($elem, $indent . "\t", $index);
			$result .= "\n{$indent}\t{$index}\t{$value}";
		}
		return $result;
		
	case 'object':
		$properties = get_object_vars($resp);
		$result = $properties['__PHP_Incomplete_Class_Name'];
		unset($properties['__PHP_Incomplete_Class_Name']);
		foreach ($properties as $name => $value)
		{
			if ($name == '__PHP_Incomplete_Class_Name')
				continue;
			$value = formatResponse($value, $indent . "\t", $name);
			$result .= "\n{$indent}\t{$name}\t{$value}";
		}
		return $result;
	}
}

function isLineLogStart($curLine)
{
	if (strlen($curLine) < 20)
		return false;
	if ($curLine[4] == '-' && $curLine[7] == '-' && $curLine[10] == ' ' && $curLine[13] == ':' && $curLine[16] == ':')
		return true;
	return false;
}

function printLogFiltered($logPortion, $sessionId)
{
	$curSession = null;
	$logLines = explode("\n", $logPortion);
	foreach ($logLines as $logLine)
	{
		if (isLineLogStart($logLine))
		{
			$explodedLine = explode(' ', $logLine, 6);
			$curSession = substr($explodedLine[4], 1, -1);
		}
		
		if ($curSession == $sessionId)
			echo $logLine . "\n";
	}
}

KalturaSecretRepository::init();

// parse command line
$options = KalturaCommandLineParser::parseArguments($commandLineSwitches);
$arguments = KalturaCommandLineParser::stripCommandLineSwitches($commandLineSwitches, $argv);

if (count($arguments) < 2)
{
	$usage = "Usage: kalcli [switches] <service> <action> [<param1> <param2> ...]\nOptions:\n";
	$usage .= KalturaCommandLineParser::getArgumentsUsage($commandLineSwitches);
	die($usage);
}

$service = trim($arguments[0]);
$action = trim($arguments[1]);

$params = array('clientTag' => 'kalcli:@DATE@');
$extraArgCount = count($arguments);
for ($curIndex = 2; $curIndex < $extraArgCount; $curIndex++)
{
	$explodedArg = explode('=', trim($arguments[$curIndex]), 2);
	if (count($explodedArg) == 2)
		$params[$explodedArg[0]] = $explodedArg[1];
	else
		$params[$explodedArg[0]] = '';
}

// read parameters from stdin
if (!isset($options['no-stdin']))
{
	$f = fopen('php://stdin', 'r');
	while ($line = fgets($f))
	{
		$line = preg_replace('/\s+/', ' ', trim($line));
		$explodedLine = explode(" ", $line, 2);
		if (count($explodedLine) == 2)
			$params[$explodedLine[0]] = $explodedLine[1];
		else
			$params[$explodedLine[0]] = '';
	}
	fclose($f);
}

if (!isset($options['raw']) && !isset($options['curl']))
	$params['format'] = '3';      # PHP
	
// renew all ks'es
if (!isset($options['no-renew']))
{
	$renewedSessions = array();
	foreach ($params as $key => &$value)
	{
		if ($key != 'ks' && !preg_match('/[\d]+:ks/', $key))
			continue;

		if (isset($renewedSessions[$value]))
		{
			$value = $renewedSessions[$value];
			continue;
		}
		
		$renewedKs = KalturaSession::extendKs($value);
		if (!$renewedKs)
			continue;
		
		$renewedSessions[$value] = $renewedKs; 
		$value = $renewedKs;
	}
}

// get the service url
if (isset($options['url']) && is_string($options['url']))
{
	$serviceUrl = $options['url'];
}
else
{
	$serviceUrl = 'www.kaltura.com';
}

if (strpos($serviceUrl, '://') === false)
{
	if (isset($options['https']))
		$serviceUrl = 'https://' . $serviceUrl;
	else
		$serviceUrl = 'http://' . $serviceUrl;
}

$url = $serviceUrl . "/api_v3/index.php?service={$service}&action={$action}";

if (isset($options['curl']))
{
	$commandLine = "curl";
	
	if (isset($options['header']))
	{
		$headers = $options['header'];
		if (!is_array($headers))
			$headers = array($headers);
		foreach ($headers as $curHeader)
			$commandLine .= ' "-H'.$curHeader.'"';
	}
	
	if (isset($options['insecure']))
		$commandLine .= ' -k';	
	if (isset($options['include']))
		$commandLine .= ' -i';
	if (isset($options['head']))
		$commandLine .= ' -I';
		
	if (!isset($options['get']))
	{
		$commandLine .= ' "-d'.http_build_query($params).'"';
	}
	else if ($params)
	{
		$url .= '&' . http_build_query($params);
	}
	$commandLine .= ' "'.$url.'"';
	
	die($commandLine . "\n");
}


// initialize the curl wrapper
$curlWrapper = new KalturaCurlWrapper();
if (isset($options['header']))
{
	if (is_array($options['header']))
		$curlWrapper->requestHeaders = $options['header'];
	else if (is_string($options['header']))
		$curlWrapper->requestHeaders = array($options['header']);
}

$curlWrapper->useGet = isset($options['get']);
$curlWrapper->ignoreCertErrors = isset($options['insecure']);
$curlWrapper->followRedirects = isset($options['location']);

if (isset($options['range']))
{
	$curlWrapper->range = $options['range'];
}

if (isset($options['log']))
{
	$initialLogSize = filesize(API_LOG_FILENAME);
}

// issue the request
$result = $curlWrapper->getUrl($url, $params);

if (isset($options['log']))
{
	clearstatcache();
	$currentLogSize = filesize(API_LOG_FILENAME);
	
	$logPortion = file_get_contents(API_LOG_FILENAME, false, null, $initialLogSize, $currentLogSize - $initialLogSize);
	
	if (preg_match('/X-Kaltura-Session: (\d+)/', $curlWrapper->responseHeaders, $matches))
	{
		$sessionId = $matches[1];
		printLogFiltered($logPortion, $sessionId);
	}
	die;
}

// output the response
if (isset($options['include']) || isset($options['head']))
	echo $curlWrapper->responseHeaders;

if (!isset($options['head']))
{
	if (!isset($options['raw']))
	{
		$unserializedResult = @unserialize($result);
		if ($unserializedResult !== false)
		{
			$result = formatResponse($unserializedResult) . "\n";
		}
	}
	echo $result;
}

if (isset($options['time']) && !is_null($curlWrapper->totalTime))
	echo "execution time\t{$curlWrapper->totalTime}\n";
