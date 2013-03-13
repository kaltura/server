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

require_once(dirname(__file__) . '/lib/KalturaCommandLineParser.php');
require_once(dirname(__file__) . '/lib/KalturaCurlWrapper.php');
require_once(dirname(__file__) . '/kalcliSwitches.php');

function formatResponse($resp, $indent = '')
{
	switch (gettype($resp))
	{
	case 'boolean':
	case 'integer':
	case 'double':
	case 'string':
	case 'NULL':
		return (string)$resp;
		
	case 'array':
		$result = "array";
		foreach ($resp as $index => $elem)
		{
			$value = formatResponse($elem, $indent . "\t");
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
			$value = formatResponse($value, $indent . "\t");
			$result .= "\n{$indent}\t{$name}\t{$value}";
		}
		return $result;
	}
}

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

$params = array();
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

if (!isset($options['raw']))
	$params['format'] = '3';      # PHP

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

// issue the request
$result = $curlWrapper->getUrl($url, $params);

// output the response
if (isset($options['include']) || isset($options['head']))
	echo $curlWrapper->responseHeaders;

if (!isset($options['head']))
{
	if (!isset($options['raw']))
		$result = formatResponse(unserialize($result)) . "\n";
	echo $result;
}

if (isset($options['time']) && !is_null($curlWrapper->totalTime))
	echo "execution time\t{$curlWrapper->totalTime}\n";
