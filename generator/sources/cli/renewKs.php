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
require_once(dirname(__file__) . '/lib/KalturaSession.php');

function renewKs($input, $expiry)
{
	$ks = $input; 
	$patterns = array(
		'/\/ks\/([a-zA-Z0-9+_\-]+=*)/', 
		'/&ks=([a-zA-Z0-9+\/_\-]+=*)/', 
		'/\?ks=([a-zA-Z0-9+\/_\-]+=*)/');
	foreach ($patterns as $pattern)
	{
		preg_match_all($pattern, $input, $matches);
		if ($matches[1])
		{
			$ks = reset($matches[1]);
			break;
		}
	}
	
	return str_replace($ks, KalturaSession::extendKs($ks, $expiry), $input);
}

$commandLineSwitches = array(
	array(KalturaCommandLineParser::SWITCH_NO_VALUE, 'i', 'stdin', 'Read input from stdin'),
	array(KalturaCommandLineParser::SWITCH_NO_VALUE, 'b', 'bare', 'Print only the KS itself'),
	array(KalturaCommandLineParser::SWITCH_REQUIRES_VALUE, 'e', 'expiry', 'Session expiry (seconds)'),
);

// parse command line
$options = KalturaCommandLineParser::parseArguments($commandLineSwitches);
$arguments = KalturaCommandLineParser::stripCommandLineSwitches($commandLineSwitches, $argv);

if (!$arguments && !isset($options['stdin']))
{
	$usage = "Usage: renewKs [switches] <ks>\nOptions:\n";
	$usage .= KalturaCommandLineParser::getArgumentsUsage($commandLineSwitches);
	die($usage);
}

KalturaSecretRepository::init();

$expiry = (isset($options['expiry']) ? $options['expiry'] : 86400);

if (!isset($options['stdin']))
{
	if (!isset($options['bare']))
		echo "ks\t";
	
	echo renewKs($arguments[0], $expiry);
	
	if (!isset($options['bare']))
		echo "\n";
	die;
}

$f = fopen('php://stdin', 'r');
for (;;)
{
	$line = fgets($f);
	if (!$line)
	{
		break;
	}
	echo renewKs(trim($line), $expiry);
	echo "\n";
}
fclose($f);
