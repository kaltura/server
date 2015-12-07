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

if ($argc < 2)
	die("Usage: genIpHeader <ip address>\n");

$ipAddress = $argv[1];

$config = parse_ini_file(dirname(__file__) . '/config/config.ini');
$salt = $config['ipSalt'];
$curTime = time();
$uniqId = rand(1,32767);
$baseHeader = array($ipAddress, $curTime, $uniqId);
$headerHash = md5(implode(',', $baseHeader) . ',' . $salt);
$ipHeader = implode(',', $baseHeader) . ',' . $headerHash;
print "X-KALTURA-REMOTE-ADDR:$ipHeader";
