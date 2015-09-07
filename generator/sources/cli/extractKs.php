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

function formatTimeInterval($secs)
{
	$bit = array(
		' year'   => intval($secs / 31556926),
		' month'  => $secs / 2628000 % 12,
		' day'    => $secs / 86400 % 30,
		' hour'   => $secs / 3600 % 24,
		' minute' => $secs / 60 % 60,
		' second' => $secs % 60
	);

	foreach($bit as $k => $v)
	{
		if($v > 1)
		{
			$ret[] = $v . $k . 's';
		}
		else if($v == 1)
		{
			$ret[] = $v . $k;
		}
	}
	
	$ret = array_slice($ret, 0, 2);		// don't care about more than 2 levels
	array_splice($ret, count($ret) - 1, 0, 'and');

	return join(' ', $ret);
}

KalturaSecretRepository::init();

if ($argc < 2)
	die("Usage: extractKs <ks>\n");

$ks = $argv[1];
$ksObj = KalturaSession::getKsObject($ks);
if (!$ksObj)
	die("Failed to parse ks {$ks}\n");

echo str_pad('Sig', 20) . $ksObj->hash . "\n";
echo str_pad('Fields', 20) . $ksObj->real_str . "\n";
echo "---\n";
$fieldNames = array('partner_id','partner_pattern','valid_until','type','rand','user','privileges','master_partner_id','additional_data');
foreach ($fieldNames as $fieldName)
{
	echo str_pad($fieldName, 20) . $ksObj->$fieldName;
	if ($fieldName == 'valid_until')
	{
		$currentTime = time();
		echo ' = ' . date('Y-m-d H:i:s', $ksObj->valid_until);
		if ($currentTime >= $ksObj->valid_until)
		{
			echo ' (expired ' . formatTimeInterval($currentTime - $ksObj->valid_until) . ' ago';
		}
		else
		{
			echo ' (will expire in ' . formatTimeInterval($ksObj->valid_until - $currentTime);
		}
		echo ')';
	}
	echo "\n";
}
