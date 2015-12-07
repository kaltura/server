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

class KalturaCurlWrapper
{
	public $totalTime = null;
	public $responseHeaders = '';
	public $requestHeaders = array();
	public $useGet = false;
	public $ignoreCertErrors = false;
	public $followRedirects = false;
	public $range = '';
	
	public function readHeader($ch, $string)
	{
		$this->responseHeaders .= $string;
		return strlen($string);	
	}

	function getUrl($url, $params) 
	{
		$ch = curl_init();
		if (!$this->useGet && !$this->range)
		{
			curl_setopt($ch, CURLOPT_POST, 1);
			$hasFiles = false;
			foreach($params as $key => &$value)
			{
				if (strlen($value) > 1 && $value[0] == '@')
				{
					if (substr($value, 0, 2) == '@@' && file_exists(substr($value, 2)))
						$value = file_get_contents(substr($value, 2));
					else if (file_exists(substr($value, 1)))
						$hasFiles = true;
				}
			}
			curl_setopt($ch, CURLOPT_POSTFIELDS, $hasFiles ? $params : http_build_query($params));
		}
		else if ($params)
		{	
			$url .= '?' . http_build_query($params);
		}
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		if (!$this->range)
		{
			curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
		}
		if ($this->ignoreCertErrors)
		{
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		}
		if ($this->followRedirects)
		{
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		}
		if ($this->range)
		{
			curl_setopt($ch, CURLOPT_RANGE, $this->range);
		}
		
		curl_setopt($ch, CURLOPT_HEADERFUNCTION, array($this, 'readHeader'));
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->requestHeaders);
		$data = curl_exec($ch);
		if(curl_errno($ch))
		{
			echo 'curl error: ' . curl_error($ch);
			$data = false;
		}
		else
		{
			$info = curl_getinfo($ch);
			$this->totalTime = $info['total_time'] - $info['pretransfer_time'];
		}
	 
		curl_close($ch);
		return $data;
	}
}

