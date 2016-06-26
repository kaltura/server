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
/**
 * @package Kaltura
 * @subpackage Client
 */
class Kaltura_Client_Configuration
{
	private $logger;

	public $serviceUrl    				= "http://www.kaltura.com/";
	public $format        				= Kaltura_Client_ClientBase::KALTURA_SERVICE_FORMAT_XML;
	public $curlTimeout   				= 120;
	public $startZendDebuggerSession 	= false;
	public $userAgent					= '';
	public $proxyHost                   = null;
	public $proxyPort                   = null;
	public $proxyType                   = 'HTTP';
	public $proxyUser                   = null;
	public $proxyPassword               = '';
	public $verifySSL 					= true;
	public $sslCertificatePath			= null;
	public $requestHeaders				= array();
	
	/**
	 * Set logger to get kaltura client debug logs
	 *
	 * @param Kaltura_Client_ILogger $log
	 */
	public function setLogger(Kaltura_Client_ILogger $log)
	{
		$this->logger = $log;
	}

	/**
	 * Gets the logger (Internal client use)
	 *
	 * @return Kaltura_Client_ILogger
	 */
	public function getLogger()
	{
		return $this->logger;
	}
}
