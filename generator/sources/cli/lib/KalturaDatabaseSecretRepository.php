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

require_once(dirname(__file__) . '/KalturaSecretRepositoryBase.php');

class KalturaDatabaseSecretRepository implements KalturaSecretRepositoryBase
{
	public function __construct($config)
	{
		$this->link = mysqli_connect($config['host'], $config['user'], $config['password'], $config['database'])
			or die('Error: Could not connect: ' . mysqli_connect_error() . "\n");
	}
	
	public function __destruct()
	{
		mysqli_close($this->link);
	}
		
	protected function executeQuery($query)
	{
		$resultSet = mysqli_query($this->link, $query) or die('Error: Select query failed: ' . mysqli_error($this->link) . "\n");
		$result = mysqli_fetch_array($resultSet, MYSQL_NUM);
		mysqli_free_result($resultSet);
		return $result;
	}

	public function getAdminSecret($partnerId)
	{
		$results = $this->executeQuery("SELECT admin_secret FROM partner WHERE partner.ID = '".str_replace("'", "''", $partnerId)."'");
		if (!$results)
			return null;
		return $results[0];
	}
}