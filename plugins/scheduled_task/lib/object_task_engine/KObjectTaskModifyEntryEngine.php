<?php

/**
 * @package plugins.scheduledTask
 * @subpackage lib.objectTaskEngine
 */
class KObjectTaskModifyEntryEngine extends KObjectTaskEntryEngineBase
{
	/**
	 * @param KalturaBaseEntry $object
	 */
	function processObject($object)
	{
		/** @var KalturaModifyEntryObjectTask $objectTask */
		$objectTask = $this->getObjectTask();
		if (is_null($objectTask))
			return;

		KBatchBase::impersonate($object->partnerId);
		$client = $this->getClient();
		$metadataPlugin = KalturaMetadataClientPlugin::get($client);
		$entryId = $object->id;
		$objectType = KalturaMetadataObjectType::ENTRY;
		
		$metadataFilter = new KalturaMetadataFilter();
		$metadataFilter->metadataObjectTypeEqual = $objectType;
		$metadataFilter->objectIdEqual = $entryId;
		
		if(isset($objectTask->outputMetadataProfileId) && $objectTask->outputMetadataProfileId != '')
		{
			$entryResultForMetadataUpdate = null;
			try
			{
				$entryResultForMetadataUpdate = $client->baseEntry->get($entryId);
			}
			catch(Exception $e)
			{
				KalturaLog::err("entry get entry id $entryId - " . $e->getMessage());
			}
			
			if ($entryResultForMetadataUpdate)
			{
				$metadataFilter->metadataProfileIdEqual = $objectTask->outputMetadataProfileId;
				$metadataId = null;
	
				try
				{
					$metadataInputResult = $metadataPlugin->metadata->listAction($metadataFilter, null);
				}
				catch(Exception $e)
				{
					KalturaLog::err("metadata list with entry id $entryId - " . $e->getMessage());
				}
	
				$outputMetadataArr = $objectTask->outputMetadata;
	
				if($metadataInputResult->totalCount != 0)
				{
					$metadataId = $metadataInputResult->objects[0]->id;
					$metadataInputXmlStr = $metadataInputResult->objects[0]->xml;
					$xmlObj = new SimpleXMLElement($metadataInputXmlStr);
				}
				else
					$xmlObj = new SimpleXMLElement("<metadata></metadata>");
	
				foreach($outputMetadataArr as $outputMetadataItem)
				{
					$xpathName = $outputMetadataItem->key;
					$fieldName = $outputMetadataItem->value;
					$xmlObj->$xpathName = $entryResultForMetadataUpdate->$fieldName;
				}
	
				$xmlData = $xmlObj->asXML();
	
				if($metadataId)
				{
					try
					{
						$metadataPlugin->metadata->update($metadataId, $xmlData, null);
					}
					catch(Exception $e)
					{
						KalturaLog::err("metadata update entry id $entryId - " . $e->getMessage());
					}
				}
				else
				{
					try
					{
						$metadataPlugin->metadata->add($objectTask->outputMetadataProfileId, $objectType, $entryId, $xmlData);
					}
					catch(Exception $e)
					{
						KalturaLog::err("metadata add entry id $entryId - " . $e->getMessage());
					}
				}
			}
		}
		
		KalturaLog::info("updating entry ". $entryId);
		$entryObj = new KalturaBaseEntry();
		$objectType = KalturaMetadataObjectType::ENTRY;

		if(isset($objectTask->inputMetadataProfileId) && $objectTask->inputMetadataProfileId != '')
		{
			$metadataFilter->metadataProfileIdEqual = $objectTask->inputMetadataProfileId;
			
			try
			{
				$metadataInputResult = $metadataPlugin->metadata->listAction($metadataFilter, null);
			}
			catch(Exception $e)
			{
				KalturaLog::err("metadata list with entry id $entryId - " . $e->getMessage());
			}

			if($metadataInputResult->totalCount != 0)
			{
				$metadataInputXmlStr = $metadataInputResult->objects[0]->xml;
				$xmlObj = new SimpleXMLElement($metadataInputXmlStr);
				$inputMetadataArr = $objectTask->inputMetadata;
				foreach($inputMetadataArr as $inputMetadataItem)
				{					
					$xpathName = $inputMetadataItem->key;
					$fieldName = $inputMetadataItem->value;
					KalturaLog::debug("ZZZZ - " . print_r($inputMetadataItem, true));
					KalturaLog::debug("YYYYY - " . print_r($xmlObj, true));
					KalturaLog::debug("XXXXX - xpathName $xpathName fieldName $fieldName");

					$metadataValue = $xmlObj->$xpathName;
					KalturaLog::debug("WWWWW - metadataValue $metadataValue");
					$entryObj->$fieldName = $metadataValue;
				}	
			}
			else
				KalturaLog::info("found no input metadata objects for entry $entryId");
		}

		if(isset($objectTask->inputUserId) && $objectTask->inputUserId != '')
			$entryObj->userId = $objectTask->inputUserId != 'null' ? $objectTask->inputUserId : null;
		
		if(isset($objectTask->inputUserId) && $objectTask->inputEntitledUsersEdit != '')
			$entryObj->entitledUsersEdit = $objectTask->inputEntitledUsersEdit != 'null' ? $objectTask->inputEntitledUsersEdit : '';
		
		if(isset($objectTask->inputEntitledUsersPublish) && $objectTask->inputEntitledUsersPublish != '')
			$entryObj->entitledUsersPublish = $objectTask->inputEntitledUsersPublish != 'null' ? $objectTask->inputEntitledUsersPublish : '';

			try
		{
			$client->baseEntry->update($entryId, $entryObj);
		}
		catch(Exception $e)
		{
			KalturaLog::err("entry update entry id $entryId - " . $e->getMessage());
		}
		KBatchBase::unimpersonate();
	}
}
