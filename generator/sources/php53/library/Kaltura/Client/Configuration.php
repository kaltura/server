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
 * @namespace
 */
namespace Kaltura\Client;

/**
 * @package Kaltura
 * @subpackage Client
 */
class Configuration
{
	/**
	 * @var string
	 */
	private $serviceUrl    				= "http://www.kaltura.com/";
	
	/**
	 * @var int
	 */
	private $format        				= Client::KALTURA_SERVICE_FORMAT_XML;
	
	/**
	 * @var int
	 */
	private $curlTimeout   				= 120;
	
	/**
	 * @var string
	 */
	private $userAgent					= '';
	
	/**
	 * @var bool
	 */
	private $startZendDebuggerSession 	= false;
	
	/**
	 * @var string
	 */
	private $proxyHost                   = null;
	
	/**
	 * @var int
	 */
	private $proxyPort                   = null;
	
	/**
	 * @var string
	 */
	private $proxyType                   = 'HTTP';
	
	/**
	 * @var string
	 */
	private $proxyUser                   = null;
	
	/**
	 * @var string
	 */
	private $proxyPassword               = '';
	
	/**
	 * @var bool
	 */
	private $verifySSL 					= true;
	
	/**
	 * @var array
	 */
	private $requestHeaders				= array();
	
	/**
	 * @var \Kaltura\Client\ILogger
	 */
	private $logger;
	
	/**
	 * @return the $serviceUrl
	 */
	public function getServiceUrl ()
	{
		return $this->serviceUrl;
	}

	/**
	 * @return the $format
	 */
	public function getFormat ()
	{
		return $this->format;
	}

	/**
	 * @return the $curlTimeout
	 */
	public function getCurlTimeout ()
	{
		return $this->curlTimeout;
	}

	/**
	 * @return the $userAgent
	 */
	public function getUserAgent ()
	{
		return $this->userAgent;
	}

	/**
	 * @return the $startZendDebuggerSession
	 */
	public function getStartZendDebuggerSession ()
	{
		return $this->startZendDebuggerSession;
	}

	/**
	 * @return the $proxyHost
	 */
	public function getProxyHost ()
	{
		return $this->proxyHost;
	}

	/**
	 * @return the $proxyPort
	 */
	public function getProxyPort ()
	{
		return $this->proxyPort;
	}

	/**
	 * @return the $proxyType
	 */
	public function getProxyType ()
	{
		return $this->proxyType;
	}

	/**
	 * @return the $proxyUser
	 */
	public function getProxyUser ()
	{
		return $this->proxyUser;
	}

	/**
	 * @return the $proxyPassword
	 */
	public function getProxyPassword ()
	{
		return $this->proxyPassword;
	}

	/**
	 * @return the $verifySSL
	 */
	public function getVerifySSL ()
	{
		return $this->verifySSL;
	}

	/**
	 * @return the $requestHeaders
	 */
	public function getRequestHeaders ()
	{
		return $this->requestHeaders;
	}

	/**
	 * @param string $serviceUrl
	 */
	public function setServiceUrl ($serviceUrl)
	{
		$this->serviceUrl = $serviceUrl;
	}

	/**
	 * @param int $format
	 */
	public function setFormat ($format)
	{
		$this->format = $format;
	}

	/**
	 * @param int $curlTimeout
	 */
	public function setCurlTimeout ($curlTimeout)
	{
		$this->curlTimeout = $curlTimeout;
	}

	/**
	 * @param string $userAgent
	 */
	public function setUserAgent ($userAgent)
	{
		$this->userAgent = $userAgent;
	}

	/**
	 * @param bool $startZendDebuggerSession
	 */
	public function setStartZendDebuggerSession ($startZendDebuggerSession)
	{
		$this->startZendDebuggerSession = $startZendDebuggerSession;
	}

	/**
	 * @param string $proxyHost
	 */
	public function setProxyHost ($proxyHost)
	{
		$this->proxyHost = $proxyHost;
	}

	/**
	 * @param int $proxyPort
	 */
	public function setProxyPort ($proxyPort)
	{
		$this->proxyPort = $proxyPort;
	}

	/**
	 * @param string $proxyType
	 */
	public function setProxyType ($proxyType)
	{
		$this->proxyType = $proxyType;
	}

	/**
	 * @param string $proxyUser
	 */
	public function setProxyUser ($proxyUser)
	{
		$this->proxyUser = $proxyUser;
	}

	/**
	 * @param string $proxyPassword
	 */
	public function setProxyPassword ($proxyPassword)
	{
		$this->proxyPassword = $proxyPassword;
	}

	/**
	 * @param bool $verifySSL
	 */
	public function setVerifySSL ($verifySSL)
	{
		$this->verifySSL = $verifySSL;
	}

	/**
	 * @param array $requestHeaders
	 */
	public function setRequestHeaders ($requestHeaders)
	{
		$this->requestHeaders = $requestHeaders;
	}

	/**
	 * Set logger to get kaltura client debug logs
	 *
	 * @param \Kaltura\Client\ILogger $log
	 */
	public function setLogger(ILogger $log)
	{
		$this->logger = $log;
	}
	
	/**
	 * Gets the logger (Internal client use)
	 *
	 * @return \Kaltura\Client\ILogger
	 */
	public function getLogger()
	{
		return $this->logger;
	}
}
