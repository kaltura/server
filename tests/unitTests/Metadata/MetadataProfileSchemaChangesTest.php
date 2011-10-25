<?php
require_once(dirname(__FILE__) . "/../../lib/KalturaClient.php");
require_once('config/config.php');

class MetadataProfileSchemaChangesTest extends PHPUnit_Framework_TestCase 
{
	private $client = null;
	private $metadatas = array(); 
	
	public function testMetadataProfileSchemaChanges() 
	{
		echo 'Metadata Profile Schema Changes Test - Start' . PHP_EOL;
		
		$this->initClient();
		
		$metadataProfile = $this->metadataProfileAdd(Config::METADATA_NAME, Config::METADATA_XSD_DATA_ADD);
		$entries = $this->mediaList();
		
		$i = 0;
		foreach ($entries as $entry)
		{
			$i++;
			if ($i > count(Config::$metadatas))
				break;
			
			$this->metadataAdd($metadataProfile->id, $entry->id, Config::$metadatas[$i][Config::ENTRY_METADATA], Config::$metadatas[$i][Config::ENTRY_METADATA_TRANSFORMED]);
		}
		
		
		$metadataProfile = $this->metadataProfileUpdate($metadataProfile->id, Config::METADATA_XSD_DATA_UPDATE);
		
		$this->metadataTransformed($metadataProfile);		
	}
	
	private function initClient()
	{
		echo 'initClient' . PHP_EOL;
		
		if ($this->client)
			return;
			
		try {
			$config = new KalturaConfiguration(Config::PARTNER_ID);
			$config->serviceUrl = Config::SERVER_URL;
			$client = new KalturaClient($config);
			$ks = $client->session->start(Config::PARTNER_ADMIN_SECRET, Config::PARTNER_USER_ID, KalturaSessionType::ADMIN, Config::PARTNER_ID);
			$client->setKs($ks);
		}
		catch (Exception $ex)
		{
			$this->assertTrue ( false, 'Exception in session->start - ' . $ex->getMessage());
		}
		
		$this->client = $client;
	}
	
	private function metadataProfileAdd($profileName, $xsdData)
	{
		echo 'metadataProfileAdd' . PHP_EOL;
		
		$metadataProfile = new KalturaMetadataProfile();
		$metadataProfile->metadataObjectType = KalturaMetadataObjectType::ENTRY;
		$metadataProfile->name = $profileName;
		$metadataProfile->createMode = KalturaMetadataProfileCreateMode::KMC;
		
		try{
			$profile = $this->client->metadataProfile->add($metadataProfile, $xsdData);
		}
		catch (Exception $ex)
		{
			$this->assertTrue ( false, 'Exception in metadataProfileAdd - metadataProfile->add - ' . $ex->getMessage());
		}
		
		echo 'metadataProfileAdd: added metadata profile: ' . $profile->id . PHP_EOL;
		
		return $profile;
	}
	
	private function mediaList()
	{
		echo 'mediaList' . PHP_EOL;
		
		try{
			$entries = $this->client->media->listAction();
		}
		catch (Exception $ex)
		{
			$this->assertTrue ( false, 'Exception in mediaList - media->listAction - ' . $ex->getMessage());
		}
		
		echo 'mediaList return [' . count($entries->objects) . '] entries' . PHP_EOL;
		
		return $entries->objects;
	}
	
	private function metadataAdd($profileId, $entryId, $entryMetadata, $entryTransformedMetadata)
	{
		echo 'metadataAdd' . PHP_EOL;
		$objectType = KalturaMetadataObjectType::ENTRY; 
		
		try{
			$metadata = $this->client->metadata->add($profileId, $objectType, $entryId, $entryMetadata);
		}
		catch (Exception $ex)
		{
			$this->assertTrue ( false, 'Exception in metadataAdd - metadata->add - ' . $ex->getMessage());
		}
		
		$this->metadatas[] = array(Config::METADATA => $metadata, Config::ENTRY_METADATA_TRANSFORMED => $entryTransformedMetadata);
	}
	
	private function metadataProfileUpdate($profileId, $xsdData)
	{
		echo 'metadataProfileUpdate' . PHP_EOL;
		$metadataProfile = new KalturaMetadataProfile();
		
		try{
			$profile = $this->client->metadataProfile->update($profileId, $metadataProfile, $xsdData);
		}
		catch (Exception $ex)
		{
			$this->assertTrue ( false, 'Exception in metadataProfileUpdate - metadataProfile->update - ' . $ex->getMessage());
		}
		
		echo 'metadataProfileUpdate - update profile id: ' . $profile->id . PHP_EOL;
		
		return $profile;
	}
	
	private function metadataTransformed($metadataProfile)
	{
		echo 'metadataTransformed' . PHP_EOL;
		
		while (count($this->metadatas))
		{
			foreach ($this->metadatas as $key => $metadata)
			{
				echo 'Metadata : ' . $metadata[Config::METADATA]->id . PHP_EOL;
				try{
					$updatedMetadata = $this->client->metadata->get($metadata[Config::METADATA]->id);
				}
				catch (Exception $ex)
				{
					$this->assertTrue ( false, 'Exception in metadataTransformed - metadata->get - ' . $ex->getMessage());
				}
				
				if ($updatedMetadata->metadataProfileVersion == $metadataProfile->version)
				{	
					$find = array ("\r\n", "\n", "\r");
					$replace = '';
					$updatedMetadata->xml = str_replace($find, $replace, $updatedMetadata->xml);
					$metadata[Config::ENTRY_METADATA_TRANSFORMED] = str_replace($find, $replace, $metadata[Config::ENTRY_METADATA_TRANSFORMED]);					
					$this->assertEquals($updatedMetadata->xml, $metadata[Config::ENTRY_METADATA_TRANSFORMED], 'Metadata was not transform as expected');
					unset($this->metadatas[$key]);
				}
			}
			echo 'sleep...' . PHP_EOL;
			sleep(10);
		}
	}
}
