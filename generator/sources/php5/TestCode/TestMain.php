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
require_once(dirname(__FILE__) . '/../KalturaClient.php');

class TestMain implements IKalturaLogger
{
	const CONFIG_FILE = 'config.ini';
	const CONFIG_TEMPLATE_FILE = 'config.template.ini';
	
	const CONFIG_ITEM_PARTNER_ID = 'partnerId';
	const CONFIG_ITEM_ADMIN_SECRET = 'adminSecret';
	const CONFIG_ITEM_SERVICE_URL = 'serviceUrl';
	const CONFIG_ITEM_UPLOAD_FILE = 'uploadFile';
	const CONFIG_ITEM_TIMEZONE = 'timezone';
	
	/**
	 * @var array
	 */
	private $config;
	
	public function log($message)
	{
		echo date('Y-m-d H:i:s') . ' ' .  $message . "\n";
	}

	public static function run()
	{   
		$test = new TestMain();
		$test->loadConfig();
		$test->listActions();
		$test->multiRequest();
		$test->add();
		echo "\nFinished running client library tests\n";
	}
	
	private function loadConfig()
	{
		$filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . self::CONFIG_FILE;
		if(!file_exists($filename)){
			$template = dirname(__FILE__) . DIRECTORY_SEPARATOR . self::CONFIG_TEMPLATE_FILE;
			throw new Exception("Configuration file [$filename] not found, Use template file [$template].");
		}
		
		$this->config = parse_ini_file($filename);
		
		date_default_timezone_set($this->config[self::CONFIG_ITEM_TIMEZONE]);
	}
	
	private function getKalturaClient($partnerId, $adminSecret, $isAdmin)
	{
		$kConfig = new KalturaConfiguration();
		$kConfig->serviceUrl = $this->config[self::CONFIG_ITEM_SERVICE_URL];
		$kConfig->setLogger($this);
		$client = new KalturaClient($kConfig);
		
		$userId = "SomeUser";
		$sessionType = ($isAdmin)? KalturaSessionType::ADMIN : KalturaSessionType::USER; 
		try
		{
			$ks = $client->generateSession($adminSecret, $userId, $sessionType, $partnerId);
			$client->setKs($ks);
		}
		catch(Exception $ex)
		{
			throw new Exception("Could not start session - check configurations in config.ini");
		}
		
		return $client;
	}
	
	public function listActions()
	{
		$client = $this->getKalturaClient($this->config[self::CONFIG_ITEM_PARTNER_ID], $this->config[self::CONFIG_ITEM_ADMIN_SECRET], true);
		$results = $client->media->listAction();
		$entry = $results->objects[0];
		echo "\nGot an entry: [{$entry->name}]";
	}

	public function multiRequest()
	{
		$client = $this->getKalturaClient($this->config[self::CONFIG_ITEM_PARTNER_ID], $this->config[self::CONFIG_ITEM_ADMIN_SECRET], true);
		$client->startMultiRequest();
		$client->baseEntry->count();
		$client->partner->getInfo();
		$client->partner->getUsage(2011);
		$multiRequest = $client->doMultiRequest();
		$partner = $multiRequest[1];
		if(!is_object($partner) || get_class($partner) != 'KalturaPartner')
		{
			throw new Exception("UNEXPECTED_RESULT");
		}
		echo "\nGot Admin User email: [{$partner->adminEmail}]";
	}	

	public function add()
	{
		echo "\nUploading test video...";
		$client = $this->getKalturaClient($this->config[self::CONFIG_ITEM_PARTNER_ID], $this->config[self::CONFIG_ITEM_ADMIN_SECRET], false);
		$filePath = $this->config[self::CONFIG_ITEM_UPLOAD_FILE];
		
		$token = $client->baseEntry->upload($filePath);
		$entry = new KalturaMediaEntry();
		$entry->name = "my upload entry";
		$entry->mediaType = KalturaMediaType::VIDEO;
		$newEntry = $client->media->addFromUploadedFile($entry, $token);
		echo "\nUploaded a new Video entry " . $newEntry->id;
		$client->media->delete($newEntry->id);
		try {
			$entry = null;
			$entry = $client->media->get($newEntry->id);
		} catch (KalturaException $exApi) {
			if ($entry == null) {
				echo "\nDeleted the entry (" . $newEntry->id .") successfully!";
			}
		}
	}
}

TestMain::run();
