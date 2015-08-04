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

$commandLineSwitches = array(
	array(KalturaCommandLineParser::SWITCH_REQUIRES_VALUE, 'v', 'version', 'Session version (1/2)'),
	array(KalturaCommandLineParser::SWITCH_REQUIRES_VALUE, 't', 'type', 'Session type - 0=USER, 2=ADMIN'),
	array(KalturaCommandLineParser::SWITCH_REQUIRES_VALUE, 'u', 'user', 'User name'),
	array(KalturaCommandLineParser::SWITCH_REQUIRES_VALUE, 'e', 'expiry', 'Session expiry (seconds)'),
	array(KalturaCommandLineParser::SWITCH_REQUIRES_VALUE, 'p', 'privileges', 'Session privileges'),
	array(KalturaCommandLineParser::SWITCH_NO_VALUE, 'w', 'widget', 'Widget session'),
	array(KalturaCommandLineParser::SWITCH_NO_VALUE, 'b', 'bare', 'Print only the KS itself'),
);

// parse command line
$options = KalturaCommandLineParser::parseArguments($commandLineSwitches);
$arguments = KalturaCommandLineParser::stripCommandLineSwitches($commandLineSwitches, $argv);

if (!$arguments)
{
	$usage = "Usage: generateKs [switches] <partnerId>\nOptions:\n";
	$usage .= KalturaCommandLineParser::getArgumentsUsage($commandLineSwitches);
	die($usage);
}

$partnerId = $arguments[0];

KalturaSecretRepository::init();

$adminSecret = KalturaSecretRepository::getAdminSecret($partnerId);
if (!$adminSecret)
    die("Failed to get secret for partner {$partnerId}\n");

$type = (isset($options['type']) ? $options['type'] : 2);
$user = (isset($options['user']) ? $options['user'] : 'admin');
$expiry = (isset($options['expiry']) ? $options['expiry'] : 86400);
$privileges = (isset($options['privileges']) ? $options['privileges'] : 'disableentitlement');

if (isset($options['widget']))
{
	$type = 0;
	$user = '0';
	$expiry = 86400;
	$privileges = 'widget:1,view:*';
}

if (!isset($options['bare']))
	echo "ks\t";

$version = isset($options['version']) ? $options['version'] : 1;
switch ($version)
{ 
case 1:
	$ks = KalturaSession::generateKsV1($adminSecret, $user, $type, $partnerId, $expiry, $privileges, null, null);
	break;

case 2:
	$ks = KalturaSession::generateKsV2($adminSecret, $user, $type, $partnerId, $expiry, $privileges, null, null);
	break;

default:
	die("Invalid version {$version}\n");
}
	
echo $ks;

if (!isset($options['bare']))
	echo "\n";
