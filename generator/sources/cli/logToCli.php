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
        preg_match_all('/^\s{4}\[(.+?)\] \=\> ?/m', $in, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
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

function parseMultirequest($parsedParams)
{
	$paramsByRequest = array();
	foreach ($parsedParams as $paramName => $paramValue)
	{
		$explodedName = explode(':', $paramName);
		if (count($explodedName) <= 1 || !is_numeric($explodedName[0]))
		{
			$requestIndex = 'common';
		}
		else
		{		
			$requestIndex = (int)$explodedName[0];
			$paramName = implode(':', array_slice($explodedName, 1));
		}
		
		if (!array_key_exists($requestIndex, $paramsByRequest))
		{
			$paramsByRequest[$requestIndex] = array();
		}
		$paramsByRequest[$requestIndex][$paramName] = $paramValue;
	}
	
	if (isset($paramsByRequest['common']))
	{
		foreach ($paramsByRequest as $requestIndex => &$curParams)
		{
			if ($requestIndex == 'common')
				continue;
			$curParams = array_merge($curParams, $paramsByRequest['common']);
		}
		unset($paramsByRequest['common']);
	}
	ksort($paramsByRequest);
	return $paramsByRequest;
}

function genKalcliCommand($parsedParams)
{
	if (!isset($parsedParams['service']))
		return 'Error: service not defined';
	$service = $parsedParams['service'];
	unset($parsedParams['service']);

	if (!isset($parsedParams['action']))
		return 'Error: action not defined';
	$action = $parsedParams['action'];
	unset($parsedParams['action']);
	
	$res = "kalcli -x {$service} {$action}";

	ksort($parsedParams);
	foreach ($parsedParams as $param => $value)
	{
		$curParam = "{$param}={$value}";
		if (!preg_match('/^[a-zA-Z0-9\:_\-,=\.]+$/', $curParam))
			if (strpos($curParam, "'") === false)
				$res .= " '{$curParam}'";
			else
				$res .= " \"{$curParam}\"";
		else
			$res .= " {$curParam}";
	}
	return $res;
}

function generateOutput($parsedParams, $multireqMode)
{
	if (isset($parsedParams['service']) && $parsedParams['service'] == 'multirequest')
	{
		if ($multireqMode == 'multi')
		{
			unset($parsedParams['service']);
			unset($parsedParams['action']);
			$requestByParams = parseMultirequest($parsedParams);
			foreach ($requestByParams as $curParams)
			{
				$curCmd = genKalcliCommand($curParams);
				echo $curCmd . "\n";
			}
			return;
		}
		$parsedParams['action'] = 'null';
	}
	
	$curCmd = genKalcliCommand($parsedParams);
	echo $curCmd . "\n";
}

// parse the command line
$commandLineSwitches = array(
		array(KalturaCommandLineParser::SWITCH_NO_VALUE, 's', 'single', 'Generate a single command for multirequest'),
		array(KalturaCommandLineParser::SWITCH_NO_VALUE, 'h', 'help', 'Prints usage information'),
);

$options = KalturaCommandLineParser::parseArguments($commandLineSwitches);
if (isset($options['help']))
{
	$usage = "Usage: logToCli [switches]\nOptions:\n";
	$usage .= KalturaCommandLineParser::getArgumentsUsage($commandLineSwitches);
	echo $usage; 
	exit(1);
}

$multireqMode = 'multi';
if (isset($options['single']))
	$multireqMode = 'single';

// read parameters from stdin
$f = fopen('php://stdin', 'r');
$logSection = '';
for (;;)
{
	$line = fgets($f);
	$trimmedLine = trim($line);
	if (!$trimmedLine || $trimmedLine == ']')
		break;
	$logSection .= $line;
}
fclose($f);

// parse the log section
$logSection = str_replace("\r", '', $logSection);
$arrayPos = strpos($logSection, 'Array');
$curlPos = strpos($logSection, 'curl: ');
if ($arrayPos !== false)
{
	$logSection = substr($logSection, $arrayPos);
	$parsedParams = print_r_reverse($logSection);
	if (!is_array($parsedParams))
	{
		echo 'Error: failed to parse action parameters';
		exit(1);
	}
}
else if ($curlPos !== false)
{
	$logSection = substr($logSection, $curlPos);
	$parsedUrl = parse_url(trim($logSection));
	$parsedParams = null;
	parse_str($parsedUrl['query'], $parsedParams);
	
}
else 
{
	echo 'Error: failed to parse log section (missing "Array")';
	exit(1);
}

// output the result
generateOutput($parsedParams, $multireqMode);
