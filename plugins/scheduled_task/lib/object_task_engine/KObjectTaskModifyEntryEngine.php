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
					$value = $outputMetadataItem->value;
					list($xpathName, $fieldName) = explode(",", $value);
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
					$value = $inputMetadataItem->value;
					list($xpathName, $fieldName) = explode(",", $value);

					$metadataValue = $xmlObj->$xpathName;
					$entryObj->$fieldName = $metadataValue;
				}
				
			}
			else
				KalturaLog::info("found no input metadata objects for entry $entryId");
		}

		$fieldValues = $objectTask->fieldValues;

		foreach($fieldValues as $fieldValueItem)
		{
			$value = $fieldValueItem->value;
			list($fieldValue, $fieldName) = explode(",", $value);

			$entryObj->$fieldName = $fieldValue;
		}

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
